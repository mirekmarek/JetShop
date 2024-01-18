<?php
/**
 *
 * @copyright 
 * @license  
 * @author  
 */
namespace JetApplicationModule\Admin\Services\Services;

use Jet\Application_Module;
use JetApplication\Admin_Entity_WithShopData_Manager_Interface;
use JetApplication\Admin_Entity_WithShopData_Manager_Trait;
use JetApplication\Admin_Entity_WithShopData_Interface;
use JetApplication\Entity_WithShopData;

/**
 *
 */
class Main extends Application_Module implements Admin_Entity_WithShopData_Manager_Interface
{
	use Admin_Entity_WithShopData_Manager_Trait;

	public const ADMIN_MAIN_PAGE = 'service';

	public const ACTION_GET = 'get_service';
	public const ACTION_ADD = 'add_service';
	public const ACTION_UPDATE = 'update_service';
	public const ACTION_DELETE = 'delete_service';
	
	public static function getEntityInstance(): Entity_WithShopData|Admin_Entity_WithShopData_Interface
	{
		return new Service();
	}
	
	public static function getEntityNameReadable() : string
	{
		return 'Service';
	}

}