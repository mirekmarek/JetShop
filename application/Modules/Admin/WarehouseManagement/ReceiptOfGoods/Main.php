<?php
/**
 * @copyright Copyright (c) Miroslav Marek <mirek.marek@web-jet.cz>
 * @license EUPL 1.2  https://eupl.eu/1.2/en/
 * @author Miroslav Marek <mirek.marek@web-jet.cz>
 */
namespace JetApplicationModule\Admin\WarehouseManagement\ReceiptOfGoods;


use JetApplication\Application_Service_Admin_ReceiptOfGoods;
use JetApplication\EShopEntity_Basic;
use JetApplication\WarehouseManagement_ReceiptOfGoods;


class Main extends Application_Service_Admin_ReceiptOfGoods
{
	public const ADMIN_MAIN_PAGE = 'receipt-of-goods';
	
	public static function getEntityInstance(): EShopEntity_Basic
	{
		return new WarehouseManagement_ReceiptOfGoods();
	}
	
	public static function getCurrentUserCanDelete() : bool
	{
		return false;
	}
	
}