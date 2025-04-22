<?php
/**
 * @copyright Copyright (c) Miroslav Marek <mirek.marek@web-jet.cz>
 * @license EUPL 1.2  https://eupl.eu/1.2/en/
 * @author Miroslav Marek <mirek.marek@web-jet.cz>
 */
namespace JetApplicationModule\Admin\DeliveryNotes;

use JetApplication\Admin_Listing_Filter_Customer;

class Listing_Filter_Customer extends Admin_Listing_Filter_Customer
{
	public const KEY = 'customer';
	
	public function generateWhere(): void
	{
		if(!$this->customer_id) {
			return;
		}
		
		$this->listing->addFilterWhere([
			'customer_id'   => $this->customer_id,
		]);
	}

}