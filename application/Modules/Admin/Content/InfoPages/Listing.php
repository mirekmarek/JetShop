<?php
/**
 *
 * @copyright 
 * @license  
 * @author  
 */
namespace JetApplicationModule\Admin\Content\InfoPages;

use JetApplication\Content_InfoPage as ContentInfoPage;
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
		'page_id'   => ['title' => 'Page ID'],
		'shop_code' => ['title' => 'Shop'],
		'is_active'    => [
			'title' => 'Status',
			'disallow_sort' => true,
		],
		'title' => ['title' => 'title'],
		'internal_description' => [
			'title' => 'Internal description',
			'disallow_sort' => true,
		]
	];
	
	/**
	 *
	 */
	protected function initFilters(): void
	{
		$this->filters['search'] = new Listing_Filter_Search($this);
	}

	/**
	 * @return ContentInfoPage[]|DataModel_Fetch_Instances
	 * @noinspection PhpDocSignatureInspection
	 */
	protected function getList() : DataModel_Fetch_Instances
	{
		return ContentInfoPage::getList();
	}

}