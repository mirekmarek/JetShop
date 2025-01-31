<?php
/**
 * @copyright Copyright (c) Miroslav Marek <mirek.marek@web-jet.cz>
 * @license EUPL 1.2  https://eupl.eu/1.2/en/
 * @author Miroslav Marek <mirek.marek@web-jet.cz>
 */
namespace JetApplicationModule\Admin\OrderPersonalReceipt\Overview;


use JetApplication\Admin_EntityManager_Module;
use JetApplication\EShopEntity_Basic;
use JetApplication\OrderPersonalReceipt;


class Main extends Admin_EntityManager_Module
{
	
	public const ADMIN_MAIN_PAGE = 'order-dispatch-overview';
	
	public static function getCurrentUserCanCreate(): bool
	{
		return false;
	}
	
	public static function getCurrentUserCanDelete(): bool
	{
		return false;
	}
	
	public static function getEntityInstance(): EShopEntity_Basic
	{
		return new OrderPersonalReceipt();
	}
}