<?php
namespace JetShop;

use JetApplication\Product_EShopData;

interface Core_EShop_Managers_Compare {
	
	public function renderIntegration() : string;
	
	public function renderProductButton( Product_EShopData $product, bool $container=true ) : string;
	
	public function renderIcon() : string;
}