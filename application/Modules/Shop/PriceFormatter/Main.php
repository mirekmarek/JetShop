<?php
/**
 *
 * @copyright 
 * @license  
 * @author  
 */
namespace JetApplicationModule\Shop\PriceFormatter;

use Jet\Application_Module;
use JetApplication\Shop_Managers_PriceFormatter;
use JetApplication\Shops;
use JetApplication\Shops_Shop;


class Main extends Application_Module implements Shop_Managers_PriceFormatter
{
	
	public function format( float $price, ?Shops_Shop $shop=null  ): string
	{
		$shop = $shop?:Shops::getCurrent();
		
		return number_format(
			$price,
			$shop->getCurrencyDecimalPlaces(),
			$shop->getCurrencyDecimalSeparator(),
			$shop->getCurrencyThousandsSeparator()
		);
	}
	
	public function formatWithCurrency( float $price, ?Shops_Shop $shop=null  ): string
	{
		$shop = $shop?:Shops::getCurrent();
		
		return $shop->getCurrencySymbolLeft().$this->format( $price ).$shop->getCurrencySymbolRight();
	}
}