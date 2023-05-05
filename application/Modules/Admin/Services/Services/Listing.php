<?php
/**
 *
 * @copyright 
 * @license  
 * @author  
 */
namespace JetApplicationModule\Admin\Services\Services;

use JetApplication\Services_Service;

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
		'group'         => ['title' => 'Group'],
		'internal_name'   => ['title' => 'Name'],
	];
	
	/**
	 * @return Services_Service[]|DataModel_Fetch_Instances
	 * @noinspection PhpDocSignatureInspection
	 */
	protected function getList() : DataModel_Fetch_Instances
	{
		return Services_Service::getList();
	}
	
	
	protected function initFilters(): void
	{
		$this->filters['search'] = new Listing_Filter_Search( $this );
	}
}