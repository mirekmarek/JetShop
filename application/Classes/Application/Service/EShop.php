<?php
/**
 * @copyright Copyright (c) Miroslav Marek <mirek.marek@web-jet.cz>
 * @license EUPL 1.2  https://eupl.eu/1.2/en/
 * @author Miroslav Marek <mirek.marek@web-jet.cz>
 */
namespace JetApplication;


use Jet\Application_Module;
use JetShop\Core_Application_Service_EShop;

class Application_Service_EShop extends Core_Application_Service_EShop {

	public static function QRPayment( ?EShop $eshop = null ) : Application_Module|Application_Service_EShop_QRPayment|null
	{
		return static::list($eshop)->get( Application_Service_EShop_QRPayment::class );
	}
}