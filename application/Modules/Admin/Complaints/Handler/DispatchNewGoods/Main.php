<?php
/**
 *
 * @copyright
 * @license
 * @author
 */
namespace JetApplicationModule\Admin\Complaints;


use Jet\AJAX;
use Jet\Form;
use Jet\Form_Field_Hidden;
use Jet\Form_Field_Input;
use Jet\Form_Field_Select;
use Jet\Form_Field_Textarea;
use JetApplication\Delivery_Method_ShopData;
use JetApplication\Order;

class Handler_DispatchNewGoods_Main extends Handler {
	public const KEY = 'dispatch_new_goods';
	
	protected Form $form;
	protected bool $has_dialog = true;
	
	protected function init() : void
	{
		$comment = new Form_Field_Textarea('comment', 'Comments:');
		
		$product = new Form_Field_Hidden('product_id', '');
		$product->setDefaultValue( $this->complaint->getProductId() );
		
		$order = Order::get( $this->complaint->getOrderId() );
		
		$delivery_method = new Form_Field_Select('delivery_method', 'Delivery method:');
		$delivery_method->setDefaultValue( $order->getDeliveryMethodId() );
		$options = [];
		foreach( Delivery_Method_ShopData::getAllActive( $order->getShop() ) as $dm ) {
			$options[$dm->getId()] = $dm->getTitle();
		}
		$delivery_method->setSelectOptions( $options );
		
		$delivery_point_code = new Form_Field_Input('delivery_point_code', 'Delivery point code:');
		$delivery_point_code->setDefaultValue( $order->getDeliveryPersonalTakeoverDeliveryPointCode() );
		$delivery_point_code->setErrorMessages([
			Form_Field_Input::ERROR_CODE_EMPTY => 'Please enter delivery point code',
			'unknown' => 'Unknown delivery point'
		]);
		$delivery_point_code->setValidator( function() use ($delivery_point_code, $delivery_method, $order) {
			$_delivery_point_code = $delivery_point_code->getValue();
			$_delivery_method = Delivery_Method_ShopData::get( $delivery_method->getValue(), $order->getShop() );
			if(!$_delivery_method->isPersonalTakeover()) {
				return true;
			}
			
			if( !$_delivery_point_code ) {
				$delivery_point_code->setError( Form_Field_Input::ERROR_CODE_EMPTY );
				return false;
			}
			
			if( !$_delivery_method->getPersonalTakeoverDeliveryPoint( $_delivery_point_code ) ) {
				$delivery_point_code->setError( 'unknown' );
				return false;
			}
			
			return true;
		} );
		
		
		$this->form = new Form('dispatch_new_goods_form', [
			$product,
			$delivery_method,
			$delivery_point_code,
			$comment,
		]);
		$this->view->setVar('dispatch_new_goods_form', $this->form);
	}
	
	public function handle() : void
	{
		if($this->form->catchInput()) {
			if($this->form->validate()) {

				$this->complaint->newProductDispatched(
					$this->form->field('product_id')->getValue(),
					$this->form->field('delivery_method')->getValue(),
					$this->form->field('delivery_point_code')->getValue(),
					$this->form->field('comment')->getValue()
				);
				
				AJAX::operationResponse(
					true
				);
			} else {
				AJAX::operationResponse(
					false,
					snippets: [
						'dispatch_new_goods_form_area' => $this->view->render('form')
					]
				);
			}
		}
	}
}