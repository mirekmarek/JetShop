<?php
/**
 * @copyright Copyright (c) Miroslav Marek <mirek.marek@web-jet.cz>
 * @license EUPL 1.2  https://eupl.eu/1.2/en/
 * @author Miroslav Marek <mirek.marek@web-jet.cz>
 */
namespace JetApplicationModule\Admin\Orders;

use JetApplication\Admin_Listing_Filter_DateInterval;

class Listing_Filter_DatePurchased extends Admin_Listing_Filter_DateInterval
{
	public const KEY = 'date_purchased';
	protected string $label = 'Date purchased';
	
	public function generateWhere(): void
	{
		if($this->from) {
			$this->listing->addFilterWhere([
				'date_purchased >='   => $this->from,
			]);
		}
		
		if($this->till) {
			$this->listing->addFilterWhere([
				'date_purchased <='   => $this->till,
			]);
		}
	}

}