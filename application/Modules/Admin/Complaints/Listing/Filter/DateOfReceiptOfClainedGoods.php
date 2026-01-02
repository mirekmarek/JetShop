<?php
/**
 * @copyright Copyright (c) Miroslav Marek <mirek.marek@web-jet.cz>
 * @license EUPL 1.2  https://eupl.eu/1.2/en/
 * @author Miroslav Marek <mirek.marek@web-jet.cz>
 */
namespace JetApplicationModule\Admin\Complaints;

use JetApplication\Admin_Listing_Filter_DateInterval;

class Listing_Filter_DateOfReceiptOfClainedGoods extends Admin_Listing_Filter_DateInterval
{
	public const KEY = 'date_of_receipt_of_clained_goods';
	protected string $label = 'Date of receipt of clained goods';
	
	public function generateWhere(): void
	{
		if($this->from) {
			$this->from->setTime(0, 0, 0);
			$this->from->setOnlyDate(false);
			
			$this->listing->addFilterWhere([
				'date_of_receipt_of_clained_goods >=' => $this->from,
			]);
		}
		
		if($this->till) {
			$this->till->setTime(23, 59, 59);
			$this->till->setOnlyDate(false);
			$this->listing->addFilterWhere([
				'date_of_receipt_of_clained_goods <=' => $this->till,
			]);
		}
	}
}