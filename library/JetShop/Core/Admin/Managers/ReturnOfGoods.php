<?php
namespace JetShop;

use JetApplication\Admin_EntityManager_WithEShopRelation_Interface;
use JetApplication\ReturnOfGoods;
use JetApplication\Order;

interface Core_Admin_Managers_ReturnOfGoods extends Admin_EntityManager_WithEShopRelation_Interface
{
	public function showReturnOfGoodsStatus( ReturnOfGoods $return_of_goods ) : string;
	
	public function showOrderReturnsOfGoods( Order $order ) : void;
}