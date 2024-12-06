<?php
/**
 *
 * @copyright 
 * @license  
 * @author  
 */
namespace JetApplicationModule\Admin\PriceFormatter;

use Jet\Application_Module;
use Jet\Factory_MVC;
use Jet\Tr;
use JetApplication\Admin_Managers_PriceFormatter;
use JetApplication\Currency;
use JetApplication\Entity_Price;
use JetApplication\Pricelist;

/**
 *
 */
class Main extends Application_Module implements Admin_Managers_PriceFormatter
{
	
	public function format( Pricelist $pricelist, float $price ): string
	{
		if($pricelist->getPricesAreWithoutVat()) {
			return $this->format_WithoutVAT( $pricelist->getCurrency(), $price );
		}
		
		return $this->format_WithVAT( $pricelist->getCurrency(), $price );
	}
	
	public function formatWithCurrency( Pricelist $pricelist, float $price ): string
	{
		if($pricelist->getPricesAreWithoutVat()) {
			return $this->formatWithCurrency_WithoutVAT( $pricelist->getCurrency(), $price );
		}
		
		return $this->formatWithCurrency_WithVAT( $pricelist->getCurrency(), $price );
	}
	
	
	public function formatWithCurrencyByHasVAT( bool $has_VAT, Currency $currency, float $price ): string
	{
		if(!$has_VAT) {
			return $this->formatWithCurrency_WithoutVAT( $currency, $price );
		}
		
		return $this->formatWithCurrency_WithVAT( $currency, $price );
	}
	
	
	
	
	public function format_WithoutVAT( Currency $currency, float $price ): string
	{
		return number_format(
			$price,
			$currency->getDecimalPlaces_WithoutVAT(),
			$currency->getDecimalSeparator(),
			$currency->getThousandsSeparator()
		);
	}
	
	public function formatWithCurrency_WithoutVAT( Currency $currency, float $price ): string
	{
		return $currency->getSymbolLeft_WithoutVAT().$this->format_WithoutVAT( $currency, $price ).$currency->getSymbolRight_WithoutVAT();
	}
	
	public function format_VAT( Currency $currency, float $price ): string
	{
		return number_format(
			$price,
			$currency->getDecimalPlaces_VAT(),
			$currency->getDecimalSeparator(),
			$currency->getThousandsSeparator()
		);
	}
	
	public function formatWithCurrency_VAT( Currency $currency, float $price ): string
	{
		return $currency->getSymbolLeft_VAT().$this->format_VAT( $currency, $price ).$currency->getSymbolRight_VAT();
	}
	
	public function format_WithVAT( Currency $currency, float $price ): string
	{
		return number_format(
			$price,
			$currency->getDecimalPlaces_WithVAT(),
			$currency->getDecimalSeparator(),
			$currency->getThousandsSeparator()
		);
	}
	
	public function formatWithCurrency_WithVAT( Currency $currency, float $price ): string
	{
		return $currency->getSymbolLeft_WithVAT().$this->format_WithVAT( $currency, $price ).$currency->getSymbolRight_WithVAT();
	}
	
	public function showPriceInfo( Entity_Price $price ) : string
	{
		$view = Factory_MVC::getViewInstance( $this->getViewsDir() );
		$view->setVar('price', $price);
		
		return Tr::setCurrentDictionaryTemporary(dictionary: $this->module_manifest->getName(),action: function() use ($view) {
			return $view->render('price-info');
		});
	}
	
}