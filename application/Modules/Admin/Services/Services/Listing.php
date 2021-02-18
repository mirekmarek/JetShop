<?php
/**
 *
 * @copyright 
 * @license  
 * @author  
 */
namespace JetShopModule\Admin\Services\Services;

use JetShop\Services_Service;

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
		'group'         => ['title' => 'Group'],
		'internal_name'   => ['title' => 'Name'],
	];

	/**
	 * @var string[]
	 */
	protected array $filters = [
		'search',
	];

	/**
	 * @return Services_Service[]|DataModel_Fetch_Instances
	 */
	protected function getList() : DataModel_Fetch_Instances
	{
		return Services_Service::getList();
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