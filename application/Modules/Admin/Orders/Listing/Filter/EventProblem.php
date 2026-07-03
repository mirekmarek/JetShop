<?php
/**
 * @copyright Copyright (c) Miroslav Marek <mirek.marek@web-jet.cz>
 * @license EUPL 1.2  https://eupl.eu/1.2/en/
 * @author Miroslav Marek <mirek.marek@web-jet.cz>
 */
namespace JetApplicationModule\Admin\Orders;

use JetApplication\Admin_Listing_Filter_StdFilter_YesNo;
use JetApplication\Order_Event;

class Listing_Filter_EventProblem extends Admin_Listing_Filter_StdFilter_YesNo
{
	public const KEY = 'EventProblem';
	protected string $label = 'Event handling problem';
	
	public function generateWhere(): void
	{
		if(!$this->value) {
			return;
		}
		
		if($this->value==static::YES) {
			$ids = Order_Event::getProblematicOrderIds();
			
			if(!$ids) {
				$ids[] = 0;
			}
			
			$this->listing->addFilterWhere([
				'id' => $ids,
			]);
		}
		
	}
	
}