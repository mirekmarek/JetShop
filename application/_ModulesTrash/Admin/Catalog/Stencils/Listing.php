<?php
namespace JetShopModule\Admin\Catalog\Stencils;

use Jet\Data_Listing;
use Jet\DataModel_Fetch_Instances;

use JetShop\Stencil;

class Listing extends Data_Listing {

	protected array $grid_columns = [
		'_edit_'     => [
			'title'         => '',
			'disallow_sort' => true
		],
		'id'         => ['title' => 'ID'],
		'name'       => ['title' => 'Name'],
	];

	protected function getList() : DataModel_Fetch_Instances
	{
		return Stencil::getList();
	}
	
	
	protected function initFilters(): void
	{
		$this->filters['search'] = new Listing_Filter_Search($this);
	}
}