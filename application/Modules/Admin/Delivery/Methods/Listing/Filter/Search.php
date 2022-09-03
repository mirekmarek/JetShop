<?php
/**
 *
 * @copyright Copyright (c) Miroslav Marek <mirek.marek@web-jet.cz>
 * @license http://www.php-jet.net/license/license.txt
 * @author Miroslav Marek <mirek.marek@web-jet.cz>
 */

namespace JetShopModule\Admin\Delivery\Methods;

use Jet\Data_Listing_Filter_Search;

class Listing_Filter_Search extends Data_Listing_Filter_Search {
	
	/**
	 *
	 */
	public function generateWhere(): void
	{
		if( $this->search ) {
			$search = '%' . $this->search . '%';
			$this->listing->addWhere( [
				'internal_name *'   => $search,
			] );
		}
	}
}