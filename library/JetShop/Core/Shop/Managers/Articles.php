<?php
namespace JetShop;

use JetApplication\Product_ShopData;

interface Core_Shop_Managers_Articles {
	
	public function renderProductAdvice( Product_ShopData $product ) : string;
	
}