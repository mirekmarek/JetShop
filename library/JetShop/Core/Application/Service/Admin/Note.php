<?php
/**
 * @copyright Copyright (c) Miroslav Marek <mirek.marek@web-jet.cz>
 * @license EUPL 1.2  https://eupl.eu/1.2/en/
 * @author Miroslav Marek <mirek.marek@web-jet.cz>
 */
namespace JetShop;


use Jet\Application_Module;
use JetApplication\Application_Service_Admin;
use JetApplication\EShopEntity_Note;
use Closure;
use JetApplication\EShopEntity_Note_MessageGenerator;
use Jet\Application_Service_MetaInfo;

#[Application_Service_MetaInfo(
	group: Application_Service_Admin::GROUP,
	is_mandatory: false,
	name: 'Internal notes and messages for customer',
	description: '',
	module_name_prefix: 'Admin.'
)]
abstract class Core_Application_Service_Admin_Note extends Application_Module
{
	abstract public function init( EShopEntity_Note $new_note, string $generated_subject, string $customer_email_address, Closure $after_add ) : void;
	
	abstract public function handle() : void;
	
	abstract public function showDialog() : string;
	
	abstract public function showButton() : string;
	
	abstract public function showNote( EShopEntity_Note $note ) : string;
	
	abstract public function addMessageGenerator( EShopEntity_Note_MessageGenerator $generator ) : void;
	
	/**
	 * @return EShopEntity_Note_MessageGenerator[]
	 */
	abstract public function getMessageGenerators() : array;
}