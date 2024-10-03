<?php
namespace JetShop;

use JetApplication\ReturnOfGoods;
use JetApplication\Order;

interface Core_Admin_Managers_ReturnOfGoods
{
	public function showName( int $id ): string;
	
	public function showReturnOfGoodsStatus( ReturnOfGoods $return_of_goods ) : string;
	
	public function showOrderReturnsOfGoods( Order $order ) : void;
}