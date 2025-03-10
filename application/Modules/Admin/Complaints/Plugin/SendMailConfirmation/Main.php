<?php
/**
 * @copyright Copyright (c) Miroslav Marek <mirek.marek@web-jet.cz>
 * @license EUPL 1.2  https://eupl.eu/1.2/en/
 * @author Miroslav Marek <mirek.marek@web-jet.cz>
 */
namespace JetApplicationModule\Admin\Complaints;


use Jet\Http_Headers;
use Jet\Http_Request;
use Jet\Tr;
use Jet\UI_messages;
use JetApplication\Complaint_Event_NewComplaintFinished;

class Plugin_SendMailConfirmation_Main extends Plugin
{
	public const KEY = 'send_mail_confirmation';
	
	public function hasDialog(): bool
	{
		return false;
	}
	
	protected function init() : void
	{
	}
	
	public function handle() : void
	{
		if(Http_Request::GET()->getString('send_email')=='confirm') {
			
			$handler = $this->item->createEvent( Complaint_Event_NewComplaintFinished::new() )->getHandlerModule();
			
			if( $handler->sendNewComplaintEmail( $this->item ) ) {
				UI_messages::success( Tr::_('Complaint confirmation e-mail has been sent') );
			} else {
				UI_messages::danger( Tr::_('Error during complaint confirmation e-mail sending') );
			}
			
			Http_Headers::reload(unset_GET_params: ['send_email']);
			
		}
		
	}
	
}