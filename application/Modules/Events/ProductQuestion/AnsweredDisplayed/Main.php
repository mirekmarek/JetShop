<?php
/**
 * @copyright Copyright (c) Miroslav Marek <mirek.marek@web-jet.cz>
 * @license EUPL 1.2  https://eupl.eu/1.2/en/
 * @author Miroslav Marek <mirek.marek@web-jet.cz>
 */
namespace JetApplicationModule\Events\ProductQuestion\AnsweredDisplayed;


use Jet\Data_DateTime;
use JetApplication\EMail_TemplateProvider;
use JetApplication\ProductQuestion_Event_HandlerModule;


class Main extends ProductQuestion_Event_HandlerModule implements EMail_TemplateProvider
{
	public function handleExternals(): bool
	{
		return true;
	}
	
	public function handleInternals(): bool
	{
		$q = $this->question;
		
		$q->setAnswered( true );
		$q->setAnsweredDateTime( Data_DateTime::now() );
		$q->setDisplay( true );
		$q->save();
		$q->actualizeProduct();
		
		return true;
	}
	
	public function sendNotifications(): bool
	{
		if($this->question->getAuthorEmail()) {
			$email_template = new EMailTemplate();
			$email_template->setQuestion( $this->question );
			$email = $email_template->createEmail( $this->question->getEshop() );
			if($email) {
				$email->setSaveHistoryAfterSend( true );
				
				$email->send();
			}
		}
		
		return true;
	}
	
	public function getEventNameReadable(): string
	{
		return 'Answered - displayed';
	}
	
	public function getEventCSSClass(): string
	{
		return 'event-done-success';
	}
	
	public function getEMailTemplates(): array
	{
		return [
			new EMailTemplate()
		];
	}
}