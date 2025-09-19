<?php
/**
 * @copyright Copyright (c) Miroslav Marek <mirek.marek@web-jet.cz>
 * @license EUPL 1.2  https://eupl.eu/1.2/en/
 * @author Miroslav Marek <mirek.marek@web-jet.cz>
 */
namespace JetShop;


use JetApplication\Application_Service_General_Files;
use JetApplication\Application_Service_General;

abstract class Core_Files {
	
	public static function Manager() : Application_Service_General_Files
	{
		return Application_Service_General::Files();
	}

}