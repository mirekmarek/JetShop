<?php
namespace JetApplicationModule\Admin\Catalog\Products;

use Jet\DataListing_Filter_Search;
use JetApplication\Admin_Managers;
use JetApplication\Product;

class Listing_Filter_Search extends DataListing_Filter_Search {
	
	public const KEY = 'search';
	
	/**
	 * @var string
	 */
	protected string $search = '';
	
	public function getKey(): string
	{
		return static::KEY;
	}

	public function generateWhere(): void
	{
		if($this->search) {
			$ids = Admin_Managers::FulltextSearch()->search(
				Product::getEntityType(),
				$this->search
			);
			
			if(!$ids) {
				$ids = [0];
			}
			
			
			$this->listing->addFilterWhere([
				'id'   => $ids,
			]);
		}
	}
}