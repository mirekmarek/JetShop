<?php
/**
 * @copyright Copyright (c) Miroslav Marek <mirek.marek@web-jet.cz>
 * @license EUPL 1.2  https://eupl.eu/1.2/en/
 * @author Miroslav Marek <mirek.marek@web-jet.cz>
 */
namespace JetShop;


use Error;
use Jet\Application_Module;

use Jet\Factory_MVC;
use Jet\Tr;
use JetApplication\EShopEntity_Event;


abstract class Core_Event_HandlerModule extends Application_Module
{
	
	abstract public function init( EShopEntity_Event $event ) : void;
	
	abstract public function getEvent(): EShopEntity_Event;
	
	
	public function handle() : bool
	{
		try {
			$e = $this->getEvent();
			
			if($e->getHandled()) {
				return true;
			}
			
			$error = false;
			
			$e->setErrorMessage('');
			
			if(
				!$this->getEvent()->getExternalsHandled() &&
				!$this->getEvent()->getDoNotHandleExternals()
			) {
				if($this->handleExternals()) {
					$this->getEvent()->setExternalsHandled( true );
					$this->getEvent()->save();
				} else {
					$error = true;
				}
			}
			
			if( !$this->getEvent()->getInternalsHandled() ) {
				if($this->handleInternals()) {
					$this->getEvent()->setInternalsHandled( true );
					$this->getEvent()->save();
				} else {
					$error = true;
				}
			}
			
			if(
				!$this->getEvent()->getNotificationSent() &&
				!$this->getEvent()->getDoNotSendNotification()
			) {
				if($this->sendNotifications()) {
					$this->getEvent()->setNotificationSent( true );
					$this->getEvent()->save();
				} else {
					$error = true;
				}
			}
			
			
			if($error) {
				return false;
			}
			
			$e->setHandled( true );
			$e->save();
			
			return true;
			
		} catch( Error $e) {
			$this->getEvent()->setErrorMessage( $this->getEvent()->getErrorMessage(). $e->getMessage() );
			$this->getEvent()->save();
			
			return false;
		}
	}
	
	abstract public function handleExternals() : bool;
	
	abstract public function handleInternals() : bool;
	
	abstract public function sendNotifications() : bool;
	
	abstract public function getEventNameReadable() : string;
	
	abstract public function getEventStyle() : string;
	
	public function getEventNameReadableTranslated() : string
	{
		return Tr::setCurrentDictionaryTemporary(
			dictionary: $this->module_manifest->getName(),
			action: function() {
				return $this->getEventNameReadable();
			}
		);
	}
	
	public function showEventName() : string
	{
		return $this->getEventNameReadableTranslated();
	}
	
	public function showEventDetails() : string
	{
		return Tr::setCurrentDictionaryTemporary(
			dictionary: $this->getModuleManifest()->getName(),
			action: function() {
				$view = Factory_MVC::getViewInstance( $this->getViewsDir() );
				
				$view->setVar( 'handler', $this );
				$view->setVar( 'event', $this->event );
				
				return $view->render('event-detail');
			}
		);
	}
	
}