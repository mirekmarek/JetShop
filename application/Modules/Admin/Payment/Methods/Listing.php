<?php
/**
 *
 * @copyright 
 * @license  
 * @author  
 */
namespace JetShopModule\Admin\Payment\Methods;

use JetShop\Payment_Method;

use Jet\Data_Listing;
use Jet\Data_Listing_Filter_search;
use Jet\DataModel_Fetch_Instances;

/**
 *
 */
class Listing extends Data_Listing {

	use Data_Listing_Filter_search;

	/**
	 * @var array
	 */
	protected array $grid_columns = [
		'_edit_'     => [
			'title'         => '',
			'disallow_sort' => true
		],
		'code'         => ['title' => 'Code'],
		'kind'         => ['title' => 'Kind'],
		'internal_name'   => ['title' => 'Internal name'],
		'status'          => [
			'title' => 'Status',
			'disallow_sort' => true
		],
		'delivery_methods'   => ['title' => 'Delivery methods', 'disallow_sort' => true],
		'services'   => ['title' => 'Services', 'disallow_sort' => true],
	];

	/**
	 * @var string[]
	 */
	protected array $filters = [
		'search',
	];

	/**
	 * @return Payment_Method[]|DataModel_Fetch_Instances
	 */
	protected function getList() : DataModel_Fetch_Instances
	{
		return Payment_Method::getList();
	}

	/**
	 *
	 */
	protected function filter_search_getWhere() : void
	{
		if(!$this->search) {
			return;
		}

		$search = '%'.$this->search.'%';
		$this->filter_addWhere([
			'internal_name *'   => $search,
		]);

	}
}