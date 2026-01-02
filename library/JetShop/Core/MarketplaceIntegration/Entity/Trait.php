<?php
/**
 * @copyright Copyright (c) Miroslav Marek <mirek.marek@web-jet.cz>
 * @license EUPL 1.2  https://eupl.eu/1.2/en/
 * @author Miroslav Marek <mirek.marek@web-jet.cz>
 */
namespace JetShop;

use Jet\DataModel;
use Jet\DataModel_Definition;
use JetApplication\EShops;
use JetApplication\MarketplaceIntegration_Marketplace;

trait Core_MarketplaceIntegration_Entity_Trait {
	
	#[DataModel_Definition(
		type: DataModel::TYPE_STRING,
		max_len: 100,
		is_key: true,
	)]
	protected string $marketplace_code = '';
	
	
	public function setMarketplace( MarketplaceIntegration_Marketplace $marketplace ): void
	{
		if(!$marketplace->getEshop()) {
			$this->setEshop( EShops::getDefault() );
		} else {
			$this->setEshop( $marketplace->getEshop() );
		}
		$this->marketplace_code = $marketplace->getMarketplaceCode();
	}
	
	public function getMarketplace(): MarketplaceIntegration_Marketplace
	{
		return new MarketplaceIntegration_Marketplace( $this->marketplace_code, $this->getEshop() );
	}
	
	public function getMarketplaceCode(): string
	{
		return $this->marketplace_code;
	}
	
	public function setMarketplaceCode( string $marketplace_code ): void
	{
		$this->marketplace_code = $marketplace_code;
	}
	
	

	
}