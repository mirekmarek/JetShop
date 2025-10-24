<?php
/**
 * @copyright Copyright (c) Miroslav Marek <mirek.marek@web-jet.cz>
 * @license EUPL 1.2  https://eupl.eu/1.2/en/
 * @author Miroslav Marek <mirek.marek@web-jet.cz>
 */
namespace JetApplicationModule\Admin\Orders;


use Jet\Http_Headers;
use Jet\Http_Request;
use JetApplication\Order;
use JetApplication\Order_Status_HandedOver;
use JetApplication\Order_Status_PersonalReceiptPrepared;

class Plugin_PersonalPickup_Main extends Plugin {
	public const KEY = 'parsonal-pickup';
	
	public function hasDialog(): bool
	{
		return false;
	}
	
	
	protected function init() : void
	{
	}
	
	public function handleOnlyIfItemIsEditable() : bool
	{
		return false;
	}
	
	public function handle() : void
	{
		/**
		 * @var Order $order
		 */
		$order = $this->item;
		if(
			!$order->getDeliveryMethod()?->getKind()?->isPersonalTakeoverInternal() ||
			$order->getDelivered()
		) {
			return;
		}
		
		
		switch(Http_Request::GET()->getString('pp_action')) {
			case 'ready_to_head_over':
				$order->setStatus( Order_Status_PersonalReceiptPrepared::get() );
				Http_Headers::reload(unset_GET_params: ['pp_action']);
				break;
			case 'picked_up_by_customer':
				$order->setStatus( Order_Status_HandedOver::get() );
				Http_Headers::reload(unset_GET_params: ['pp_action']);
				break;
		}
	}
}