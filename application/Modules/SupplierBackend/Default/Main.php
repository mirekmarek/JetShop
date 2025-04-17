<?php
/**
 * @copyright Copyright (c) Miroslav Marek <mirek.marek@web-jet.cz>
 * @license EUPL 1.2  https://eupl.eu/1.2/en/
 * @author Miroslav Marek <mirek.marek@web-jet.cz>
 */
namespace JetApplicationModule\SupplierBackend\Default;


use JetApplication\Supplier_Backend_Module;
use JetApplication\Supplier_GoodsOrder;


class Main extends Supplier_Backend_Module
{
	
	public function sendOrder( Supplier_GoodsOrder $order, string &$error_message ): bool
	{
		// TODO: Implement sendOrder() method.
		return true;
		
		//$error_message = 'test error';
		//return false;
	}
}