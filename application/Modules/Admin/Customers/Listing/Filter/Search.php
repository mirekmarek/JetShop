<?php
/**
 *
 * @copyright Copyright (c) Miroslav Marek <mirek.marek@web-jet.cz>
 * @license http://www.php-jet.net/license/license.txt
 * @author Miroslav Marek <mirek.marek@web-jet.cz>
 */

namespace JetShopModule\Admin\Customers;

use Jet\Data_Listing_Filter_Search;

class Listing_Filter_Search extends Data_Listing_Filter_Search {
	
	/**
	 *
	 */
	public function generateWhere(): void
	{
		if( $this->search ) {
			$search = explode(' ', preg_replace('!\s+!', ' ', $this->search));
			
			$where = [];
			foreach( $search as $s ) {
				$s = '%'.$s.'%';
				
				if($where) {
					$where[] = 'AND';
				}
				
				
				$where[] = [
					'email *'   => $s,
					'OR',
					'first_name *' => $s,
					'OR',
					'surname *' => $s,
					'OR',
					'phone_number *' => $s,
				];
			}
			
			
			$this->listing->addWhere($where);
		}
	}
}