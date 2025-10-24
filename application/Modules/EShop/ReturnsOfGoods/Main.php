<?php
/**
 * @copyright Copyright (c) Miroslav Marek <mirek.marek@web-jet.cz>
 * @license EUPL 1.2  https://eupl.eu/1.2/en/
 * @author Miroslav Marek <mirek.marek@web-jet.cz>
 */
namespace JetApplicationModule\EShop\ReturnsOfGoods;


use JetApplication\Application_Service_EShop_ReturnOfGoods;
use JetApplication\EShop_ModuleUsingTemplate_Interface;
use JetApplication\EShop_ModuleUsingTemplate_Trait;
use JetApplication\EShop_Pages;
use JetApplication\Order;
use JetApplication\Order_Item;


class Main extends Application_Service_EShop_ReturnOfGoods implements EShop_ModuleUsingTemplate_Interface
{
	use EShop_ModuleUsingTemplate_Trait;
	
	public function generateCreateURL( Order $order, Order_Item $order_item ): false|string
	{
		if( !$order_item->isPhysicalProduct() ) {
			return false;
		}
		
		return EShop_Pages::ReturnOfGoods( $order->getEshop() )->getURL(
			GET_params: [
				'order' => $order->getKey(),
				'product_id' => $order_item->getItemId(),
				'm' => sha1($order->getEmail())
			]
		);

	}
}