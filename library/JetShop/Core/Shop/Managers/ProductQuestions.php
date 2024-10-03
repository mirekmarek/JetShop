<?php
namespace JetShop;

use JetApplication\Product_ShopData;

interface Core_Shop_Managers_ProductQuestions {
	
	public function renderQuestions( Product_ShopData $product ): string;
	
}