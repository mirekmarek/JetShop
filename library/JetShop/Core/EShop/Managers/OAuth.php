<?php
/**
 * @copyright Copyright (c) Miroslav Marek <mirek.marek@web-jet.cz>
 * @license EUPL 1.2  https://eupl.eu/1.2/en/
 * @author Miroslav Marek <mirek.marek@web-jet.cz>
 */
namespace JetShop;


use Jet\Application_Module;
use JetApplication\EShop_OAuth_BackendModule;
use JetApplication\Manager_MetaInfo;

#[Manager_MetaInfo(
	group: Manager_MetaInfo::GROUP_ESHOP,
	is_mandatory: false,
	name: 'OAuth',
	description: '',
	module_name_prefix: 'EShop.'
)]
abstract class Core_EShop_Managers_OAuth extends Application_Module
{
	
	/**
	 * @return EShop_OAuth_BackendModule[]
	 */
	abstract public function getOAuthModules(): array;
	
	abstract public function handle( EShop_OAuth_BackendModule $module ): void;
	
	abstract public function renderLoginButton( EShop_OAuth_BackendModule $module ) : string;
	
}