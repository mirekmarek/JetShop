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
use Jet\Tr;
use Jet\UI_messages;
use JetApplication\Order_Event;
use JetApplication\Order;

class Handler_SendMailConfirmation_Main extends Handler
{
	public const KEY = 'send_mail_confirmation';
	
	protected function init() : void
	{
	}
	
	public function handle() : void
	{
		if(Http_Request::GET()->getString('send_email')=='confirm') {
			
			$handler = Order_Event::getEventHandlerModule( Order::EVENT_NEW_ORDER );
			
			if( $handler->sendNewOrderEmail( $this->order ) ) {
				UI_messages::success( Tr::_('Order confirmation e-mail has been sent') );
			} else {
				UI_messages::danger( Tr::_('Error during order confirmation e-mail sending') );
			}
			
			Http_Headers::reload(unset_GET_params: ['send_email']);
			
		}
		
	}
	
	
	
}