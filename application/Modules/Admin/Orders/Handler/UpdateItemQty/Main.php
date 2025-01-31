<?php
/**
 * @copyright Copyright (c) Miroslav Marek <mirek.marek@web-jet.cz>
 * @license EUPL 1.2  https://eupl.eu/1.2/en/
 * @author Miroslav Marek <mirek.marek@web-jet.cz>
 */
namespace JetApplicationModule\Admin\Orders;



use Jet\AJAX;
use Jet\Http_Request;


class Handler_UpdateItemQty_Main extends Handler {
	
	public const KEY = 'delete_item';
	
	protected bool $has_dialog = true;
	
	protected function init() : void
	{
	}
	
	public function handle() : void
	{
		if(!Http_Request::GET()->exists('update_item_qty')) {
			return;
		}
		
		$POST = Http_Request::POST();
		$new_qty = $POST->getRawData();
		
		
		$items = [];
		foreach($this->order->getItems() as $item) {
			if(!$POST->exists('/new_qty/'.$item->getId())) {
				continue;
			}
			
			$items[$item->getId()] = $POST->getFloat('/new_qty/'.$item->getId());
		}
		
		if($items) {
			$this->order->changeItemsQty( $items );
		}
		
		$this->order->checkIsReady();
		
		AJAX::operationResponse(true);
		
	}
	
	public function renderDeleteButton() : string
	{
		return $this->renderButton();
	}
}