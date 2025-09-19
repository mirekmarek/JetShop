<?php
/**
 * @copyright Copyright (c) Miroslav Marek <mirek.marek@web-jet.cz>
 * @license EUPL 1.2  https://eupl.eu/1.2/en/
 * @author Miroslav Marek <mirek.marek@web-jet.cz>
 */
namespace JetShop;

use Jet\Application_Module;
use JetApplication\Application_Service_EShop;
use JetApplication\EShop;
use Jet\Application_Service_MetaInfo;

#[Application_Service_MetaInfo(
	group: Application_Service_EShop::GROUP,
	is_mandatory: false,
	name: 'E-mail marketing subscribe - backend',
	description: '',
	module_name_prefix: ''
)]
abstract class Core_Application_Service_EShop_EMailMarketingSubscribeManagerBackend extends Application_Module
{
	abstract public function isSubscribed( EShop $eshop, string $email_address ) : bool;
	
	abstract public function subscribe( EShop $eshop, string $email_address, string $source, string $comment='' ) : void;
	
	abstract public function unsubscribe( EShop $eshop, string $email_address, string $source, string $comment='' ) : void;
	
	abstract public function delete( EShop $eshop, string $email_address, string $source, string $comment='' ) : void;
	
	abstract public function changeMail( EShop $eshop, string $old_email_address, string $new_mail_address, string $source, string $comment='' ) : void;
	
	abstract public function showStatus( EShop $eshop, string $email ) : string;
}