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
use JetApplication\Shops_Shop;

/**
 *
 */
class Main extends Application_Module implements Admin_Managers_PriceFormatter
{
	
	public function format( Shops_Shop $shop, float $price ): string
	{
		return number_format(
			$price,
			$shop->getCurrencyDecimalPlaces(),
			$shop->getCurrencyDecimalSeparator(),
			$shop->getCurrencyThousandsSeparator()
		);
	}
	
	public function formatWithCurrency( Shops_Shop $shop, float $price ): string
	{
		return $shop->getCurrencySymbolLeft().$this->format( $shop, $price ).$shop->getCurrencySymbolRight();
	}

}