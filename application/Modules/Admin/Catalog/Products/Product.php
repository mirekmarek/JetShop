<?php
namespace JetApplicationModule\Admin\Catalog\Products;

use Jet\DataModel_Definition;
use Jet\Form;
use Jet\Form_Field_Checkbox;
use Jet\Form_Field_Select;
use Jet\Tr;
use JetApplication\Admin_FulltextSearch_IndexDataProvider;
use JetApplication\Admin_Managers;
use JetApplication\Shops;
use Jet\DataModel_Fetch_Instances;
use JetApplication\Admin_Entity_Interface;
use JetApplication\Admin_Entity_Trait;

use JetApplication\Product as Application_Product;
use JetApplication\Sticker;

#[DataModel_Definition(
	force_class_name: Application_Product::class
)]
class Product extends Application_Product implements Admin_Entity_Interface, Admin_FulltextSearch_IndexDataProvider {
	use Admin_Entity_Trait;
	

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
		$form->field('brand_id')->setErrorMessages([
			Form_Field_Select::ERROR_CODE_INVALID_VALUE => 'Please select value'
		]);
		$form->field('supplier_id')->setErrorMessages([
			Form_Field_Select::ERROR_CODE_INVALID_VALUE => 'Please select value'
		]);
		
		$this->_setupForm_stickers( $form );
		$this->_setupForm_set( $form );
		$this->_setupForm_variants( $form );
		
		
		
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
			$vat_rate->setSelectOptions( Shops::getVatRatesScope( $shop ) );
			$vat_rate->setIsReadonly( $form->field('/shop_data/'.$shop_key.'/vat_rate')->getIsReadonly() );
			
			
			$form->removeField('/shop_data/'.$shop_key.'/vat_rate');
			$form->addField( $vat_rate );
			
			
		}
		
	}
	
	protected function _setupForm_stickers( Form $form ) : void
	{
		foreach(Sticker::getScope() as $s_code=>$sticker_internal_name) {
			
			$field = new Form_Field_Checkbox('/sticker/'.$s_code, $sticker_internal_name );
			$field->setDefaultValue( isset($this->stickers[$s_code]) );
			$field->setFieldValueCatcher( function( $value ) {
				if($value) {
					$this->addSticker($value);
				} else {
					$this->removeSticker($value);
				}
			} );
			
			$form->addField( $field );
		}
		
	}
	
	protected function _setupForm_set( Form $form ) : void
	{
	}
	
	
	protected function _setupForm_variants( Form $form ) : void
	{
		if($this->type!=static::PRODUCT_TYPE_VARIANT) {
			return;
		}
		
		$this->_edit_form->field('brand_id')->setIsReadonly(true);
		$this->_edit_form->field('supplier_id')->setIsReadonly(true);
		
		foreach(Shops::getList() as $shop) {
			$shop_key = $shop->getKey();
			
			$this->_edit_form->field('/shop_data/'.$shop_key.'/vat_rate')->setIsReadonly(true);
		}
		
		
		foreach(Shops::getList() as $shop) {
			$shop_key = $shop->getKey();
			
			$this->_edit_form->field('/shop_data/'.$shop_key.'/name')->setIsReadonly(true);
			$this->_edit_form->field('/shop_data/'.$shop_key.'/description')->setIsReadonly(true);
			$this->_edit_form->field('/shop_data/'.$shop_key.'/short_description')->setIsReadonly(true);
			$this->_edit_form->field('/shop_data/'.$shop_key.'/seo_title')->setIsReadonly(true);
			$this->_edit_form->field('/shop_data/'.$shop_key.'/seo_h1')->setIsReadonly(true);
			$this->_edit_form->field('/shop_data/'.$shop_key.'/seo_description')->setIsReadonly(true);
			$this->_edit_form->field('/shop_data/'.$shop_key.'/seo_keywords')->setIsReadonly(true);
			$this->_edit_form->field('/shop_data/'.$shop_key.'/internal_fulltext_keywords')->setIsReadonly(true);
		}
		
		foreach(Sticker::getScope() as $sticker_code=>$sticker_internal_name) {
			$this->_edit_form->field('/sticker/'.$sticker_code)->setIsReadonly(true);
		}
		
		
	}
	
	
	public function getAddForm() : Form
	{
		if(!$this->_add_form) {
			$this->_add_form = $this->createForm('add_form');
			
			$this->_setupForm( $this->_add_form );
			
			foreach(Shops::getList() as $shop) {
				$this->_add_form->field( '/shop_data/' . $shop->getKey() . '/vat_rate' )->setDefaultValue( Shops::getDefaultVatRate( $shop ) );
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
		$this->actualizeSetItem();
		$this->actualizeVariant();
		
		Admin_Managers::FulltextSearch()->updateIndex( $this );
	}
	
	public function afterDelete() : void
	{
		$this->actualizeSetItem();
		$this->actualizeVariant();
		
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
		
		return $this->internal_name.$codes;
	}
	
	public function renderActiveState() : string
	{
		return Admin_Managers::Product()->renderActiveState( $this );
	}
	
	public function getEditURL() : string
	{
		return Main::getEditUrl( $this->id );
	}
	
	public function getAdminFulltextObjectClass(): string
	{
		return static::getEntityType();
	}
	
	public function getAdminFulltextObjectId(): string
	{
		return $this->id;
	}
	
	public function getAdminFulltextObjectType(): string
	{
		return $this->getType();
	}
	
	public function getAdminFulltextObjectIsActive(): bool
	{
		return $this->isActive();
	}
	
	public function getAdminFulltextObjectTitle(): string
	{
		return $this->getAdminTitle();
	}
	
	public function getAdminFulltextTexts(): array
	{
		return [$this->internal_name, $this->internal_code, $this->ean];
	}
	
}