<?php
/**
 *
 * @copyright 
 * @license  
 * @author  
 */
namespace JetApplicationModule\Admin\OrderPersonalReceipt\Overview;

use Jet\Application_Module;
use JetApplication\Admin_EntityManager_Interface;
use JetApplication\Admin_EntityManager_Trait;
use JetApplication\EShopEntity_Basic;
use JetApplication\OrderPersonalReceipt;

/**
 *
 */
class Main extends Application_Module implements Admin_EntityManager_Interface
{
	use Admin_EntityManager_Trait;
	
	public const ADMIN_MAIN_PAGE = 'order-dispatch-overview';

	public const ACTION_GET = 'get_order_dispatch';
	
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
	
	public static function getEntityNameReadable(): string
	{
		return 'Order Personal Receipt dispatch';
	}
	
	
}