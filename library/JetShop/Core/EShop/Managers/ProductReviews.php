<?php
namespace JetShop;

use JetApplication\Product_EShopData;

interface Core_EShop_Managers_ProductReviews {
	
	public function renderRank( Product_EShopData $product ) : string;
	public function renderReviews( Product_EShopData $product ): string;
	public function handleCustomerSectionReviews() : void;
	public function renderCustomerSectionReviews() : string;
	
}