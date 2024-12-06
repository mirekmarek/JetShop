<?php
/**
 *
 * @copyright 
 * @license  
 * @author  
 */
namespace JetApplicationModule\Events\ReturnOfGoods\DoneAccepted;

use JetApplication\EMail_TemplateProvider;
use JetApplication\ReturnOfGoods_Event_HandlerModule;


/**
 *
 */
class Main extends ReturnOfGoods_Event_HandlerModule implements EMail_TemplateProvider
{
	

	
	public function sendNotifications(): bool
	{
		$email_template = new EMailTemplate();
		$email_template->setReturnOfGoods( $this->return_of_goods );
		
		$email = $email_template->createEmail( $this->return_of_goods->getEshop() );
		
		return $email->send();
	}
	
	public function getEMailTemplates(): array
	{
		return [
			(new EMailTemplate())
		];
	}
	
	public function handleExternals(): bool
	{
		return true;
	}
	
	public function handleInternals(): bool
	{
		return true;
	}
	
	public function getEventNameReadable(): string
	{
		return 'Done - accepted';
	}
	
	public function getEventStyle(): string
	{
		return '';
	}
}