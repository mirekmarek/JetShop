<?php
/**
 * @copyright Copyright (c) Miroslav Marek <mirek.marek@web-jet.cz>
 * @license EUPL 1.2  https://eupl.eu/1.2/en/
 * @author Miroslav Marek <mirek.marek@web-jet.cz>
 */
namespace JetApplicationModule\Admin\Complaints;

use JetApplication\Admin_Listing_Filter_Product;

class Listing_Filter_Product extends Admin_Listing_Filter_Product
{
	public const KEY = 'product';
	protected string $label = 'Product';
	
	public function generateWhere(): void
	{
		if(!$this->product_id) {
			return;
		}
		
		$this->listing->addFilterWhere([
			'product_id'   => $this->product_id,
		]);
	}

}