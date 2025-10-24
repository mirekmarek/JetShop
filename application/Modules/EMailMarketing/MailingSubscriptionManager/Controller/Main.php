<?php
/**
 * @copyright Copyright (c) Miroslav Marek <mirek.marek@web-jet.cz>
 * @license EUPL 1.2  https://eupl.eu/1.2/en/
 * @author Miroslav Marek <mirek.marek@web-jet.cz>
 */
namespace JetApplicationModule\EMailMarketing\MailingSubscriptionManager;


use Jet\Http_Headers;
use Jet\Http_Request;
use Jet\MVC;
use Jet\MVC_Controller_Default;
use JetApplication\EShops;

class Controller_Main extends MVC_Controller_Default
{
	public function unsubscribe_Action() : void
	{
		/**
		 * @var Main $module
		 */
		$module = $this->module;
		$eshop = EShops::getCurrent();
		
		$data = file_get_contents('php://input');
		
		if($data) {
			$data = json_decode($data, true);
			
			if(
				is_array($data) &&
				isset($data['payload']['email']) &&
				isset($data['payload']['status'])
			) {
				if($data['payload']['status']=='UNSUBSCRIBED') {
					$module->unsubscribe( $eshop, $data['payload']['email'], 'Ecomail API - webhook' );
				}
			}
			
			die();
		}
		
		
		
		$GET = Http_Request::GET();
		
		$email = base64_decode( $GET->getString( 'm' ) );
		$key = $GET->getString( 'k' );
		
		if($key==$module->generateUnsubscribeKey( $eshop, $email )){
			$module->unsubscribe( $eshop, $email, 'unsubscribe page' );
		}
		
		
		$this->output('unsubscribed');
	}
	
	public function subscribe_Action() : void
	{
		/**
		 * @var Main $module
		 */
		$module = $this->module;
		$eshop = EShops::getCurrent();

		$email = Http_Request::GET()->getString( 'email' );
		if(!$email){
			Http_Headers::movedTemporary( MVC::getHomePage()->getURL() );
		}
		
		$module->subscribe( $eshop, $email, 'subscribe page' );
		
		$this->output('subscribed');
	}
}