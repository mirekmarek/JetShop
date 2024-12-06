<?php
namespace JetShop;

use JetApplication\Product_EShopData;

interface Core_EShop_Managers_ProductQuestions {
	
	public function renderQuestions( Product_EShopData $product ): string;
	
}