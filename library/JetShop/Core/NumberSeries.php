<?php
/**
 * @copyright Copyright (c) Miroslav Marek <mirek.marek@web-jet.cz>
 * @license EUPL 1.2  https://eupl.eu/1.2/en/
 * @author Miroslav Marek <mirek.marek@web-jet.cz>
 */
namespace JetShop;


use JetApplication\Application_Service_General;
use JetApplication\Application_Service_General_NumberSeries;

class Core_NumberSeries {
	
	public static function getManager() : Application_Service_General_NumberSeries
	{
		return Application_Service_General::NumberSeries();
	}
}