<?php
/**
 *
 * @copyright
 * @license
 * @author
 */
namespace JetApplicationModule\Admin\Complaints;

use Jet\Http_Headers;
use Jet\Http_Request;
use Jet\Tr;
use Jet\UI_messages;
use JetApplication\Complaint_Event;
use JetApplication\Complaint;

class Handler_SendMailConfirmation_Main extends Handler
{
	public const KEY = 'send_mail_confirmation';
	
	protected function init() : void
	{
	}
	
	public function handle() : void
	{
		if(Http_Request::GET()->getString('send_email')=='confirm') {
			
			$handler = Complaint_Event::getEventHandlerModule( Complaint::EVENT_NEW_COMPLAINT_FINISHED );
			
			if( $handler->sendNewComplaintEmail( $this->complaint ) ) {
				UI_messages::success( Tr::_('Complaint confirmation e-mail has been sent') );
			} else {
				UI_messages::danger( Tr::_('Error during complaint confirmation e-mail sending') );
			}
			
			Http_Headers::reload(unset_GET_params: ['send_email']);
			
		}
		
	}
	
	
	
}