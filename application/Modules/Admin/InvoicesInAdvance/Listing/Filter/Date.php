<?php
/**
 * @copyright Copyright (c) Miroslav Marek <mirek.marek@web-jet.cz>
 * @license EUPL 1.2  https://eupl.eu/1.2/en/
 * @author Miroslav Marek <mirek.marek@web-jet.cz>
 */
namespace JetApplicationModule\Admin\InvoicesInAdvance;

use JetApplication\Admin_Listing_Filter_DateInterval;

class Listing_Filter_Date extends Admin_Listing_Filter_DateInterval
{
	public const KEY = 'date';
	protected string $label = 'Invoice date';
	
	public function generateWhere(): void
	{
		if($this->from) {
			$this->listing->addFilterWhere([
				'invoice_date >='   => $this->from,
			]);
		}
		
		if($this->till) {
			$this->listing->addFilterWhere([
				'invoice_date <='   => $this->till,
			]);
		}
	}
}