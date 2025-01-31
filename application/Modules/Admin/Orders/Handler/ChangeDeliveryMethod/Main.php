<?php
/**
 * @copyright Copyright (c) Miroslav Marek <mirek.marek@web-jet.cz>
 * @license EUPL 1.2  https://eupl.eu/1.2/en/
 * @author Miroslav Marek <mirek.marek@web-jet.cz>
 */
namespace JetApplicationModule\Admin\Orders;


use Jet\AJAX;
use Jet\Form;
use Jet\Form_Field_Float;
use Jet\Form_Field_Input;
use Jet\Form_Field_Select;
use Jet\Http_Headers;
use Jet\MVC_View;
use Jet\Tr;
use Jet\UI_messages;
use JetApplication\Delivery_Method;
use JetApplication\Delivery_Method_EShopData;
use JetApplication\Order;

class Handler_ChangeDeliveryMethod_Main extends Handler
{
	public const KEY = 'change_delivery_method';
	
	protected bool $has_dialog = true;
	
	protected MVC_View $view;
	protected Order $order;
	protected Form $form;
	
	
	protected function init() : void
	{
		
		$delivery_method = new Form_Field_Select('delivery_method', 'Payment method:' );
		$delivery_method->setDefaultValue( $this->order->getDeliveryMethodId() );
		$delivery_method->setSelectOptions( Delivery_Method::getScope() );
		$delivery_method->setErrorMessages([
			Form_Field_Select::ERROR_CODE_INVALID_VALUE => 'Invalid value'
		]);
		
		$pto_point = new Form_Field_Input('pto_delivery_point_code', 'Personal takeover delivery point code:');
		$pto_point->setErrorMessages([
			'missing' => Tr::_('Please select delivery point'),
			'unknown-place' => Tr::_('Unknown delivery point')
		]);
		$pto_point->setDefaultValue( $this->order->getDeliveryPersonalTakeoverDeliveryPointCode() );
		$pto_point->setValidator( function() use ($pto_point, $delivery_method) {
			$delivery_method_id = $delivery_method->getValue();
			$dm = Delivery_Method_EShopData::get( $delivery_method_id, $this->order->getEshop() );
			$place_code = $pto_point->getValue();
			
			if($dm->isPersonalTakeover()) {
				if(!$place_code) {
					$pto_point->setError('missing');
					return false;
				}
				
				if(!$dm->hasPersonalTakeoverDeliveryPoint( $place_code )) {
					$pto_point->setError('unknown-place');
					
					return false;
				}
				
				return true;
			}
			
			$pto_point->setValue('');
			
			return true;
		} );
		
		
		$fee = new Form_Field_Float('fee', 'Fee:');
		$fee->setDefaultValue( $this->order->getDeliveryAmount_WithVAT() );
		
		
		$this->form = new Form('change_delivery_method_form', [
			$delivery_method,
			$pto_point,
			$fee
		]);
		
		$this->view->setVar('change_delivery_method_form', $this->form);
		
	}
	
	public function handle() : void
	{
		
		if( $this->form->catchInput() ) {
			
			if($this->form->validate()) {
				$delivery_method = Delivery_Method_EShopData::get(
					$this->form->field('delivery_method')->getValue(),
					$this->order->getEshop()
				);
				
				if($delivery_method) {
					$change = $this->order->changeDeliveryMethod(
						$delivery_method,
						$this->form->field('pto_delivery_point_code')->getValue(),
						$this->form->field('fee')->getValue()
					);
					
					if($change->hasChange()) {
						UI_messages::success(Tr::_('Delivery method has been changed'));
						$this->order->save();
						$change->save();
					}
				}
				
				AJAX::operationResponse(true);
			} else {
				AJAX::operationResponse(false, [
					'change-delivery-method-form' => $this->view->render('form')
				]);
			}
			
			Http_Headers::reload();
		}
		
		
	}
}