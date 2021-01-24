<?php
namespace JetShop;

class Core_Price {
	
	public static function format( float $price, string|null $shop_id=null ) : string
	{
		if(!$shop_id) {
			$shop_id = Shops::getCurrentId();
		}

		return number_format(
			$price,
			Shops::getCurrencyDecimalPlaces( $shop_id ),
			Shops::getCurrencyDecimalSeparator( $shop_id ),
			Shops::getCurrencyThousandsSeparator( $shop_id )
		);
	}

	public static function formatWithCurrency( float $price, string|null $shop_id=null ) : string
	{
		if(!$shop_id) {
			$shop_id = Shops::getCurrentId();
		}

		return Shops::getCurrencySymbolLeft( $shop_id ).Price::format( $price, $shop_id ).Shops::getCurrencySymbolRight( $shop_id );
	}

	public static function formatWithVatTxt( float $price, string|null $shop_id=null ) : string
	{
		if(!$shop_id) {
			$shop_id = Shops::getCurrentId();
		}

		return Price::formatWithCurrency( $price, $shop_id ).Shops::getCurrencyWithVatTxt( $shop_id );
	}

}