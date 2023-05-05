<?php
/**
 *
 * @copyright 
 * @license  
 * @author  
 */
namespace JetApplicationModule\Admin\Delivery\Classes;

use JetApplication\Delivery_Class;

use Jet\Data_Listing;
use Jet\DataModel_Fetch_Instances;


/**
 *
 */
class Listing extends Data_Listing {


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
	 * @return Delivery_Class[]|DataModel_Fetch_Instances
	 * @noinspection PhpDocSignatureInspection
	 */
	protected function getList() : DataModel_Fetch_Instances
	{
		return Delivery_Class::getList();
	}
	
	protected function initFilters(): void
	{
		$this->filters['search'] = new Listing_Filter_Search( $this );
	}
}