<?php
/**
 * @copyright Copyright (c) Miroslav Marek <mirek.marek@web-jet.cz>
 * @license EUPL 1.2  https://eupl.eu/1.2/en/
 * @author Miroslav Marek <mirek.marek@web-jet.cz>
 */
namespace JetApplicaTionModule\Events\Complaint\NewComplaintFinished;


use JetApplication\Complaint;
use JetApplication\Complaint_Event_HandlerModule;
use JetApplication\EMail_TemplateProvider;


class Main extends Complaint_Event_HandlerModule implements EMail_TemplateProvider
{
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
	
	public function sendNotifications(): bool
	{
		return $this->sendNewComplaintEmail( $this->complaint );
	}
	
	public function sendNewComplaintEmail( Complaint $complaint ): bool
	{
		$email_template = new EMailTemplate();
		
		$email_template->setComplaint( $complaint );
		
		$email = $email_template->createEmail( $complaint->getEshop() );
		
		return $email->send();
	}
	
	public function getEventNameReadable(): string
	{
		return 'New complaint finished';
	}
	
	public function getEventStyle(): string
	{
		return '';
	}
}