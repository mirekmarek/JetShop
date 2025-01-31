<?php
/**
 * @copyright Copyright (c) Miroslav Marek <mirek.marek@web-jet.cz>
 * @license EUPL 1.2  https://eupl.eu/1.2/en/
 * @author Miroslav Marek <mirek.marek@web-jet.cz>
 */
namespace JetApplicaTionModule\Admin\WarehouseManagement\Warehouses;


use JetApplication\Admin_Managers_WarehouseManagement_Warehouses;
use JetApplication\EShopEntity_Basic;
use JetApplication\WarehouseManagement_Warehouse;


class Main extends Admin_Managers_WarehouseManagement_Warehouses
{
	public const ADMIN_MAIN_PAGE = 'warehouses';
	
	
	public static function getEntityInstance(): EShopEntity_Basic
	{
		return new WarehouseManagement_Warehouse();
	}
}