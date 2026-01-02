<?php
namespace JetApplicationModule\Admin\Stats\Orders;

use JetApplication\EShop;

class Source_CommonEShop extends Source
{
	protected EShop $eshop;
	
	public function __construct( EShop $eshop )
	{
		$this->eshop = $eshop;
		$this->key = $eshop->getKey();
		$this->title = $eshop->getName();
		$this->output_currency = $eshop->getDefaultPricelist()->getCurrency();
	}
	
	public function getWhere(): array
	{
		if($this->eshop->getIsVirtual()) {
			return [
				$this->eshop->getWhere()
			];
		} else {
			return [
				$this->eshop->getWhere(),
				'AND',
				'import_source' => ''
			];
		}
	}
}