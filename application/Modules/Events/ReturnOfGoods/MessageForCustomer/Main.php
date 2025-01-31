<?php
/**
 * @copyright Copyright (c) Miroslav Marek <mirek.marek@web-jet.cz>
 * @license EUPL 1.2  https://eupl.eu/1.2/en/
 * @author Miroslav Marek <mirek.marek@web-jet.cz>
 */
namespace JetApplicationModule\Events\ReturnOfGoods\MessageForCustomer;


use JetApplication\EMail_TemplateProvider;
use JetApplication\ReturnOfGoods_Event_HandlerModule;
use JetApplication\ReturnOfGoods_Note;


class Main extends ReturnOfGoods_Event_HandlerModule implements EMail_TemplateProvider
{
	
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
		/**
		 * @var ReturnOfGoods_Note $note
		 */
		$note = $this->event->getContext();
		if($note) {
			$template = new EMailTemplate();
			$template->setReturnOfGoods( $this->return_of_goods );
			$template->setMessage( $note->getNote() );
			
			$email = $template->createEmail( $this->return_of_goods->getEshop() );
			
			$email->setSubject( $note->getSubject() );
			
			foreach($note->getFiles() as $file) {
				$email->addAttachments( $file->getPath() );
			}
			
			$email->setTo( $note->getCustomerEmailAddress() );
			$email->send();
			
		}
		
		return true;
	}
	
	
	public function getEMailTemplates(): array
	{
		$template = new EMailTemplate();
		
		return [$template];
	}
	
	public function getEventNameReadable(): string
	{
		return 'Message for customer';
	}
	
	public function getEventStyle(): string
	{
		return 'background-color: #c29595;';
	}
}