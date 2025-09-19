<?php
/**
 * @copyright Copyright (c) Miroslav Marek <mirek.marek@web-jet.cz>
 * @license EUPL 1.2  https://eupl.eu/1.2/en/
 * @author Miroslav Marek <mirek.marek@web-jet.cz>
 */
namespace JetApplicationModule\EShop\PriceFormatter;

use JetApplication\Currencies;
use JetApplication\Currency;
use JetApplication\Application_Service_EShop_PriceFormatter;
use JetApplication\Pricelist;
use JetApplication\Pricelists;


class Main extends Application_Service_EShop_PriceFormatter
{
	
	public function format( float $price, ?Pricelist $pricelist=null  ): string
	{
		$pricelist = $pricelist?:Pricelists::getCurrent();
		
		if($pricelist->getPricesAreWithoutVat()) {
			return $this->format_WithoutVAT( $price, $pricelist->getCurrency() );
		}
		
		return $this->format_WithVAT( $price, $pricelist->getCurrency() );
	}
	
	public function formatWithCurrency( float $price, ?Pricelist $pricelist=null  ): string
	{
		$pricelist = $pricelist?:Pricelists::getCurrent();
		
		if($pricelist->getPricesAreWithoutVat()) {
			return $this->formatWithCurrency_WithoutVAT( $price, $pricelist->getCurrency() );
		}
		
		return $this->formatWithCurrency_WithVAT( $price, $pricelist->getCurrency() );
	}
	
	public function format_WithoutVAT( float $price, ?Currency $currency=null  ): string
	{
		$currency = $currency?:Currencies::getCurrent();
		
		return number_format(
			$price,
			$currency->getDecimalPlaces_WithoutVAT(),
			$currency->getDecimalSeparator(),
			$currency->getThousandsSeparator()
		);
	}
	
	public function formatWithCurrency_WithoutVAT( float $price, ?Currency $currency=null  ): string
	{
		return $currency->getSymbolLeft_WithoutVAT().$this->format_WithoutVAT( $price, $currency ).$currency->getSymbolRight_WithoutVAT();
	}
	
	public function format_VAT( float $price, ?Currency $currency=null ): string
	{
		$currency = $currency?:Currencies::getCurrent();
		
		return number_format(
			$price,
			$currency->getDecimalPlaces_VAT(),
			$currency->getDecimalSeparator(),
			$currency->getThousandsSeparator()
		);
	}
	
	public function formatWithCurrency_VAT( float $price, ?Currency $currency=null  ): string
	{
		return $currency->getSymbolLeft_VAT().$this->format_VAT( $price, $currency  ).$currency->getSymbolRight_VAT();
	}
	
	public function format_WithVAT( float $price, ?Currency $currency=null  ): string
	{
		$currency = $currency?:Currencies::getCurrent();
		
		return number_format(
			$price,
			$currency->getDecimalPlaces_WithVAT(),
			$currency->getDecimalSeparator(),
			$currency->getThousandsSeparator()
		);
	}
	
	public function formatWithCurrency_WithVAT( float $price, ?Currency $currency=null   ): string
	{
		return $currency->getSymbolLeft_WithVAT().$this->format_WithVAT( $price, $currency ).$currency->getSymbolRight_WithVAT();
	}
	
}