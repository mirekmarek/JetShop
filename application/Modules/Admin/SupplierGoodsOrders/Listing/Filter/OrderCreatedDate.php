<?php
/**
 * @copyright Copyright (c) Miroslav Marek <mirek.marek@web-jet.cz>
 * @license EUPL 1.2  https://eupl.eu/1.2/en/
 * @author Miroslav Marek <mirek.marek@web-jet.cz>
 */
namespace JetApplicationModule\Admin\SupplierGoodsOrders;

use JetApplication\Admin_Listing_Filter_DateInterval;

class Listing_Filter_OrderCreatedDate extends Admin_Listing_Filter_DateInterval
{
	public const KEY = 'order_created_date';
	protected string $label = 'Order created';
	
	public function generateWhere(): void
	{
		if($this->from) {
			$this->listing->addFilterWhere([
				'order_created_date >='   => $this->from,
			]);
		}
		
		if($this->till) {
			$this->listing->addFilterWhere([
				'order_created_date <='   => $this->till,
			]);
		}
		
		
	}
	
}