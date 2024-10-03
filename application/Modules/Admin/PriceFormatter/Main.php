<?php
/**
 *
 * @copyright 
 * @license  
 * @author  
 */
namespace JetApplicationModule\Admin\PriceFormatter;

use Jet\Application_Module;
use JetApplication\Admin_Managers_PriceFormatter;
use JetApplication\Currencies_Currency;

/**
 *
 */
class Main extends Application_Module implements Admin_Managers_PriceFormatter
{
	
	public function format( Currencies_Currency $currency, float $price ): string
	{
		return number_format(
			$price,
			$currency->getDecimalPlaces(),
			$currency->getDecimalSeparator(),
			$currency->getThousandsSeparator()
		);
	}
	
	public function formatWithCurrency( Currencies_Currency $currency, float $price ): string
	{
		return $currency->getSymbolLeft().$this->format( $currency, $price ).$currency->getSymbolRight();
	}
	
	public function format_WithoutVAT( Currencies_Currency $currency, float $price ): string
	{
		return number_format(
			$price,
			$currency->getRoundPrecision_WithoutVAT(),
			$currency->getDecimalSeparator(),
			$currency->getThousandsSeparator()
		);
	}
	
	public function formatWithCurrency_WithoutVAT( Currencies_Currency $currency, float $price ): string
	{
		return $currency->getSymbolLeft().$this->format_WithoutVAT( $currency, $price ).$currency->getSymbolRight();
	}
	
	public function format_VAT( Currencies_Currency $currency, float $price ): string
	{
		return number_format(
			$price,
			$currency->getRoundPrecision_VAT(),
			$currency->getDecimalSeparator(),
			$currency->getThousandsSeparator()
		);
	}
	
	public function formatWithCurrency_VAT( Currencies_Currency $currency, float $price ): string
	{
		return $currency->getSymbolLeft().$this->format_VAT( $currency, $price ).$currency->getSymbolRight();
	}
	
	public function format_WithVAT( Currencies_Currency $currency, float $price ): string
	{
		return number_format(
			$price,
			$currency->getRoundPrecision_WithVAT(),
			$currency->getDecimalSeparator(),
			$currency->getThousandsSeparator()
		);
	}
	
	public function formatWithCurrency_WithVAT( Currencies_Currency $currency, float $price ): string
	{
		return $currency->getSymbolLeft().$this->format_WithVAT( $currency, $price ).$currency->getSymbolRight();
	}
	
}