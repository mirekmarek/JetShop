<?php
namespace JetApplicationModule\Admin\Complaints;

use JetApplication\Complaint_EMailTemplate;
use Jet\Tr;

class Handler_Note_EMailTemplate extends Complaint_EMailTemplate {

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
