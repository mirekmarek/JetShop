<?php
namespace JetShop;

class ProductListing_Pagination extends Core_ProductListing_Pagination {


	public function getPaginationUrlParam() : string
	{
		return 'page';
	}

	public function init() : void
	{
	}

}