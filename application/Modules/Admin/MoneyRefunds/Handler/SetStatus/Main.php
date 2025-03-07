<?php
/**
 * @copyright Copyright (c) Miroslav Marek <mirek.marek@web-jet.cz>
 * @license EUPL 1.2  https://eupl.eu/1.2/en/
 * @author Miroslav Marek <mirek.marek@web-jet.cz>
 */
namespace JetApplicationModule\Admin\MoneyRefunds;

use Jet\Http_Headers;
use Jet\Http_Request;
use JetApplication\MoneyRefund_Status_Cancelled;
use JetApplication\MoneyRefund_Status_Done;
use JetApplication\MoneyRefund_Status_InProcessing;
use JetApplication\MoneyRefund_Status_New;

class Handler_SetStatus_Main extends Handler
{
	public const KEY = 'set_status';
	
	protected bool $has_dialog = true;
	
	protected function init() : void
	{
	}
	
	public function handleOnlyIfMoneyRefundIsEditable() : bool
	{
		return false;
	}
	
	public function renderDialog() : string
	{
		return '';
	}
	
	public function renderButton() : string
	{
		if(
			!$this->canBeHandled()
		) {
			return '';
		}
		
		
		return match ($this->money_refund->getStatusCode()) {
			MoneyRefund_Status_New::getCode()
					=> $this->view->render( 'set-status-button/new' ),
			MoneyRefund_Status_InProcessing::getCode()
					=> $this->view->render( 'set-status-button/in-processing' ),
			MoneyRefund_Status_Done::getCode()
					=> $this->view->render( 'set-status-button/done' ),
			MoneyRefund_Status_Cancelled::getCode()
					=> $this->view->render( 'set-status-button/cancelled' ),
			default => '',
		};
	}
	
	public function handle(): void
	{
		if( !$this->canBeHandled() ) {
			return;
		}
		
		$new_status = Http_Request::GET()->getString('set_status');
		if(!$new_status) {
			return;
		}
		
		switch($this->money_refund->getStatusCode()) {
			case MoneyRefund_Status_New::getCode():
				if($new_status=='start_processing') {
					$this->money_refund->startProcessing();
				}
				if($new_status=='cancel') {
					$this->money_refund->cancel();
				}
				break;
			case MoneyRefund_Status_InProcessing::getCode():
				if($new_status=='done') {
					$this->money_refund->done();
				}
				if($new_status=='cancel') {
					$this->money_refund->cancel();
				}
				if($new_status=='rollback') {
					$this->money_refund->rollback();
				}
				break;
			case MoneyRefund_Status_Done::getCode():
			case MoneyRefund_Status_Cancelled::getCode():
				if($new_status=='rollback') {
					$this->money_refund->rollback();
				}
				break;
		}
		
		Http_Headers::reload(unset_GET_params: ['set_status']);
	}
}