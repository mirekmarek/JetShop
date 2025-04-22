<?php
/**
 * @copyright Copyright (c) Miroslav Marek <mirek.marek@web-jet.cz>
 * @license EUPL 1.2  https://eupl.eu/1.2/en/
 * @author Miroslav Marek <mirek.marek@web-jet.cz>
 */
namespace JetApplicationModule\Admin\Orders;

use JetApplication\Admin_Listing_Filter_StdFilter;
use JetApplication\Payment_Method;

class Listing_Filter_Payment extends Admin_Listing_Filter_StdFilter
{
	public const KEY = 'payment';
	protected string $label = 'Payment method';
	
	protected function getOptions() : array
	{
		return Payment_Method::getScope();
	}
	
	public function generateWhere(): void
	{
		if(!$this->value) {
			return;
		}
		
		$this->listing->addFilterWhere([
			'payment_method_id'   => $this->value,
		]);
	}
	
}