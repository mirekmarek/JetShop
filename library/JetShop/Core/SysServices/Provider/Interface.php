<?php
namespace JetShop;

use JetApplication\SysServices_Definition;

interface Core_SysServices_Provider_Interface {
	
	/**
	 * @return SysServices_Definition[]
	 */
	public function getSysServicesDefinitions() : array;
}