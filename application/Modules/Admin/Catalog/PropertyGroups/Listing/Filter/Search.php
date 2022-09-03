<?php
/**
 *
 * @copyright 
 * @license  
 * @author  
 */

namespace JetShopModule\Admin\Catalog\PropertyGroups;


use Jet\Data_Listing_Filter_Search;

class Listing_Filter_Search extends Data_Listing_Filter_Search {

	/**
	 *
	 */
	public function generateWhere(): void
	{
		if($this->search) {
			$search = '%'.$this->search.'%';
			$this->listing->addWhere([
				'internal_notes *'   => $search,
			]);
		}
	}
}