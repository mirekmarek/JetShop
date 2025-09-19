<?php
/**
 * @copyright Copyright (c) Miroslav Marek <mirek.marek@web-jet.cz>
 * @license EUPL 1.2  https://eupl.eu/1.2/en/
 * @author Miroslav Marek <mirek.marek@web-jet.cz>
 */
namespace JetShop;

use JetApplication\MarketplaceIntegration_Marketplace;

interface Core_MarketplaceIntegration_Entity_Interface {
	
	public function setMarketplace( MarketplaceIntegration_Marketplace $marketplace ): void;
	
	public function getMarketplace(): MarketplaceIntegration_Marketplace;
	
	public function getMarketplaceCode(): string;

}