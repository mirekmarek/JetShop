<?php
namespace JetShopModule\Admin\Delivery\Deadlines;

use Jet\Data_Listing;
use Jet\DataModel_Fetch_Instances;

use JetShop\Delivery_Deadline;

class Listing extends Data_Listing {

	protected array $grid_columns = [
		'_edit_'     => [
			'title'         => '',
			'disallow_sort' => true
		],
		'code'       => ['title' => 'Code'],
		'internal_name'  => ['title' => 'Internal name'],
	];
	

	protected function getList() : DataModel_Fetch_Instances
	{
		return Delivery_Deadline::getList();
	}
	
	
	protected function initFilters(): void
	{
		$this->filters['search'] = new Listing_Filter_Search( $this );
	}
}