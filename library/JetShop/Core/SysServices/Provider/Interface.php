<?php
/**
 * @copyright Copyright (c) Miroslav Marek <mirek.marek@web-jet.cz>
 * @license EUPL 1.2  https://eupl.eu/1.2/en/
 * @author Miroslav Marek <mirek.marek@web-jet.cz>
 */
namespace JetShop;


use JetApplication\SysServices_Definition;

interface Core_SysServices_Provider_Interface {
	
	/**
	 * @return SysServices_Definition[]
	 */
	public function getSysServicesDefinitions() : array;
}