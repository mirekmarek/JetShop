<?php
namespace JetApplicationModule\Admin\Stats\Orders;

use JetApplication\EShops;

class Source_All extends Source
{
	public function __construct()
	{
		$this->key = 'all';
		$this->title = 'All';
		$this->output_currency = EShops::getDefault()->getDefaultPricelist()->getCurrency();
	}
	
	public function getWhere(): array
	{
		return [];
	}
}