<?php
/**
 * @copyright Copyright (c) Miroslav Marek <mirek.marek@web-jet.cz>
 * @license EUPL 1.2  https://eupl.eu/1.2/en/
 * @author Miroslav Marek <mirek.marek@web-jet.cz>
 */
namespace JetShop;


use JetApplication\Managers_General;
use JetApplication\NumberSeries_Manager;

class Core_NumberSeries {
	
	public static function getManager() : NumberSeries_Manager
	{
		return Managers_General::NumberSeries();
	}
}