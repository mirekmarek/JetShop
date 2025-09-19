<?php
/**
 * @copyright Copyright (c) Miroslav Marek <mirek.marek@web-jet.cz>
 * @license EUPL 1.2  https://eupl.eu/1.2/en/
 * @author Miroslav Marek <mirek.marek@web-jet.cz>
 */
namespace JetShop;

use Jet\BaseObject;
use JetApplication\EShop;

abstract class Core_MarketplaceIntegration_Marketplace extends BaseObject
{
	protected string $marketplace_code;
	protected ?EShop $eshop;
	
	public function __construct( string $marketplace_code, ?EShop $eshop )
	{
		$this->marketplace_code = $marketplace_code;
		$this->eshop = $eshop;
	}
	
	public function getMarketplaceCode(): string
	{
		return $this->marketplace_code;
	}
	
	public function getEshop(): ?EShop
	{
		return $this->eshop;
	}
	
	public function getWhere() : array
	{
		$where = [
			'marketplace_code' => $this->getMarketplaceCode(),
		];
		
		if($this->eshop) {
			$where[] = 'AND';
			$where[] = $this->getEshop()->getWhere();
		}
		
		return $where;
	}
	
	public function getKey() : string
	{
		if($this->eshop) {
			return $this->marketplace_code.':'.$this->eshop->getKey();
		} else {
			return $this->marketplace_code;
		}
	}
}