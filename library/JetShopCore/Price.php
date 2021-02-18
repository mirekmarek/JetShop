<?php
namespace JetShop;

class Core_Price {
	
	public static function format( float $price, string|null $shop_code=null ) : string
	{
		if(!$shop_code) {
			$shop_code = Shops::getCurrentCode();
		}

		return number_format(
			$price,
			Shops::getCurrencyDecimalPlaces( $shop_code ),
			Shops::getCurrencyDecimalSeparator( $shop_code ),
			Shops::getCurrencyThousandsSeparator( $shop_code )
		);
	}

	public static function formatWithCurrency( float $price, string|null $shop_code=null ) : string
	{
		if(!$shop_code) {
			$shop_code = Shops::getCurrentCode();
		}

		return Shops::getCurrencySymbolLeft( $shop_code ).Price::format( $price, $shop_code ).Shops::getCurrencySymbolRight( $shop_code );
	}

	public static function formatWithVatTxt( float $price, string|null $shop_code=null ) : string
	{
		if(!$shop_code) {
			$shop_code = Shops::getCurrentCode();
		}

		return Price::formatWithCurrency( $price, $shop_code ).Shops::getCurrencyWithVatTxt( $shop_code );
	}

	public static function round( float $price, string|null $shop_code=null ) : float
	{
		if(!$shop_code) {
			$shop_code = Shops::getCurrentCode();
		}

		return round( $price, Shops::getCurrencyDecimalPlaces( $shop_code ) );
	}

}