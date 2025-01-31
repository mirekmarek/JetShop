<?php
/**
 * @copyright Copyright (c) Miroslav Marek <mirek.marek@web-jet.cz>
 * @license EUPL 1.2  https://eupl.eu/1.2/en/
 * @author Miroslav Marek <mirek.marek@web-jet.cz>
 */
namespace JetShop;


use JetApplication\Customer;

interface Core_EShop_Managers_CustomerLogin {

	public function handleLogin(): void;
	public function handleIsNotActivated( Customer $customer ) : void;
	public function handleIsBlocked( Customer $customer ) : void;
	public function handleMustChangePassword( Customer $customer ) : void;
	
	public function renderCustomerIcon() : string;
}