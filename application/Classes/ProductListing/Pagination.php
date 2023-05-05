<?php
namespace JetApplication;

use JetShop\Core_ProductListing_Pagination;

class ProductListing_Pagination extends Core_ProductListing_Pagination {


	public function getPaginationUrlParam() : string
	{
		return 'page';
	}

	public function init() : void
	{
	}

}