<?php
/**
 * @copyright Copyright (c) Miroslav Marek <mirek.marek@web-jet.cz>
 * @license EUPL 1.2  https://eupl.eu/1.2/en/
 * @author Miroslav Marek <mirek.marek@web-jet.cz>
 */
namespace JetShop;


use JetApplication\Discounts_Manager;
use JetApplication\Managers_General;

class Core_Discounts
{
	public static function Manager() : Discounts_Manager
	{
		return Managers_General::Discounts();
	}
	
}
