<?php
/**
 *
 * @copyright 
 * @license  
 * @author  
 */
namespace JetApplicationModule\Admin\OrderDispatch\Overview;

use Jet\Application_Module;
use JetApplication\Admin_Entity_WithShopRelation_Interface;
use JetApplication\Admin_EntityManager_WithShopRelation_Interface;
use JetApplication\Admin_EntityManager_WithShopRelation_Trait;
use JetApplication\Entity_WithShopRelation;

/**
 *
 */
class Main extends Application_Module implements Admin_EntityManager_WithShopRelation_Interface
{
	use Admin_EntityManager_WithShopRelation_Trait;
	
	public const ADMIN_MAIN_PAGE = 'order-dispatch-overview';

	public const ACTION_GET = 'get_order_dispatch';
	
	
	public static function showActiveState( int $id ): string
	{
		return '';
	}
	
	public static function getCurrentUserCanCreate(): bool
	{
		return false;
	}
	
	public static function getCurrentUserCanDelete(): bool
	{
		return false;
	}
	
	public static function getEntityInstance(): Entity_WithShopRelation|Admin_Entity_WithShopRelation_Interface
	{
		return new OrderDispatch();
	}
	
	public static function getEntityNameReadable(): string
	{
		return 'Order dispatch';
	}
	
	
}