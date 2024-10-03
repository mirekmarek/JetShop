<?php
/**
 *
 * @copyright
 * @license
 * @author
 */
namespace JetShop;

use Jet\MVC_Controller_Default;
use JetApplication\MarketplaceIntegration_Module;
use JetApplication\Order;

/**
 *
 */
abstract class Core_MarketplaceIntegration_Module_Controller_OrderDetail extends MVC_Controller_Default
{
	
	protected Order $order;
	
	protected MarketplaceIntegration_Module $marketplace;
	
	public function init(
		Order $order,
		MarketplaceIntegration_Module $marketplace
	): void
	{
		$this->order = $order;
		$this->marketplace = $marketplace;
	}
	

	
	public function getOrder(): Order
	{
		return $this->order;
	}
	
	public function getMarketplace(): MarketplaceIntegration_Module
	{
		return $this->marketplace;
	}
	
	public function resolve() : string|bool
	{
		return 'default';
	}
	
	public function default_Action() : void
	{
	}
	
	
}