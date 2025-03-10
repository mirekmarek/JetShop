<?php
/**
 * @copyright Copyright (c) Miroslav Marek <mirek.marek@web-jet.cz>
 * @license EUPL 1.2  https://eupl.eu/1.2/en/
 * @author Miroslav Marek <mirek.marek@web-jet.cz>
 */
namespace JetApplicationModule\Admin\Orders;



use Jet\AJAX;
use Jet\Http_Request;
use JetApplication\Order;


class Plugin_UpdateItemQty_Main extends Plugin {
	
	public const KEY = 'delete_item';
	
	
	public function hasDialog(): bool
	{
		return true;
	}
	
	protected function init() : void
	{
	}
	
	public function handle() : void
	{
		if(!Http_Request::GET()->exists('update_item_qty')) {
			return;
		}
		
		/**
		 * @var Order $order
		 */
		$order = $this->item;
		
		
		$POST = Http_Request::POST();
		$new_qty = $POST->getRawData();
		
		
		$items = [];
		foreach( $this->item->getItems() as $item) {
			if(!$POST->exists('/new_qty/'.$item->getId())) {
				continue;
			}
			
			$items[$item->getId()] = $POST->getFloat('/new_qty/'.$item->getId());
		}
		
		if($items) {
			$order->changeItemsQty( $items );
		}
		
		$order->checkIsReady();
		
		AJAX::operationResponse(true);
		
	}
	
	public function renderDeleteButton() : string
	{
		return $this->renderButton();
	}
}