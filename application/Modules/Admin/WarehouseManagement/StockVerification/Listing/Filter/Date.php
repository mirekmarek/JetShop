<?php
/**
 * @copyright Copyright (c) Miroslav Marek <mirek.marek@web-jet.cz>
 * @license EUPL 1.2  https://eupl.eu/1.2/en/
 * @author Miroslav Marek <mirek.marek@web-jet.cz>
 */
namespace JetApplicationModule\Admin\WarehouseManagement\StockVerification;


use JetApplication\Admin_Listing_Filter_DateInterval;

class Listing_Filter_Date extends Admin_Listing_Filter_DateInterval
{
	public const KEY = 'date';
	protected string $label = 'Date';
	
	public function generateWhere(): void
	{
		if($this->from) {
			$this->listing->addFilterWhere([
				'date >='   => $this->from,
			]);
		}
		
		if($this->till) {
			$this->listing->addFilterWhere([
				'date <='   => $this->till,
			]);
		}
		
		
	}
	
}