<?php
/**
 * @copyright Copyright (c) Miroslav Marek <mirek.marek@web-jet.cz>
 * @license EUPL 1.2  https://eupl.eu/1.2/en/
 * @author Miroslav Marek <mirek.marek@web-jet.cz>
 */
namespace JetApplicationModule\Admin\Orders;

use JetApplication\Admin_Listing_Filter_StdFilter;
use JetApplication\Delivery_Method;

class Listing_Filter_Delivery extends Admin_Listing_Filter_StdFilter
{
	public const KEY = 'delivery';
	
	protected string $label = 'Delivery method';
	
	protected function getOptions(): array
	{
		return Delivery_Method::getScope();
	}
	
	public function generateWhere(): void
	{
		if(!$this->value) {
			return;
		}
		
		$this->listing->addFilterWhere([
			'delivery_method_id'   => $this->value,
		]);
	}
	
}