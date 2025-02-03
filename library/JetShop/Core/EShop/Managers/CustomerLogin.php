<?php
/**
 * @copyright Copyright (c) Miroslav Marek <mirek.marek@web-jet.cz>
 * @license EUPL 1.2  https://eupl.eu/1.2/en/
 * @author Miroslav Marek <mirek.marek@web-jet.cz>
 */
namespace JetShop;


use Jet\Application_Module;
use JetApplication\Customer;
use JetApplication\Manager_MetaInfo;

#[Manager_MetaInfo(
	group: Manager_MetaInfo::GROUP_ESHOP,
	is_mandatory: true,
	name: 'Customer Login',
	description: '',
	module_name_prefix: 'EShop.'
)]
abstract class Core_EShop_Managers_CustomerLogin extends Application_Module
{

	abstract public function handleLogin(): void;
	abstract public function handleIsNotActivated( Customer $customer ) : void;
	abstract public function handleIsBlocked( Customer $customer ) : void;
	abstract public function handleMustChangePassword( Customer $customer ) : void;
	
	abstract public function renderCustomerIcon() : string;
}