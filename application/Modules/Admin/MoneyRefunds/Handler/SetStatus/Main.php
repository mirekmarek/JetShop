<?php
/**
 * @copyright Copyright (c) Miroslav Marek <mirek.marek@web-jet.cz>
 * @license EUPL 1.2  https://eupl.eu/1.2/en/
 * @author Miroslav Marek <mirek.marek@web-jet.cz>
 */
namespace JetApplicationModule\Admin\MoneyRefunds;

use Jet\Http_Headers;
use Jet\Http_Request;

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
		
		return $this->view->render( 'set-status-button' );
	}
	
	public function handle(): void
	{
		if( !$this->canBeHandled() ) {
			return;
		}
		
		$future_states = $this->money_refund->getStatus()->getPossibleFutureStates();
		
		$new_status = Http_Request::GET()->getString('set_status');
		if(!$new_status) {
			return;
		}
		
		foreach( $future_states as $future_state ) {
			if( get_class($future_state->getStatus())==$new_status ) {
				$this->money_refund->setStatus( $future_state->getStatus() );
			}
		}
		
		Http_Headers::reload(unset_GET_params: ['set_status']);
	}
}