<?php
/**
 *
 */

namespace JetShop;

use Jet\Application_Module;
use Jet\Application_Modules;

use JetApplication\CashDesk;
use JetApplication\Payment_Pricing_Module;
use JetApplication\Payment_Method;
use JetApplication\Payment_Pricing_PriceInfo;

abstract class Core_Payment_Pricing_Module extends Application_Module
{
	protected static string $module_name = 'Order.Payment.Pricing';

	public static function getModuleName(): string
	{
		return self::$module_name;
	}

	public static function setModuleName( string $module_name ): void
	{
		self::$module_name = $module_name;
	}

	public static function getModule() : Payment_Pricing_Module
	{
		/** @noinspection PhpIncompatibleReturnTypeInspection */
		return Application_Modules::moduleInstance( Payment_Pricing_Module::getModuleName() );
	}

	abstract public function getPrice( CashDesk $cash_desk, Payment_Method $payment_method ) : Payment_Pricing_PriceInfo;

}