<?php
namespace JetShop;

use JetApplication\Product_ShopData;

interface Core_Shop_Managers_ProductListing
{
	public function init( array $product_ids, int $category_id=0, string $category_name='', ?string $optional_URL_parameter = null ) : void;
	
	public function render() : string;
	
	public function renderItem( Product_ShopData $item ) : string;
}