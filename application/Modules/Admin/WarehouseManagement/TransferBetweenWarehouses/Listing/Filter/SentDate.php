<?php
/**
 * @copyright Copyright (c) Miroslav Marek <mirek.marek@web-jet.cz>
 * @license EUPL 1.2  https://eupl.eu/1.2/en/
 * @author Miroslav Marek <mirek.marek@web-jet.cz>
 */
namespace JetApplicationModule\Admin\WarehouseManagement\TransferBetweenWarehouses;

use JetApplication\Admin_Listing_Filter_DateInterval;

class Listing_Filter_SentDate extends Admin_Listing_Filter_DateInterval
{
	public const KEY = 'sent_date';
	protected string $label = 'Sent date';
	
	public function generateWhere(): void
	{
		if($this->from) {
			$this->listing->addFilterWhere([
				'sent_date_time >='   => $this->from,
			]);
		}
		
		if($this->till) {
			$this->listing->addFilterWhere([
				'sent_date_time <='   => $this->till,
			]);
		}
		
		
	}
	
}