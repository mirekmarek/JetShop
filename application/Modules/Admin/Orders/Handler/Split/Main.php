<?php
/**
 *
 * @copyright
 * @license
 * @author
 */
namespace JetApplicationModule\Admin\Orders;

use Jet\AJAX;
use Jet\Http_Request;
use Jet\Tr;
use Jet\UI_messages;
use JetApplication\Order;

class Handler_Split_Main extends Handler
{
	public const KEY = 'split';
	
	protected bool $has_dialog = true;
	
	protected function init() : void
	{
	}
	
	public function handleOnlyIfOrderIsEditable() : bool
	{
		return true;
	}
	
	public function handle() : void
	{
		if(!Http_Request::GET()->exists('split_order')) {
			return;
		}
		
		$POST = Http_Request::POST();
		$new_qty = $POST->getRawData();
		
		$split_items = [];
		foreach($this->order->getItems() as $item) {
			if(!$POST->exists('/new_qty/'.$item->getId())) {
				continue;
			}
			
			$new_qty = $POST->getFloat('/new_qty/'.$item->getId());
			if(!$new_qty) {
				continue;
			}
			
			$split_items[ $item->getId() ] = $new_qty;
		}
		
		if($split_items) {
			$change = $this->order->split( $split_items );
			
			$new_order = null;
			
			foreach( $change->getChanges() as $change ) {
				if($change->getProperty()=='split_new_order') {
					$new_order_id = $change->getNewValue();
					
					$new_order = Order::get( $new_order_id );
					
					UI_messages::success(Tr::_('The order %old_order_number% has been split. New order number is <b>%%</b>', [
						'old_order_number' => $this->order->getNumber(),
						'new_order_number' => $new_order->getNumber(),
					]));
					
				}
			}
		}
		
		
		AJAX::operationResponse(true);
	
	}
}