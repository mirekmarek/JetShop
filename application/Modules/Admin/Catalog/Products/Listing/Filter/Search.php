<?php
namespace JetShopModule\Admin\Catalog\Products;

use Jet\Data_Listing_Filter_Search;
use JetShop\Fulltext_Index_Internal_Product;

class Listing_Filter_Search extends Data_Listing_Filter_Search {
	
	/**
	 *
	 */
	public function generateWhere(): void
	{
		if($this->search) {
			$ids = Fulltext_Index_Internal_Product::search(
				search_string: $this->search,
				only_ids: true
			);
			
			if(!$ids) {
				$ids = [0];
			}
			
			
			$this->listing->addWhere([
				'id'   => $ids,
			]);
		}
	}
}