<?php
/**
 *
 * @copyright 
 * @license  
 * @author  
 */
namespace JetShopModule\Admin\WarehouseManagement\Warehouses;

use JetShop\WarehouseManagement_Warehouse as Warehouse;

use Jet\Data_Listing;
use Jet\DataModel_Fetch_Instances;

/**
 *
 */
class Listing extends Data_Listing {

	/**
	 * @var array
	 */
	protected array $grid_columns = [
		'_edit_'     => [
			'title'         => '',
			'disallow_sort' => true
		],
		'code'         => ['title' => 'Code'],
		'shops'         => [
			'title' => 'Associated shops',
			'disallow_sort' => true
		],
		'internal_name' => ['title' => 'Internal name'],
		'internal_description' => ['title' => 'Internal description'],
	];

	/**
	 * @return Warehouse[]|DataModel_Fetch_Instances
	 * @noinspection PhpDocSignatureInspection
	 */
	protected function getList() : DataModel_Fetch_Instances
	{
		return Warehouse::getList();
	}
	
	protected function initFilters(): void
	{
		$this->filters['search'] = new Listing_Filter_Search($this);
	}

}