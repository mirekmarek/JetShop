<?php
namespace JetApplicationModule\Admin\Catalog\Products;

use Jet\DataModel_Definition;
use Jet\Form;
use Jet\Form_Field;
use Jet\Form_Field_Input;
use Jet\Form_Field_Select;
use Jet\Tr;
use JetApplication\Admin_Managers;
use JetApplication\KindOfProduct;
use JetApplication\Shops;
use Jet\DataModel_Fetch_Instances;
use JetApplication\Admin_Entity_WithShopData_Interface;
use JetApplication\Admin_Entity_WithShopData_Trait;

use JetApplication\Product as Application_Product;

#[DataModel_Definition]
class Product extends Application_Product implements Admin_Entity_WithShopData_Interface {
	use Admin_Entity_WithShopData_Trait;
	
	use Product_Parameters;
	use Product_Variants;
	use Product_Set;
	

	public static function getProductTypes() : array
	{
		return [
			static::PRODUCT_TYPE_REGULAR        => Tr::_('Regular'),
			static::PRODUCT_TYPE_VARIANT_MASTER => Tr::_('Variant master'),
			static::PRODUCT_TYPE_VARIANT        => Tr::_('Variant'),
			static::PRODUCT_TYPE_SET            => Tr::_('Set'),
		];
		
	}
	
	
	public function defineImages(): void
	{
	}
	
	/**
	 *
	 * @return DataModel_Fetch_Instances|static[]
	 */
	public static function getList() : DataModel_Fetch_Instances|iterable
	{
		$list = static::fetchInstances();
		
		return $list;
	}
	
	protected function _setupForm( Form $form ) : void
	{
		
		foreach(Shops::getList() as $shop) {
			$shop_key = $shop->getKey();
			
			$vat_rate = new Form_Field_Select('/shop_data/'.$shop_key.'/vat_rate', 'VAT rate:' );
			$vat_rate->setDefaultValue( $this->getShopData($shop)->getVatRate() );
			
			$vat_rate->setErrorMessages([
				Form_Field_Select::ERROR_CODE_INVALID_VALUE => 'Invalid date'
			]);
			$vat_rate->setFieldValueCatcher(function( $value ) use ($shop) {
				$this->getShopData($shop)->setVatRate( $value );
			});
			$vat_rate->setSelectOptions( $shop->getVatRatesScope() );
			$vat_rate->setIsReadonly( $form->field('/shop_data/'.$shop_key.'/vat_rate')->getIsReadonly() );
			
			
			$form->removeField('/shop_data/'.$shop_key.'/vat_rate');
			$form->addField( $vat_rate );
			
			
		}
		
		$this->_setupForm_regular( $form );
		$this->_setupForm_set( $form );
		$this->_setupForm_variant( $form );
		$this->_setupForm_variantMaster( $form );
		
	}
	
	protected function _setupForm_regular( Form $form ) : void
	{
		if(!$this->isRegular()) {
			return;
		}
		$form->removeField('internal_name_of_variant');
		
		
		foreach( Shops::getList() as $shop ) {
			$form->field('/shop_data/'.$shop->getKey().'/standard_price')->setIsReadonly( true );
			$form->field('/shop_data/'.$shop->getKey().'/price')->setIsReadonly( true );
			
			$form->removeField( '/shop_data/'.$shop->getKey().'/variant_name' );
		}
	}
	
	
	
	
	public function getAddForm() : Form
	{
		if(!$this->_add_form) {
			$this->_add_form = $this->createForm('add_form');
			
			$kind_id_field = new Form_Field_Input( 'kind_of_product_id', 'Kind of product:' );
			$kind_id_field->setDefaultValue( $this->kind_id );
			$kind_id_field->setErrorMessages([
				Form_Field::ERROR_CODE_EMPTY => 'Please select kind of product',
			]);
			$kind_id_field->setFieldValueCatcher( function( $value ) {
				$this->setKindId( $value );
			} );
			$kind_id_field->setValidator( function() use ($kind_id_field) {
				$kind_id = $kind_id_field->getValue();
				if(
					!$kind_id ||
					!KindOfProduct::exists( $kind_id )
				) {
					$kind_id_field->setError(Form_Field::ERROR_CODE_EMPTY);
					return false;
				}
				
				
				return true;
			} );
			
			
			$this->_add_form->addField( $kind_id_field );
			
			$this->_setupForm( $this->_add_form );
			
			foreach(Shops::getList() as $shop) {
				$this->_add_form->field( '/shop_data/' . $shop->getKey() . '/vat_rate' )->setDefaultValue( $shop->getDefaultVatRate() );
			}
		}
		
		return $this->_add_form;
	}
	
	public function catchAddForm() : bool
	{
		$add_form = $this->getAddForm();
		if( !$add_form->catch() ) {
			return false;
		}
		
		return true;
	}
	
	
	public function getEditForm() : Form
	{
		if(!$this->_edit_form) {
			$this->_edit_form = $this->createForm('edit_form');
			
			$this->_setupForm( $this->_edit_form );
		}
		
		return $this->_edit_form;
	}
	
	public function catchEditForm() : bool
	{
		$edit_form = $this->getEditForm();
		if( !$edit_form->catch() ) {
			return false;
		}
		
		return true;
	}
	
	
	
	
	
	public function afterAdd() : void
	{
		foreach( Shops::getList() as $shop ) {
			$shop_key = $shop->getKey();
			
			$this->shop_data[$shop_key]->generateURLPathPart();
			$this->shop_data[$shop_key]->save();
		}
		
		Admin_Managers::FulltextSearch()->addIndex( $this );
		
	}
	
	public function afterUpdate() : void
	{
		Admin_Managers::FulltextSearch()->updateIndex( $this );
		
		switch($this->getType()) {
			case Product::PRODUCT_TYPE_REGULAR:         $this->actualizeSetItem(); break;
			//case Product::PRODUCT_TYPE_VARIANT:         $this->actualizeVariant(); break;
			case Product::PRODUCT_TYPE_VARIANT_MASTER:  $this->actualizeVariantMaster(); break;
			case Product::PRODUCT_TYPE_SET:             $this->actualizeSet(); break;
		}
	}
	
	public function afterDelete() : void
	{
		Admin_Managers::FulltextSearch()->deleteIndex( $this );
	}
	
	
	public function getAdminTitle() : string
	{
		$codes = [];
		if($this->getInternalCode()) {
			$codes[] = $this->getInternalCode();
		}
		if($this->getEan()) {
			$codes[] = $this->getEan();
		}
		
		if($codes) {
			$codes = ' ('.implode(', ', $codes).')';
		} else {
			$codes = '';
		}
		
		$internal_name = $this->internal_name;
		
		if($this->type==static::PRODUCT_TYPE_VARIANT) {
			$internal_name.= ' / '.$this->getShopData()?->getVariantName();
		}
		
		if($this->internal_name_of_variant) {
			$internal_name .= ' - '.$this->internal_name_of_variant;
		}
		
		return $internal_name.$codes;
	}
	
	public function renderActiveState() : string
	{
		return Admin_Managers::Product()->renderActiveState( $this );
	}
	
	public function getEditURL() : string
	{
		return Main::getEditUrl( $this->id );
	}
	
	public function getAdminFulltextObjectType(): string
	{
		return $this->getType();
	}

	
	public function getAdminFulltextTexts(): array
	{
		return [$this->internal_name, $this->internal_code, $this->ean, $this->internal_name_of_variant];
	}
	
	public function getVariantMasterProduct() : ?Product
	{
		return static::get( $this->variant_master_product_id );
	}
	
	
	
}