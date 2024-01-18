<?php
namespace JetApplication;

interface Admin_Managers_PriceFormatter {
	
	public function format( Shops_Shop $shop, float $price ) : string;
	
	public function formatWithCurrency( Shops_Shop $shop, float $price ) : string;
	
	
}