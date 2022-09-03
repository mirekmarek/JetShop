<?php
/**
 *
 * @copyright 
 * @license  
 * @author  
 */
namespace JetShopModule\Admin\System\OrderStatus;

use JetShop\Order_Status as OrderStatus;

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
		'internal_description'   => [
			'title' => 'Internal description',
			'disallow_sort' => true
		],

	];

	/**
	 * @return OrderStatus[]|DataModel_Fetch_Instances
	 * @noinspection PhpDocSignatureInspection
	 */
	protected function getList() : DataModel_Fetch_Instances
	{
		return OrderStatus::getList();
	}
	
	protected function initFilters(): void
	{
		$this->filters['search'] = new Listing_Filter_Search($this);
	}
}