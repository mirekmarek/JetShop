<?php
/**
 *
 * @copyright 
 * @license  
 * @author  
 */
namespace JetApplicationModule\Admin\Discounts\CodesDefinition;


use JetApplication\Data_Listing_Filter_Shop;
use JetApplication\Discounts_Code as DiscountsCode;

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
		'_edit_'    => [
						'title'         => '',
						'disallow_sort' => true
					],
		'id'     => ['title' => 'ID'],
		'shop_code' => ['title' => 'Shop'],
		'code'      => ['title' => 'Code'],
		'status'    => [
						'title' => 'Status',
						'disallow_sort' => true,
					],
		'internal_description' => [
						'title' => 'Internal description',
						'disallow_sort' => true,
					]
	];


	/**
	 * @return DiscountsCode[]|DataModel_Fetch_Instances
	 * @noinspection PhpDocSignatureInspection
	 */
	protected function getList() : DataModel_Fetch_Instances
	{
		return DiscountsCode::getList();
	}
	
	
	protected function initFilters(): void
	{
		$this->filters['search'] = new Listing_Filter_Search($this);
		$this->filters['shop'] = new Data_Listing_Filter_Shop($this);
	}
}