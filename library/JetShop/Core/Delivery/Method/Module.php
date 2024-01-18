<?php
/**
 *
 */

namespace JetShop;

use Jet\Application_Module;

use JetApplication\Delivery_Method_ShopData;
use JetApplication\Order;

abstract class Core_Delivery_Method_Module extends Application_Module
{

	public function init( Delivery_Method_ShopData $delivery_method ) : void
	{
	}

	public function getOrderConfirmationEmailInfoText( Order $order, Delivery_Method_ShopData $deliver_method ) : string
	{
		return $deliver_method->getConfirmationEmailInfoText();
	}

}