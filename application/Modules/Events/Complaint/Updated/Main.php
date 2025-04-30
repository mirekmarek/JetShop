<?php
/**
 * @copyright Copyright (c) Miroslav Marek <mirek.marek@web-jet.cz>
 * @license EUPL 1.2  https://eupl.eu/1.2/en/
 * @author Miroslav Marek <mirek.marek@web-jet.cz>
 */
namespace JetApplicationModule\Events\Complaint\Updated;


use JetApplication\Complaint;
use JetApplication\EMail_TemplateProvider;
use JetApplication\Complaint_Event_HandlerModule;


class Main extends Complaint_Event_HandlerModule implements EMail_TemplateProvider
{
	
	public function handleExternals(): bool
	{
		return true;
	}
	
	public function handleInternals(): bool
	{
		return true;
	}
	
	public function sendComplaintUpdatedEmail( Complaint $complaint ) : bool
	{
		$template = new EMailTemplate();
		$template->setComplaint( $complaint );
		$email = $template->createEmail( $complaint->getEshop() );
		
		return $email?->send()??true;
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
		return 'Complaint updated';
	}
	
	public function getEventCSSClass(): string
	{
		return 'event-updated';
	}
}