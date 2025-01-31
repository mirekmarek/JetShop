<?php
/**
 * @copyright Copyright (c) Miroslav Marek <mirek.marek@web-jet.cz>
 * @license EUPL 1.2  https://eupl.eu/1.2/en/
 * @author Miroslav Marek <mirek.marek@web-jet.cz>
 */
namespace JetApplicaTionModule\Admin\SupplierGoodsOrders;


use JetApplication\EShopEntity_Basic;
use JetApplication\Admin_Managers_SupplierGoodsOrders;
use JetApplication\Supplier_GoodsOrder;


class Main extends Admin_Managers_SupplierGoodsOrders
{
	public const ADMIN_MAIN_PAGE = 'supplier-goods-orders';
	
	public static function getEntityInstance(): EShopEntity_Basic
	{
		return new Supplier_GoodsOrder();
	}
	
	public static function getCurrentUserCanDelete() : bool
	{
		return false;
	}

}