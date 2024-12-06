<?php
namespace JetShop;

use JetApplication\Availability;
use JetApplication\DeliveryTerm_Info;
use JetApplication\Order;
use JetApplication\Product_EShopData;

interface Core_DeliveryTerm_Manager {
	
	public function getInfo( Product_EShopData $product, ?Availability $availability=null ) : DeliveryTerm_Info;
	
	public function setupOrder( Order $order ) : void;
}