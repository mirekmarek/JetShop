<?php
/**
 *
 */

namespace JetShop;

use Jet\Application_Module;
use Jet\Application_Modules;

use JetApplication\Delivery_Pricing_Module;
use JetApplication\CashDesk;
use JetApplication\Delivery_Method;
use JetApplication\Delivery_Pricing_PriceInfo;


abstract class Core_Delivery_Pricing_Module extends Application_Module
{
	protected static string $module_name = 'Order.Delivery.Pricing';

	public static function getModuleName(): string
	{
		return self::$module_name;
	}

	public static function setModuleName( string $module_name ): void
	{
		self::$module_name = $module_name;
	}

	public static function getModule() : Delivery_Pricing_Module
	{
		/** @noinspection PhpIncompatibleReturnTypeInspection */
		return Application_Modules::moduleInstance( Delivery_Pricing_Module::getModuleName() );
	}

	abstract public function getPrice( CashDesk $cash_desk, Delivery_Method $delivery_method ) : Delivery_Pricing_PriceInfo;

}