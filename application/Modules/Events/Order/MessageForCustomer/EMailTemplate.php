<?php
/**
 *
 * @copyright
 * @license
 * @author
 */
namespace JetApplicationModule\Events\Order\MessageForCustomer;

use Jet\Tr;
use JetApplication\Order_EMailTemplate;

class EMailTemplate extends Order_EMailTemplate {
	
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
		$this->setInternalName(Tr::_('Order - message'));
		$this->setInternalNotes('');
		
		$this->initCommonProperties();
		
		$message_property = $this->addProperty('message', Tr::_('Message text') );
		$message_property->setPropertyValueCreator( function() : string {
			return $this->message;
		} );
		
	}

}