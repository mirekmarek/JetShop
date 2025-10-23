<?php
/**
 * @copyright Copyright (c) Miroslav Marek <mirek.marek@web-jet.cz>
 * @license EUPL 1.2  https://eupl.eu/1.2/en/
 * @author Miroslav Marek <mirek.marek@web-jet.cz>
 */
namespace JetApplicationModule\Admin\ReturnsOfGoods;



use Jet\Form;
use Jet\Form_Field_Float;
use Jet\Form_Field_Textarea;
use Jet\Http_Headers;
use Jet\Tr;
use Jet\UI_messages;
use JetApplication\MoneyRefund;
use JetApplication\Order;use JetApplication\ReturnOfGoods;

class Plugin_MoneyRefund_Main extends Plugin {
	public const KEY = 'money_refund';
	
	protected Form $form;
	
	public function hasDialog(): bool
	{
		return true;
	}
	
	
	protected function init() : void
	{
		/**
		 * @var ReturnOfGoods $item
		 */
		$item = $this->item;
		
		$internal_summary = new Form_Field_Textarea('internal_summary', 'Internal Summary:');
		$amount_to_be_refunded = new Form_Field_Float('amount_to_be_refunded', 'Amount to be refunded:');
		
		$product_price = 0.0;
		$order = Order::get( $item->getOrderId() );
		if($order) {
			foreach($order->getItems() as $order_item){
				if(
					$order_item->isPhysicalProduct() &&
					$item->getProductId()==$order_item->getItemId()
				) {
					$product_price = $order_item->getPricePerUnit_WithVat();
				}
			}
		}
		
		$amount_to_be_refunded->setMaxValue( $product_price );
		$amount_to_be_refunded->setIsRequired( true );
		$amount_to_be_refunded->setDefaultValue( $product_price );
		
		$this->form = new Form('money_refund_form', [ $internal_summary, $amount_to_be_refunded ]);
		$this->view->setVar('money_refund_form', $this->form);
	}
	
	public function handle() : void
	{
		/**
		 * @var ReturnOfGoods $item
		 */
		$item = $this->item;
		
		if($this->form->catch()) {
			$refund = new MoneyRefund();
			$refund->setOrder( Order::get( $item->getOrderId() ) );
			$refund->setContext( $item->getProvidesContext() );
			
			$refund->setAmountToBeRefunded( $this->form->getField('amount_to_be_refunded')->getValue() );
			$refund->setInternalSummary( $this->form->getField('internal_summary')->getValue() );
			
			$refund->save();
			
			UI_messages::success(Tr::_('Money refundation request has been created'));
			Http_Headers::reload();
		}
	}
}