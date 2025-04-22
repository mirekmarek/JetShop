<?php
/**
 * @copyright Copyright (c) Miroslav Marek <mirek.marek@web-jet.cz>
 * @license EUPL 1.2  https://eupl.eu/1.2/en/
 * @author Miroslav Marek <mirek.marek@web-jet.cz>
 */
namespace JetApplicationModule\Admin\OrderPersonalReceipt\Overview;

use JetApplication\Admin_Listing_Filter_StdFilter;
use JetApplication\Carrier;


class Listing_Filter_Carrier extends Admin_Listing_Filter_StdFilter
{
	public const KEY = 'carrier';
	protected string $label = 'Carrier';
	
	protected function getOptions() : array
	{
		return Carrier::getScope();
	}
	
	public function generateWhere(): void
	{
		if(!$this->value) {
			return;
		}
		
		$this->listing->addFilterWhere([
			'carrier_code'   => $this->value,
		]);
	}
	
}