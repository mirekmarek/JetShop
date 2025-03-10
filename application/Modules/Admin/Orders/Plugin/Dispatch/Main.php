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
use JetApplication\WarehouseManagement;

class Plugin_Dispatch_Main extends Plugin
{
	public const KEY = 'dispatch';
	
	
	public function hasDialog(): bool
	{
		return true;
	}
	
	
	protected function init() : void
	{
	}
	
	public function handleOnlyIfOrderIsEditable() : bool
	{
		return false;
	}
	
	
	public function handle(): void
	{
		/**
		 * @var Order $item
		 */
		$item = $this->item;
		
		if(
			!main::getCurrentUserCanEdit() ||
			$item->getDelivered() ||
			$item->getDispatched()
		) {
			return;
		}
		
		$dispatch_action = Http_Request::GET()->getString('dispatch_action');
		if(!$dispatch_action) {
			return;
		}
		

		switch($dispatch_action) {
			case 'recheck_is_ready':
				WarehouseManagement::actualizeOrderAvailability( $item );
				$item->checkIsReady();
				$item->save();
				
				break;
			case 'start_dispatch':
				if(
					$item->getReadyForDispatch() &&
					!$item->getDispatchStarted()
				) {
					$item->dispatchStarted();
				}
				break;
			case 'cancel_dispatch':
				if(
					$item->getReadyForDispatch() &&
					$item->getDispatchStarted()
				) {
					$item->cancelDispatch();
				}
				break;
		}

		
		Http_Headers::reload(unset_GET_params: ['dispatch_action']);
	}
	
	
	public function canBeHandled() : bool
	{
		/**
		 * @var Order $item
		 */
		$item = $this->item;
		
		if(
			!Main::getCurrentUserCanEdit() ||
			$item->isCancelled() ||
			$item->getDispatched() ||
			$item->getDelivered()
		) {
			return false;
		}
		
		return true;
	}
}