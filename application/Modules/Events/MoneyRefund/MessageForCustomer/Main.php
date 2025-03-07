<?php
/**
 * @copyright Copyright (c) Miroslav Marek <mirek.marek@web-jet.cz>
 * @license EUPL 1.2  https://eupl.eu/1.2/en/
 * @author Miroslav Marek <mirek.marek@web-jet.cz>
 */
namespace JetApplicationModule\Events\MoneyRefund\MessageForCustomer;


use JetApplication\EMail_TemplateProvider;
use JetApplication\MoneyRefund_Event_HandlerModule;
use JetApplication\MoneyRefund_Note;


class Main extends MoneyRefund_Event_HandlerModule implements EMail_TemplateProvider
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
		 * @var MoneyRefund_Note $note
		 */
		$note = $this->event->getContext();
		if($note) {
			$template = new EMailTemplate();
			$template->setMoneyRefund( $this->money_refund );
			$template->setMessage( $note->getNote() );
			
			$email = $template->createEmail( $this->money_refund->getEshop() );
			
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