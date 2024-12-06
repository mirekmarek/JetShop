<?php
/**
 *
 * @copyright 
 * @license  
 * @author  
 */
namespace JetApplicationModule\Events\Complaint\MessageForCustomer;

use JetApplication\EMail_TemplateProvider;
use JetApplication\Complaint_Event_HandlerModule;
use JetApplication\Complaint_Note;

/**
 *
 */
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
	
	public function sendNotifications(): bool
	{
		/**
		 * @var Complaint_Note $note
		 */
		$note = $this->event->getContext();
		if($note) {
			$template = new EMailTemplate();
			$template->setComplaint( $this->complaint );
			$template->setMessage( $note->getNote() );
			
			$email = $template->createEmail( $this->complaint->getEshop() );
			
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