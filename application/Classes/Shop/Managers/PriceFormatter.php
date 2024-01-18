<?php
namespace JetApplication;

interface Shop_Managers_PriceFormatter {
	
	public function format( float $price, ?Shops_Shop $shop=null  ) : string;
	
	public function formatWithCurrency( float $price, ?Shops_Shop $shop=null ) : string;
	
	
}