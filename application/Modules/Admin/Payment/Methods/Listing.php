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
	 * @return Payment_Method[]|DataModel_Fetch_Instances
	 * @noinspection PhpDocSignatureInspection
	 */
	protected function getList() : DataModel_Fetch_Instances
	{
		return Payment_Method::getList();
	}
	
	protected function initFilters(): void
	{
		$this->filters['search'] = new Listing_Filter_Search( $this );
	}
}