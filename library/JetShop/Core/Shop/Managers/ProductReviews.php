<?php
namespace JetShop;

use JetApplication\Product_ShopData;

interface Core_Shop_Managers_ProductReviews {
	
	public function renderRank( Product_ShopData $product ) : string;
	public function renderReviews( Product_ShopData $product ): string;
	public function handleCustomerSectionReviews() : void;
	public function renderCustomerSectionReviews() : string;
	
}