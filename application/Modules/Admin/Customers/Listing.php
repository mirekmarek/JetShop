<?php
/**
 *
 * @copyright 
 * @license  
 * @author  
 */
namespace JetShopModule\Admin\Customers;

use JetShop\Customer as Customer;

use Jet\Data_Listing;
use Jet\DataModel_Fetch_Instances;
use JetShop\Data_Listing_Filter_Shop;


/**
 *
 */
class Listing extends Data_Listing {
	
	protected array $grid_columns = [
		'_edit_'     => [
			'title'         => '',
			'disallow_sort' => true
		],
		'id'         => ['title' => 'ID'],
		'shop_code' => ['title' => 'Shop'],
		'email'   => ['title' => 'E-mail'],
		'first_name'   => ['title' => 'First name'],
		'surname'   => ['title' => 'Surname'],
	];

	/**
	 * @return Customer[]|DataModel_Fetch_Instances
	 * @noinspection PhpDocSignatureInspection
	 */
	protected function getList() : DataModel_Fetch_Instances
	{
		return Customer::getList();
	}
	
	
	protected function initFilters(): void
	{
		$this->filters['search'] = new Listing_Filter_Search( $this );
		$this->filters['shop'] = new Data_Listing_Filter_Shop($this);
	}
}