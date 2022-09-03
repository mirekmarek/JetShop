<?php

namespace JetShopModule\Admin\Catalog\Products;

use Jet\Data_Listing;
use Jet\DataModel_Fetch_Instances;

use JetShop\Product;

class Listing extends Data_Listing
{
	
	protected array $grid_columns = [
		'_edit_'                  => [
			'title'         => '',
			'disallow_sort' => true
		],
		'id'                      => ['title' => 'ID'],
		'products_shop_data.name' => ['title' => 'Name'],
	];
	
	protected function getList(): DataModel_Fetch_Instances
	{
		return Product::getList();
	}
	
	protected function initFilters(): void
	{
		$this->filters['search'] = new Listing_Filter_Search( $this );
		$this->filters['categories'] = new Listing_Filter_Categories( $this );
		$this->filters['product_type'] = new Listing_Filter_ProductType( $this );
		$this->filters['is_active'] = new Listing_Filter_IsActive( $this );
	}
	
	public function getFilter_search() : Listing_Filter_Search
	{
		/** @noinspection PhpIncompatibleReturnTypeInspection */
		return $this->filters['search'];
	}
	
	public function getFilter_categories() : Listing_Filter_Categories
	{
		/** @noinspection PhpIncompatibleReturnTypeInspection */
		return $this->filters['categories'];
	}
	
	public function getFilter_product_type() : Listing_Filter_ProductType
	{
		/** @noinspection PhpIncompatibleReturnTypeInspection */
		return $this->filters['product_type'];
	}
	
	public function getFilter_is_active() : Listing_Filter_IsActive
	{
		/** @noinspection PhpIncompatibleReturnTypeInspection */
		return $this->filters['is_active'];
	}
	
}