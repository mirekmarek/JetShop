<?php
/**
 *
 * @copyright
 * @license
 * @author
 */
namespace JetApplicationModule\Admin\Delivery\Methods;

use Jet\DataModel_Definition;
use Jet\Form;
use Jet\Form_Field_Float;
use Jet\Form_Field_Select;
use Jet\Tr;
use JetApplication\Admin_Entity_WithShopData_Interface;
use JetApplication\Admin_Entity_WithShopData_Trait;
use JetApplication\Carrier;
use JetApplication\Delivery_Method;
use JetApplication\Pricelists;
use JetApplication\Shops;


#[DataModel_Definition]
class DeliveryMethod extends Delivery_Method implements Admin_Entity_WithShopData_Interface
{
	use Admin_Entity_WithShopData_Trait;
	
	protected ?Form $set_price_form = null;
	
	
	public function getEditURL() : string
	{
		return Main::getEditURL( $this->id );
	}
	
	public function defineImages() : void
	{
		$this->defineImage(
			image_class:  'icon1',
			image_title:  Tr::_('Icon 1'),
		);
		
		$this->defineImage(
			image_class:  'icon2',
			image_title:  Tr::_('Icon 2'),
		);
		
		$this->defineImage(
			image_class:  'icon3',
			image_title:  Tr::_('Icon 3'),
		);
		
	}
	
	public function setupEditForm( Form $form ): void
	{
		$carrier_code = $form->field('carrier_code')->getValueRaw();
		$services = [''=>''];
		$delivery_point_types = [];
		
		if($carrier_code) {
			$carrier = Carrier::get( $carrier_code );

			foreach($carrier->getServicesList() as $k=>$v) {
				$services[$k] = $v;
			}
			foreach($carrier->getDeliveryPointTypeOptions() as $k=>$v) {
				$delivery_point_types[$k] = $v;
			}
		}
		
		/**
		 * @var Form_Field_Select $services_field
		 * @var Form_Field_Select $dp_type_field
		 */
		foreach(Shops::getList() as $shop) {
			$services_field = $form->getField('/shop_data/'.$shop->getKey().'/carrier_service_code');
			$services_field->setIsRequired( false );
			$services_field->setSelectOptions( $services );
			
			$dp_type_field = $form->getField('/shop_data/'.$shop->getKey().'/allowed_delivery_point_types');
			$dp_type_field->setIsRequired( false );
			$dp_type_field->setSelectOptions( $delivery_point_types );
			
		}
	}
	
	
	public function getAddForm(): Form
	{
		if(!$this->_add_form) {
			$this->_add_form = $this->createForm('add_form');
		}
		
		return $this->_add_form;
	}
	
	public function catchAddForm(): bool
	{
		$form = $this->getAddForm();
		if(!$form->catchInput()) {
			return false;
		}
		
		$this->setupEditForm( $form );
		
		if(!$form->validate()) {
			return false;
		}
		
		$form->catch();
		
		return true;
	}
	
	public function catchEditForm(): bool
	{
		$form = $this->getEditForm();
		if(!$form->catchInput()) {
			return false;
		}
		
		$this->setupEditForm( $form );
		
		if(!$form->validate()) {
			return false;
		}
		
		$form->catch();
		
		return true;
	}
	
	
	public function getSetPriceForm() : ?Form
	{
		if( !Main::getCurrentUserCanSetPrice() ) {
			return null;
		}
		
		if(!$this->set_price_form) {
			$this->set_price_form = new Form('set_price_form', []);
			
			
			foreach(Pricelists::getList() as $pricelist) {
				
				$field_name_prefix = '/'.$pricelist->getCode().'/';
				
				$vat_rate = new Form_Field_Select( $field_name_prefix.'vat_rate', 'VAT rate:' );
				$vat_rate->setDefaultValue( $this->getVatRate( $pricelist ) );
				
				$vat_rate->setErrorMessages([
					Form_Field_Select::ERROR_CODE_INVALID_VALUE => 'Invalid value'
				]);
				$vat_rate->setFieldValueCatcher(function( $value ) use ($pricelist) {
					$p = $this->getPriceEntity($pricelist);
					$p->setVatRate($value);
					$p->save();
				});
				$vat_rate->setSelectOptions( $pricelist->getVatRatesScope() );
				$this->set_price_form->addField( $vat_rate );
				
				
				$price = new Form_Field_Float($field_name_prefix.'default_price', 'Price:');
				$price->setDefaultValue( $this->getPrice( $pricelist ) );
				$price->setFieldValueCatcher(function( $value ) use ($pricelist) {
					$p = $this->getPriceEntity($pricelist);
					$p->setPrice($value);
					$p->save();
				});
				
				$this->set_price_form->addField( $price );
				
				
			}
			
		}
		
		return $this->set_price_form;
	}
	
	
}