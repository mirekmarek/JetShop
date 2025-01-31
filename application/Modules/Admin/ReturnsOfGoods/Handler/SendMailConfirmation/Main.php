<?php
/**
 * @copyright Copyright (c) Miroslav Marek <mirek.marek@web-jet.cz>
 * @license EUPL 1.2  https://eupl.eu/1.2/en/
 * @author Miroslav Marek <mirek.marek@web-jet.cz>
 */
namespace JetApplicationModule\Admin\ReturnsOfGoods;


use Jet\Http_Headers;
use Jet\Http_Request;
use Jet\Tr;
use Jet\UI_messages;
use JetApplication\ReturnOfGoods_Event;
use JetApplication\ReturnOfGoods;

class Handler_SendMailConfirmation_Main extends Handler
{
	public const KEY = 'send_mail_confirmation';
	
	protected function init() : void
	{
	}
	
	public function handle() : void
	{
		if(Http_Request::GET()->getString('send_email')=='confirm') {
			
			$handler = ReturnOfGoods_Event::getEventHandlerModule( ReturnOfGoods::EVENT_RETURN_OF_GOODS_FINISHED );
			
			if( $handler->sendNewReturnOfGoodsEmail( $this->return_of_goods ) ) {
				UI_messages::success( Tr::_('Return of goods confirmation e-mail has been sent') );
			} else {
				UI_messages::danger( Tr::_('Error during return of goods confirmation e-mail sending') );
			}
			
			Http_Headers::reload(unset_GET_params: ['send_email']);
			
		}
		
	}
	
	
	
}