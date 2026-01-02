<?php
/**
 * @copyright Copyright (c) Miroslav Marek <mirek.marek@web-jet.cz>
 * @license EUPL 1.2  https://eupl.eu/1.2/en/
 * @author Miroslav Marek <mirek.marek@web-jet.cz>
 */
namespace JetApplicationModule\Admin\Complaints;



use Jet\AJAX;
use Jet\Form;
use Jet\Form_Field_Input;
use Jet\Tr;
use Jet\UI_messages;
use JetApplication\Complaint;
use JetApplication\Order;

class Plugin_EditMain_Main extends Plugin {
	public const KEY = 'edit_main';
	
	protected Form $form;
	
	
	public function hasDialog(): bool
	{
		return true;
	}
	
	protected function init() : void
	{
		/**
		 * @var Complaint $item
		 */
		$item = $this->item;
		
		$this->form = $item->createForm('edit_main_form', [
			'bill_number',
			'complaint_type_code',
			'delivery_of_claimed_goods_code',
			'preferred_solution_code',
			'date_started',
			'date_of_receipt_of_clained_goods',
			'date_finished'
		]);
		
		$order = new Form_Field_Input('order', 'Order number:');
		$order->setDefaultValue( $item->getOrderNumber() );
		$order->setValidator(function($order) use ( $item ) {
			$value = $order->getValue();
			
			if(!$value) {
				$bill_number = $this->form->field('bill_number')->getValue();
				if($bill_number) {
					return true;
				}
				
				$order->setError(
					Form_Field_Input::ERROR_CODE_EMPTY
				);
				
				return false;
			}
			
			$o = Order::getByNumber(
				$value,
				$item->getEshop()
			);
			if(!$o) {
				$order->setError(
					Form_Field_Input::ERROR_CODE_INVALID_VALUE
				);
				return false;
			}
			
			
			return true;
		});
		$order->setFieldValueCatcher( function( string $order_number ) use ($item) {
			if(!$order_number) {
				$item->setOrderNumber('');
				$item->setOrderId(0);
				return;
			}
			
			$order = Order::getByNumber(
				$order_number,
				$item->getEshop()
			);
			$this->item->setOrder( $order );
		} );
		$this->form->addField( $order );
		
		
		/*
		$this->form = $item->createForm('edit_main_form', [
			'eshop'
		]);
		*/
		
		$this->view->setVar('edit_main_form', $this->form);
		
	}
	
	public function canBeHandled(): bool
	{
		return true;
	}
	
	public function getForm(): Form
	{
		return $this->form;
	}
	
	public function handle() : void
	{
		/**
		 * @var Complaint $item
		 */
		$item = $this->item;
		
		if($this->form->catchInput()) {
			if($this->form->catch()) {
				$item->save();
				
				UI_messages::success( Tr::_( 'Complaint has been changed' ) );
				
				AJAX::operationResponse(true);
			} else {
				AJAX::operationResponse(false, [
					'edit-main-area' => $this->view->render('form')
				]);
			}
			
		}
	}
	
	
	
}