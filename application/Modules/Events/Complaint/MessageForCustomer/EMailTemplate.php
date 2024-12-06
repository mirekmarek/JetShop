<?php
/**
 *
 * @copyright
 * @license
 * @author
 */
namespace JetApplicationModule\Events\Complaint\MessageForCustomer;

use Jet\Tr;
use JetApplication\Complaint_EMailTemplate;

class EMailTemplate extends Complaint_EMailTemplate {
	
	protected string $message = '';
	
	public function getMessage(): string
	{
		return $this->message;
	}
	
	public function setMessage( string $message ): void
	{
		$this->message = nl2br($message);
	}
	
	public function init() : void
	{
		$this->setInternalName(Tr::_('Complaint - message'));
		$this->setInternalNotes('');
		
		$this->initCommonProperties();
		
		$message_property = $this->addProperty('message', Tr::_('Message text') );
		$message_property->setPropertyValueCreator( function() : string {
			return $this->message;
		} );
		
	}

}