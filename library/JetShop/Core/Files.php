<?php
/**
 * @copyright Copyright (c) Miroslav Marek <mirek.marek@web-jet.cz>
 * @license EUPL 1.2  https://eupl.eu/1.2/en/
 * @author Miroslav Marek <mirek.marek@web-jet.cz>
 */
namespace JetShop;


use JetApplication\Files_Manager;
use JetApplication\Managers_General;

abstract class Core_Files {
	
	public static function Manager() : Files_Manager
	{
		return Managers_General::Files();
	}

}