<?php
namespace JetApplication;

use JetShop\Core_ProductListing_Filter_Price;

class ProductListing_Filter_Price extends Core_ProductListing_Filter_Price {

	public function getFilterUrlParam() : string
	{
		return 'price';
	}

	public function getFilterDecimalPlaces() : int
	{
		return Shops::getCurrencyDecimalPlaces($this->shop);
	}

	public function getFilterStep() : int
	{
		return 10;
	}

}