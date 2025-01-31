<?php
/**
 * @copyright Copyright (c) Miroslav Marek <mirek.marek@web-jet.cz>
 * @license EUPL 1.2  https://eupl.eu/1.2/en/
 * @author Miroslav Marek <mirek.marek@web-jet.cz>
 */
namespace JetApplicaTionModule\Admin\WarehouseManagement\ReceiptOfGoods;


use JetApplication\Admin_Managers_ReceiptOfGoods;
use JetApplication\EShopEntity_Basic;
use JetApplication\WarehouseManagement_ReceiptOfGoods;


class Main extends Admin_Managers_ReceiptOfGoods
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