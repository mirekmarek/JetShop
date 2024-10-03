<?php
/**
 *
 * @copyright
 * @license
 * @author
 */
namespace JetApplicationModule\Admin\Orders;


use Jet\Http_Headers;
use Jet\Http_Request;

class ListingHandler_DispatchAllReady_Main extends ListingHandler {
	public const KEY = 'dispatch_all_ready';
	
	protected bool $has_dialog = true;
	
	
	protected function init() : void
	{

	}
	
	public function handle() : void
	{
		if(Http_Request::GET()->getString('action')=='dispatch_all_ready') {
			$ids = Order::dataFetchCol(
				select: ['id'],
				where: [
					'ready_for_dispatch' => true,
					'AND',
					'dispatch_started' => false
				]
			);
			
			foreach($ids as $id) {
				Order::get( $id )?->dispatchStarted();
			}
			
			Http_Headers::reload(unset_GET_params: ['action']);
		}
	}
}