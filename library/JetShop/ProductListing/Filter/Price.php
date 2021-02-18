<?php
namespace JetShop;

class ProductListing_Filter_Price extends Core_ProductListing_Filter_Price {

	public function getFilterUrlParam() : string
	{
		return 'price';
	}

	public function getFilterDecimalPlaces() : int
	{
		return Shops::getCurrencyDecimalPlaces($this->shop_code);
	}

	public function getFilterStep() : int
	{
		if( $this->shop_code=='sk' ) {
			return 1;
		} else {
			return 10;
		}
	}

}