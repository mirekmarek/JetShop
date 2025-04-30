<?php
/**
 * @copyright Copyright (c) Miroslav Marek <mirek.marek@web-jet.cz>
 * @license EUPL 1.2  https://eupl.eu/1.2/en/
 * @author Miroslav Marek <mirek.marek@web-jet.cz>
 */
namespace JetApplicationModule\Events\Order\MessageForCustomer;


use JetApplication\EMail_TemplateProvider;
use JetApplication\MarketplaceIntegration;
use JetApplication\Order_Event_HandlerModule;
use JetApplication\Order_Note;


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
		/**
		 * @var Order_Note $note
		 */
		$note = $this->event->getContext();
		if($note) {
			$template = new EMailTemplate();
			$template->setOrder( $this->order );
			$template->setMessage( $note->getNote() );
			
			$email = $template->createEmail( $this->order->getEshop() );
			
			if($email) {
				$email->setSubject( $note->getSubject() );
				
				foreach($note->getFiles() as $file) {
					$email->addAttachments( $file->getPath() );
				}
				
				$email->setTo( $note->getCustomerEmailAddress() );
				$email->send();
			}
			
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
	
	public function getEventCSSClass(): string
	{
		return 'event-message-for-customer';
	}
}