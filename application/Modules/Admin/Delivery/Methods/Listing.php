<?php
/**
 *
 * @copyright 
 * @license  
 * @author  
 */
namespace JetShopModule\Admin\Delivery\Methods;

use JetShop\Delivery_Method;

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
		'classes'     => [
			'title'         => 'Classes',
			'disallow_sort' => true
		],
		'internal_name'   => ['title' => 'Internal name'],
		'status'          => [
			'title' => 'Status',
			'disallow_sort' => true
		],
		'payment_methods'   => ['title' => 'Payment methods', 'disallow_sort' => true],
		'services'   => ['title' => 'Services', 'disallow_sort' => true],
	];

	/**
	 * @var string[]
	 */
	protected array $filters = [
		'search',
	];

	/**
	 * @return Delivery_Method[]|DataModel_Fetch_Instances
	 * @noinspection PhpDocSignatureInspection
	 */
	protected function getList() : DataModel_Fetch_Instances
	{
		return Delivery_Method::getList();
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