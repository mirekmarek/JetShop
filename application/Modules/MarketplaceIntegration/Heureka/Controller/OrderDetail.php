<?php
/**
 * @copyright Copyright (c) Miroslav Marek <mirek.marek@web-jet.cz>
 * @license EUPL 1.2  https://eupl.eu/1.2/en/
 * @author Miroslav Marek <mirek.marek@web-jet.cz>
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
		
		$mp->getClient( $this->order->getEshop() )->getHeurekaOrderStatus( $this->order->getId() );
	}
}