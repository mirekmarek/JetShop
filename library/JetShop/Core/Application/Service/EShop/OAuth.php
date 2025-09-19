<?php
/**
 * @copyright Copyright (c) Miroslav Marek <mirek.marek@web-jet.cz>
 * @license EUPL 1.2  https://eupl.eu/1.2/en/
 * @author Miroslav Marek <mirek.marek@web-jet.cz>
 */
namespace JetShop;


use Jet\Application_Module;
use JetApplication\Application_Service_EShop;
use JetApplication\Application_Service_EShop_OAuthBackendModule;
use Jet\Application_Service_MetaInfo;

#[Application_Service_MetaInfo(
	group: Application_Service_EShop::GROUP,
	is_mandatory: false,
	name: 'OAuth',
	description: '',
	module_name_prefix: 'EShop.'
)]
abstract class Core_Application_Service_EShop_OAuth extends Application_Module
{
	
	/**
	 * @return Application_Service_EShop_OAuthBackendModule[]
	 */
	abstract public function getOAuthModules(): array;
	
	abstract public function handle( Application_Service_EShop_OAuthBackendModule $module ): void;
	
	abstract public function renderLoginButtons() : string;
	
	abstract public function renderLoginButton( Application_Service_EShop_OAuthBackendModule $module ) : string;
	
}