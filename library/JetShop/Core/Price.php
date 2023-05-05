<?php
namespace JetShop;

use JetApplication\Shops;
use JetApplication\Shops_Shop;
use JetApplication\Price;

class Core_Price {
	
	public static function format( float $price, ?Shops_Shop $shop=null ) : string
	{
		return number_format(
			$price,
			Shops::getCurrencyDecimalPlaces( $shop ),
			Shops::getCurrencyDecimalSeparator( $shop ),
			Shops::getCurrencyThousandsSeparator( $shop )
		);
	}

	public static function formatWithCurrency( float $price, ?Shops_Shop $shop=null ) : string
	{
		return Shops::getCurrencySymbolLeft( $shop ).Price::format( $price, $shop ).Shops::getCurrencySymbolRight( $shop );
	}

	public static function formatWithVatTxt( float $price, ?Shops_Shop $shop=null  ) : string
	{
		return Price::formatWithCurrency( $price, $shop ).Shops::getCurrencyWithVatTxt( $shop );
	}

	public static function round( float $price, ?Shops_Shop $shop=null  ) : float
	{
		return round( $price, Shops::getCurrencyDecimalPlaces( $shop ) );
	}

}