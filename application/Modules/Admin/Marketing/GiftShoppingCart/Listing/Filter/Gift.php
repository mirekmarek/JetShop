<?php
/**
 * @copyright Copyright (c) Miroslav Marek <mirek.marek@web-jet.cz>
 * @license EUPL 1.2  https://eupl.eu/1.2/en/
 * @author Miroslav Marek <mirek.marek@web-jet.cz>
 */
namespace JetApplicationModule\Admin\Marketing\GiftShoppingCart;

use JetApplication\Admin_Listing_Filter_Product;

class Listing_Filter_Gift extends Admin_Listing_Filter_Product
{
	
	public const KEY = 'gift';
	protected string $label = 'Gift';
	
	
	public function generateWhere(): void
	{
		if( $this->product_id ) {
			$this->listing->addFilterWhere( [
				'gift_product_id' => $this->product_id,
			] );
		}
	}

}