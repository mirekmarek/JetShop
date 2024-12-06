<?php
namespace JetShop;

use JetApplication\Product_EShopData;

interface Core_EShop_Managers_Articles {
	
	public function renderProductAdvice( Product_EShopData $product ) : string;
	
}