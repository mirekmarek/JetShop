<?php
/**
 * @copyright Copyright (c) Miroslav Marek <mirek.marek@web-jet.cz>
 * @license EUPL 1.2  https://eupl.eu/1.2/en/
 * @author Miroslav Marek <mirek.marek@web-jet.cz>
 */
namespace JetApplicationModule\Admin\WarehouseManagement\StockVerification;


use JetApplication\Admin_Managers_WarehouseManagement_StockVerification;
use JetApplication\EShopEntity_Basic;
use JetApplication\WarehouseManagement_StockVerification;


class Main extends Admin_Managers_WarehouseManagement_StockVerification
{
	public const ADMIN_MAIN_PAGE = 'stock-verification';
	
	
	public static function getEntityInstance(): EShopEntity_Basic
	{
		return new WarehouseManagement_StockVerification();
	}
	
	public static function getCurrentUserCanDelete() : bool
	{
		return false;
	}
	
}