<?php
/**
 *
 * @copyright
 * @license
 * @author
 */
namespace JetApplicationModule\MarketplaceIntegration\Heureka;

use JetApplication\MarketplaceIntegration_Module_Controller_OrderDetail;


class Controller_OrderDetail extends MarketplaceIntegration_Module_Controller_OrderDetail
{
	
	public function default_Action() : void
	{
		/**
		 * @var Main $mp
		 */
		$mp = $this->marketplace;
		
		$mp->getClient( $this->order->getShop() )->getHeurekaOrderStatus( $this->order->getId() );
	}
}