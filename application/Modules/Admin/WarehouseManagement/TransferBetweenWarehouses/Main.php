<?php
/**
 * @copyright Copyright (c) Miroslav Marek <mirek.marek@web-jet.cz>
 * @license EUPL 1.2  https://eupl.eu/1.2/en/
 * @author Miroslav Marek <mirek.marek@web-jet.cz>
 */
namespace JetApplicationModule\Admin\WarehouseManagement\TransferBetweenWarehouses;


use JetApplication\Application_Service_Admin_WarehouseManagement_TransferBetweenWarehouses;
use JetApplication\EShopEntity_Basic;
use JetApplication\WarehouseManagement_TransferBetweenWarehouses;


class Main extends Application_Service_Admin_WarehouseManagement_TransferBetweenWarehouses
{
	public const ADMIN_MAIN_PAGE = 'transfer-between-warehouses';
	
	public static function getEntityInstance(): EShopEntity_Basic
	{
		return new WarehouseManagement_TransferBetweenWarehouses();
	}
	
	public static function getCurrentUserCanDelete() : bool
	{
		return false;
	}
	
}