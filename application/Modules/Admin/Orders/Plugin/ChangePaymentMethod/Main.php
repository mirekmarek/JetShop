<?php
/**
 * @copyright Copyright (c) Miroslav Marek <mirek.marek@web-jet.cz>
 * @license EUPL 1.2  https://eupl.eu/1.2/en/
 * @author Miroslav Marek <mirek.marek@web-jet.cz>
 */
namespace JetApplicationModule\Admin\Orders;


use Jet\Form;
use Jet\Form_Field_Float;
use Jet\Form_Field_Select;
use Jet\Http_Headers;
use Jet\Tr;
use Jet\UI_messages;
use JetApplication\Order;
use JetApplication\Payment_Method;

class Plugin_ChangePaymentMethod_Main extends Plugin
{
	public const KEY = 'change_payment_method';
	
	protected Form $form;
	
	public function hasDialog(): bool
	{
		return true;
	}
	
	protected function init() : void
	{
		/**
		 * @var Order $item
		 */
		$item = $this->item;
		
		$payment_methods = [];
		foreach(Payment_Method::getListForEShop( $item->getEshop() ) as $pm) {
			
			if($pm->getOptions()) {
				foreach($pm->getOptions() as $option) {
					$payment_methods[$pm->getId().':'.$option->getInternalCode()] = $pm->getInternalName().' - '.$option->getInternalName();
				}
			} else {
				$payment_methods[$pm->getId()] = $pm->getInternalName();
			}
		}
		
		$selected = $this->item->getPaymentMethodId();
		if($this->item->getPaymentMethodSpecification()) {
			$selected .= ':'.$this->item->getPaymentMethodSpecification();
		}
		
		$payment_method = new Form_Field_Select('payment_method', 'Payment method:' );
		$payment_method->setDefaultValue( $selected );
		$payment_method->setSelectOptions( $payment_methods );
		$payment_method->setErrorMessages([
			Form_Field_Select::ERROR_CODE_INVALID_VALUE => 'Invalid value'
		]);
		
		
		
		$fee = new Form_Field_Float('fee', 'Fee:');
		$fee->setDefaultValue( $this->item->getPaymentAmount_WithVAT() );
		
		
		$this->form = new Form('change_payment_method_form', [
			$payment_method,
			$fee
		]);
		
		$this->view->setVar('change_payment_method_form', $this->form);
		
	}
	
	public function handle() : void
	{
		/**
		 * @var Order $item
		 */
		$item = $this->item;
		
		if( $this->form->catch() ) {
			
			$selected_pm = $this->form->field('payment_method')->getValue();
			$specification = '';
			
			if(str_contains($selected_pm,':')) {
				[$selected_pm,$specification] = explode(':', $selected_pm);
			}
			
			$payment_method = Payment_Method::get( (int)$selected_pm );
			
			if($payment_method) {
				$change = $item->changePaymentMethod(
					$payment_method,
					$specification,
					$this->form->field('fee')->getValue()
				);
				
				if($change->hasChange()) {
					UI_messages::success(Tr::_('Payment method has been changed'));
					$this->item->save();
					$change->save();
				}
			}
			
			
			Http_Headers::reload();
		}
		
		
	}
}