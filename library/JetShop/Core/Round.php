<?php
namespace JetShop;

use JetApplication\Round;
use JetApplication\Shops_Shop;

class Core_Round {
	

	public static function round( Shops_Shop $shop, float $price ) : float
	{
		return Round::round_WithVAT( $shop, $price );
	}
	
	public static function round_WithVAT( Shops_Shop $shop, float $price ) : float
	{
		return round( $price, $shop->getRoundPrecision_WithoutVAT() );
	}
	
	public static function round_VAT( Shops_Shop $shop, float $price ) : float
	{
		return round( $price, $shop->getRoundPrecision_VAT() );
	}
	
	public static function round_WithoutWAT( Shops_Shop $shop, float $price ) : float
	{
		return round( $price, $shop->getRoundPrecision_WithoutVAT() );
	}
	
}