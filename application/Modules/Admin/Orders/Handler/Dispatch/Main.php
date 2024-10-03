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
use JetApplication\WarehouseManagement;

class Handler_Dispatch_Main extends Handler
{
	public const KEY = 'dispatch';
	
	protected bool $has_dialog = true;
	
	
	protected function init() : void
	{
	}
	
	public function handleOnlyIfOrderIsEditable() : bool
	{
		return false;
	}
	
	
	public function handle(): void
	{
		if(
			!main::getCurrentUserCanEdit() ||
			$this->order->getDelivered() ||
			$this->order->getDispatched()
		) {
			return;
		}
		
		$dispatch_action = Http_Request::GET()->getString('dispatch_action');
		if(!$dispatch_action) {
			return;
		}
		

		switch($dispatch_action) {
			case 'recheck_is_ready':
				WarehouseManagement::actualizeOrderAvailability( $this->order );
				$this->order->checkIsReady();
				$this->order->save();
				
				break;
			/*
			case 'set_ready':
				if(!$this->order->getReadyForDispatch()) {
					$this->order->readyForDispatch();
				}
				break;
			case 'set_not_ready':
				if(
					$this->order->getReadyForDispatch() &&
					!$this->order->getDispatchStarted()
				) {
					$this->order->notReadyForDispatch();
				}
				break;
			*/
			case 'start_dispatch':
				if(
					$this->order->getReadyForDispatch() &&
					!$this->order->getDispatchStarted()
				) {
					$this->order->dispatchStarted();
				}
				break;
			case 'cancel_dispatch':
				if(
					$this->order->getReadyForDispatch() &&
					$this->order->getDispatchStarted()
				) {
					$this->order->cancelDispatch();
				}
				break;
		}

		
		Http_Headers::reload(unset_GET_params: ['dispatch_action']);
	}
	
	
	public function canBeHandled() : bool
	{
		if(
			!Main::getCurrentUserCanEdit() ||
			$this->order->isCancelled() ||
			$this->order->getDispatched() ||
			$this->order->getDelivered()
		) {
			return false;
		}
		
		return true;
	}
}