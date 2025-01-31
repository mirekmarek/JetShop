<?php
/**
 * @copyright Copyright (c) Miroslav Marek <mirek.marek@web-jet.cz>
 * @license EUPL 1.2  https://eupl.eu/1.2/en/
 * @author Miroslav Marek <mirek.marek@web-jet.cz>
 */
namespace JetShop;


use JetApplication\Admin_EntityManager_Module;
use JetApplication\ReturnOfGoods;
use JetApplication\Order;

abstract class Core_Admin_Managers_ReturnOfGoods extends Admin_EntityManager_Module
{
	abstract public function showReturnOfGoodsStatus( ReturnOfGoods $return_of_goods ) : string;
	
	abstract public function showOrderReturnsOfGoods( Order $order ) : void;
}