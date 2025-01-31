<?php
/**
 * @copyright Copyright (c) Miroslav Marek <mirek.marek@web-jet.cz>
 * @license EUPL 1.2  https://eupl.eu/1.2/en/
 * @author Miroslav Marek <mirek.marek@web-jet.cz>
 */
namespace JetShop;


use JetApplication\EShopEntity_Note;
use Closure;
use JetApplication\EShopEntity_Note_MessageGenerator;

interface Core_Admin_Managers_Note
{
	public function init( EShopEntity_Note $new_note, string $generated_subject, string $customer_email_address, Closure $after_add ) : void;
	
	
	public function handle() : void;
	
	
	public function showDialog() : string;
	
	
	public function showNote( EShopEntity_Note $note ) : string;
	
	public function addMessageGenerator( EShopEntity_Note_MessageGenerator $generator ) : void;
	
	/**
	 * @return EShopEntity_Note_MessageGenerator[]
	 */
	public function getMessageGenerators() : array;
	
	
	
}