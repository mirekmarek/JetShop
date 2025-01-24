<?php
/**
 *
 * @copyright 
 * @license  
 * @author  
 */
namespace JetApplicationModule\Admin\Delivery\Classes;

use Jet\Application_Module;
use JetApplication\Admin_Managers_DeliveryClasses;
use JetApplication\Delivery_Class;
use JetApplication\Entity_Common;
use JetApplication\Admin_Entity_Common_Interface;
use JetApplication\Admin_EntityManager_Common_Trait;

/**
 *
 */
class Main extends Application_Module implements Admin_Managers_DeliveryClasses
{
	use Admin_EntityManager_Common_Trait;

	public const ADMIN_MAIN_PAGE = 'delivery-class';

	public const ACTION_GET = 'get_delivery_class';
	public const ACTION_ADD = 'add_delivery_class';
	public const ACTION_UPDATE = 'update_delivery_class';
	public const ACTION_DELETE = 'delete_delivery_class';
	
	
	public static function getEntityInstance(): Entity_Common|Admin_Entity_Common_Interface
	{
		return new Delivery_Class();
	}
	
	public static function getEntityNameReadable() : string
	{
		return 'Delivery class';
	}
	
}