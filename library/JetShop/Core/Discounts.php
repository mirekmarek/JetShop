<?php
/**
 * @copyright Copyright (c) Miroslav Marek <mirek.marek@web-jet.cz>
 * @license EUPL 1.2  https://eupl.eu/1.2/en/
 * @author Miroslav Marek <mirek.marek@web-jet.cz>
 */
namespace JetShop;


use JetApplication\Application_Service_General_DiscountsManager;
use JetApplication\Application_Service_General;

class Core_Discounts
{
	public static function Manager() : Application_Service_General_DiscountsManager
	{
		return Application_Service_General::DiscountsManager();
	}
	
}
