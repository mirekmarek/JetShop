<?php
namespace JetShop;

use JetApplication\Product_ShopData;

interface Core_Shop_Managers_Wishlist {
	
	public function renderIntegration() : string;
	
	public function renderProductButton( Product_ShopData $product, bool $container=true ) : string;
	
	public function renderIcon() : string;
	
}