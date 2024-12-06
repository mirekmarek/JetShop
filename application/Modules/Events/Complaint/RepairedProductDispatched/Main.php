<?php
/**
 *
 * @copyright 
 * @license  
 * @author  
 */
namespace JetApplicationModule\Events\Complaint\RepairedProductDispatched;

use JetApplication\Complaint_Event_HandlerModule;
use JetApplication\EMail_TemplateProvider;

/**
 *
 */
class Main extends Complaint_Event_HandlerModule implements EMail_TemplateProvider
{
	
	
	public function sendNotifications(): bool
	{
		$email_template = new EMailTemplate();

		$email_template->setComplaint( $this->complaint );
		
		$email = $email_template->createEmail( $this->complaint->getEshop() );
		
		return $email->send();
	}
	
	public function getEMailTemplates(): array
	{
		$email_template = new EMailTemplate();
		
		return [
			$email_template
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
		return 'Repaired product dispatched';
	}
	
	public function getEventStyle(): string
	{
		return '';
	}
}