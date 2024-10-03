<?php
namespace JetShop;

use JetApplication\Customer;

interface Core_Shop_Managers_CustomerLogin {

	public function handleLogin(): void;
	public function handleIsNotActivated( Customer $customer ) : void;
	public function handleIsBlocked( Customer $customer ) : void;
	public function handleMustChangePassword( Customer $customer ) : void;
	
	public function renderCustomerIcon() : string;
}