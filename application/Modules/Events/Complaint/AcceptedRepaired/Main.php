<?php
/**
 * @copyright Copyright (c) Miroslav Marek <mirek.marek@web-jet.cz>
 * @license EUPL 1.2  https://eupl.eu/1.2/en/
 * @author Miroslav Marek <mirek.marek@web-jet.cz>
 */
namespace JetApplicationModule\Events\Complaint\AcceptedRepaired;


use JetApplication\Complaint_Event_HandlerModule;
use JetApplication\EMail_TemplateProvider;


class Main extends Complaint_Event_HandlerModule implements EMail_TemplateProvider
{
	
	
	public function sendNotifications(): bool
	{
		$email_template = new EMailTemplate();

		$email_template->setComplaint( $this->complaint );
		
		$email = $email_template->createEmail( $this->complaint->getEshop() );
		
		return $email?->send()??true;
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
		$this->complaint->getDateFinished(true);
		
		return true;
	}
	
	public function getEventNameReadable(): string
	{
		return 'Accepted- repaired';
	}
	
	public function getEventCSSClass(): string
	{
		return 'event-done-success';
	}
}