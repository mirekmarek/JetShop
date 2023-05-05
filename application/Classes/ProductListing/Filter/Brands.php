<?php
namespace JetApplication;

use JetShop\Core_ProductListing_Filter_Brands;

class ProductListing_Filter_Brands extends Core_ProductListing_Filter_Brands {

	public function getFilterUrlParam() : string
	{
		return 'brand';
	}

}