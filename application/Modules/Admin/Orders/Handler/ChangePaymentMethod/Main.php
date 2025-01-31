<?php
/**
 * @copyright Copyright (c) Miroslav Marek <mirek.marek@web-jet.cz>
 * @license EUPL 1.2  https://eupl.eu/1.2/en/
 * @author Miroslav Marek <mirek.marek@web-jet.cz>
 */
namespace JetApplicaTionModule\Admin\Orders;


use Jet\Form;
use Jet\Form_Field_Float;
use Jet\Form_Field_Select;
use Jet\Http_Headers;
use Jet\Tr;
use Jet\UI_messages;
use JetApplication\Payment_Method;
use JetApplication\Payment_Method_EShopData;

class Handler_ChangePaymentMethod_Main extends Handler
{
	public const KEY = 'change_payment_method';
	
	protected Form $form;
	
	protected bool $has_dialog = true;
	
	protected function init() : void
	{
		$payment_methods = [];
		foreach(Payment_Method::getList() as $pm) {
			
			if($pm->getOptions()) {
				foreach($pm->getOptions() as $option) {
					$payment_methods[$pm->getId().':'.$option->getInternalCode()] = $pm->getInternalName().' - '.$option->getInternalName();
				}
			} else {
				$payment_methods[$pm->getId()] = $pm->getInternalName();
			}
		}
		
		$selected = $this->order->getPaymentMethodId();
		if($this->order->getPaymentMethodSpecification()) {
			$selected .= ':'.$this->order->getPaymentMethodSpecification();
		}
		
		$payment_method = new Form_Field_Select('payment_method', 'Payment method:' );
		$payment_method->setDefaultValue( $selected );
		$payment_method->setSelectOptions( $payment_methods );
		$payment_method->setErrorMessages([
			Form_Field_Select::ERROR_CODE_INVALID_VALUE => 'Invalid value'
		]);
		
		
		
		$fee = new Form_Field_Float('fee', 'Fee:');
		$fee->setDefaultValue( $this->order->getPaymentAmount_WithVAT() );
		
		
		$this->form = new Form('change_payment_method_form', [
			$payment_method,
			$fee
		]);
		
		$this->view->setVar('change_payment_method_form', $this->form);
		
	}
	
	public function handle() : void
	{
		
		if( $this->form->catch() ) {
			
			$selected_pm = $this->form->field('payment_method')->getValue();
			$specification = '';
			
			if(str_contains($selected_pm,':')) {
				[$selected_pm,$specification] = explode(':', $selected_pm);
			}
			
			$payment_method = Payment_Method_EShopData::get( (int)$selected_pm, $this->order->getEshop() );
			
			if($payment_method) {
				$change = $this->order->changePaymentMethod(
					$payment_method,
					$specification,
					$this->form->field('fee')->getValue()
				);
				
				if($change->hasChange()) {
					UI_messages::success(Tr::_('Payment method has been changed'));
					$this->order->save();
					$change->save();
				}
			}
			
			
			Http_Headers::reload();
		}
		
		
	}
}