<?php
/**
 * @copyright Copyright (c) Miroslav Marek <mirek.marek@web-jet.cz>
 * @license EUPL 1.2  https://eupl.eu/1.2/en/
 * @author Miroslav Marek <mirek.marek@web-jet.cz>
 */
namespace JetShop;


use Jet\Application_Module;
use JetApplication\Application_Service_EShop;
use JetApplication\Customer;
use Jet\Application_Service_MetaInfo;

#[Application_Service_MetaInfo(
	group: Application_Service_EShop::GROUP,
	is_mandatory: true,
	name: 'Customer Login',
	description: '',
	module_name_prefix: 'EShop.'
)]
abstract class Core_Application_Service_EShop_CustomerLogin extends Application_Module
{

	abstract public function handleLogin(): void;
	abstract public function handleIsNotActivated( Customer $customer ) : void;
	abstract public function handleIsBlocked( Customer $customer ) : void;
	abstract public function handleMustChangePassword( Customer $customer ) : void;
	
	abstract public function renderCustomerIcon() : string;
}