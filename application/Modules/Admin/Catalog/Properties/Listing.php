<?php
/**
 *
 * @copyright 
 * @license  
 * @author  
 */
namespace JetApplicationModule\Admin\Catalog\Properties;

use JetApplication\Property as Properties;

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
		'_edit_'               => [
			'title'         => '',
			'disallow_sort' => true
		],
		'id'                   => ['title' => 'ID'],
		'type'                 => ['title' => 'Type'],
		'internal_name'        => ['title' => 'Internal name'],
		'internal_description' => ['title' => 'Internal description'],
	];

	/**
	 *
	 */
	protected function initFilters(): void
	{
		$this->filters['search'] = new Listing_Filter_Search($this);
	}

	/**
	 * @return Properties[]|DataModel_Fetch_Instances
	 */
	protected function getList() : DataModel_Fetch_Instances
	{
		return Properties::getList();
	}

}