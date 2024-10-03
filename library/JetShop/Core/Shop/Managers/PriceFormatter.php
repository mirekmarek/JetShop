<?php
namespace JetShop;

use JetApplication\Currencies_Currency;

interface Core_Shop_Managers_PriceFormatter {
	
	public function format( float $price, ?Currencies_Currency $currency=null  ) : string;
	
	public function formatWithCurrency( float $price, ?Currencies_Currency $currency=null ) : string;
	
	
}