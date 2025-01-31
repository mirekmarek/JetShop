<?php
/**
 * @copyright Copyright (c) Miroslav Marek <mirek.marek@web-jet.cz>
 * @license EUPL 1.2  https://eupl.eu/1.2/en/
 * @author Miroslav Marek <mirek.marek@web-jet.cz>
 */
namespace JetApplicationModule\Admin\WarehouseManagement\LossOrDestruction;


use JetApplication\Admin_Managers_WarehouseManagement_LossOrDestruction;
use JetApplication\EShopEntity_Basic;
use JetApplication\WarehouseManagement_LossOrDestruction;


class Main extends Admin_Managers_WarehouseManagement_LossOrDestruction
{
	public const ADMIN_MAIN_PAGE = 'loss-or-destruction';
	
	public static function getEntityInstance(): EShopEntity_Basic
	{
		return new WarehouseManagement_LossOrDestruction();
	}
	
	public static function getCurrentUserCanDelete() : bool
	{
		return false;
	}
	
}