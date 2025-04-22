<?php
/**
 * @copyright Copyright (c) Miroslav Marek <mirek.marek@web-jet.cz>
 * @license EUPL 1.2  https://eupl.eu/1.2/en/
 * @author Miroslav Marek <mirek.marek@web-jet.cz>
 */
namespace JetApplicationModule\Admin\Orders;

use JetApplication\Admin_Listing_Filter_StdFilter_YesNo;

class Listing_Filter_Paid extends Admin_Listing_Filter_StdFilter_YesNo
{
	public const KEY = 'paid';
	
	protected string $label = 'Paid';
	
	
	public function generateWhere(): void
	{
		if(!$this->value) {
			return;
		}
		
		$this->listing->addFilterWhere([
			'paid' => $this->value==static::YES,
		]);
		
	}
}