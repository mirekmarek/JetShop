<?php
/**
 *
 * @copyright 
 * @license  
 * @author  
 */
namespace JetApplicationModule\Events\Order\PersonalReceiptPreparationStarted;

use JetApplication\EMail_TemplateProvider;
use JetApplication\MarketplaceIntegration;
use JetApplication\Order_Event_HandlerModule;

/**
 *
 */
class Main extends Order_Event_HandlerModule implements EMail_TemplateProvider
{
	public function handleExternals(): bool
	{
		$res = MarketplaceIntegration::handleOrderEvent( $this->event );
		if($res!==null) {
			return $res;
		}
		
		return true;
	}
	
	public function handleInternals(): bool
	{
		return true;
	}
	
	public function sendNotifications(): bool
	{
		return $this->sendEMail( new EMailTemplate() );
	}
	
	
	public function getEMailTemplates(): array
	{
		$template = new EMailTemplate();
		
		return [$template];
	}
	
	public function getEventNameReadable(): string
	{
		return 'Personal Receipt Preparation Started';
	}
	
	public function getEventStyle(): string
	{
		return 'background-color: #00ddc1;color: #111111;';
	}
}