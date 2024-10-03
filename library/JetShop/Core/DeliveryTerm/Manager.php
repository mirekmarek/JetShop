<?php
namespace JetShop;

use JetApplication\Availabilities_Availability;
use JetApplication\DeliveryTerm_Info;
use JetApplication\Order;
use JetApplication\Product_ShopData;

interface Core_DeliveryTerm_Manager {
	
	public function getInfo( Product_ShopData $product, ?Availabilities_Availability $availability=null ) : DeliveryTerm_Info;
	
	public function setupOrder( Order $order ) : void;
}