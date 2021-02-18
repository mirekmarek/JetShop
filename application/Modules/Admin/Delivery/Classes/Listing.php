<?php
/**
 *
 * @copyright 
 * @license  
 * @author  
 */
namespace JetShopModule\Admin\Delivery\Classes;

use JetShop\Delivery_Class;

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
		'kinds'            => ['title' => 'Kinds', 'disallow_sort' => true],
		'internal_name'   => ['title' => 'Internal name'],
		'delivery_methods' => [
			'title' => 'Delivery methods',
			'disallow_sort' => true
		]
	];

	/**
	 * @var string[]
	 */
	protected array $filters = [
		'search',
	];

	/**
	 * @return Delivery_Class[]|DataModel_Fetch_Instances
	 */
	protected function getList() : DataModel_Fetch_Instances
	{
		return Delivery_Class::getList();
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