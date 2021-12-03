<?php
namespace JetShop;

class ProductListing_Filter_Brands extends Core_ProductListing_Filter_Brands {

	public function getFilterUrlParam() : string
	{
		return 'brand';
	}

}