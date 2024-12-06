<?php
/**
 *
 * @copyright
 * @license
 * @author
 */
namespace JetApplicationModule\Admin\Payment\Methods;

use Jet\Application_Modules;
use Jet\Form;
use Jet\Form_Field_Float;
use Jet\Form_Field_Select;
use Jet\Tr;
use JetApplication\Admin_Entity_WithEShopData_Interface;
use JetApplication\Admin_Entity_WithEShopData_Trait;
use JetApplication\Payment_Method;
use JetApplication\Payment_Method_Module;
use JetApplication\Pricelists;

class PaymentMethod extends Payment_Method implements Admin_Entity_WithEShopData_Interface
{
	use Admin_Entity_WithEShopData_Trait;
	
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
			image_title:  Tr::_('Icon 3'),
		);
		$this->defineImage(
			image_class:  'icon3',
			image_title:  Tr::_('Icon 3'),
		);
		
	}
	
	public function getAddForm(): Form
	{
		if(!$this->_add_form) {
			$this->_add_form = $this->createForm('add_form');
	
		}
		
		return $this->_add_form;
	}
	
	
	/**
	 * @return PaymentMethod_Option[]
	 */
	public function getOptions() : array
	{
		if($this->options===null) {
			$this->options = PaymentMethod_Option::getListForMethod( $this->id );
		}
		
		return $this->options;
	}
	
	public function setupEditForm( Form $form ): void
	{
		$module_name = $form->field('backend_module_name')->getValueRaw();
		
		if($module_name) {
			/**
			 * @var Payment_Method_Module $module
			 */
			if(Application_Modules::moduleExists($module_name)) {
				$module = Application_Modules::moduleInstance( $module_name );
				$specification = $module->getPaymentMethodSpecificationList();
				
				/**
				 * @var Form_Field_Select $options
				 */
				$options = $form->getField('backend_module_payment_method_specification');
				$options->setSelectOptions( $specification );
			}
		} else {
			/**
			 * @var Form_Field_Select $spec
			 */
			$spec = $form->getField('backend_module_payment_method_specification');
			$spec->setIsRequired(false);
			$spec->setSelectOptions([''=>'']);
		}
		
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