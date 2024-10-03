<?php
/**
 *
 * @copyright 
 * @license  
 * @author  
 */
namespace JetApplicationModule\Shop\PriceFormatter;

use Jet\Application_Module;
use JetApplication\Currencies;
use JetApplication\Currencies_Currency;
use JetApplication\Shop_Managers_PriceFormatter;


class Main extends Application_Module implements Shop_Managers_PriceFormatter
{
	
	public function format( float $price, ?Currencies_Currency $currency=null  ): string
	{
		$currency = $currency?:Currencies::getCurrent();
		
		return number_format(
			$price,
			$currency->getDecimalPlaces(),
			$currency->getDecimalSeparator(),
			$currency->getThousandsSeparator()
		);
	}
	
	public function formatWithCurrency( float $price, ?Currencies_Currency $currency=null  ): string
	{
		$currency = $currency?:Currencies::getCurrent();
		
		return $currency->getSymbolLeft().$this->format( $price ).' '.$currency->getSymbolRight();
	}
}