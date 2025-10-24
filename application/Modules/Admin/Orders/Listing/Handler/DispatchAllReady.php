<?php
/**
 * @copyright Copyright (c) Miroslav Marek <mirek.marek@web-jet.cz>
 * @license EUPL 1.2  https://eupl.eu/1.2/en/
 * @author Miroslav Marek <mirek.marek@web-jet.cz>
 */
namespace JetApplicationModule\Admin\Orders;



use Jet\Http_Headers;
use Jet\Http_Request;
use JetApplication\Admin_Listing_Handler;
use JetApplication\Order;

class Listing_Handler_DispatchAllReady extends Admin_Listing_Handler
{
	public const KEY = 'dispatch_all_ready';
	
	protected bool $has_dialog = true;
	
	
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
	
	public function canBeHandled(): bool
	{
		return false;
		return Main::getCurrentUserCanEdit();
	}
}