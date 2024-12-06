<?php
namespace JetShop;

use JetApplication\Entity_Note;
use Closure;
use JetApplication\Entity_Note_MessageGenerator;

interface Core_Admin_Managers_Note
{
	public function init( Entity_Note $new_note, string $generated_subject, string $customer_email_address, Closure $after_add ) : void;
	
	
	public function handle() : void;
	
	
	public function showDialog() : string;
	
	
	public function showNote( Entity_Note $note ) : string;
	
	public function addMessageGenerator( Entity_Note_MessageGenerator $generator ) : void;
	
	/**
	 * @return Entity_Note_MessageGenerator[]
	 */
	public function getMessageGenerators() : array;
	
	
	
}