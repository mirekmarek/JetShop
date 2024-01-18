<?php
namespace JetApplicationModule\Admin\Catalog\Brands;

use JetApplication\Admin_Entity_WithShopData_Manager_Interface;
use JetApplication\Admin_Entity_WithShopData_Manager_Trait;
use Jet\Application_Module;
use JetApplication\Admin_Entity_WithShopData_Interface;
use JetApplication\Entity_WithShopData;

/**
 *
 */
class Main extends Application_Module implements Admin_Entity_WithShopData_Manager_Interface
{
	use Admin_Entity_WithShopData_Manager_Trait;

	public const ADMIN_MAIN_PAGE = 'brands';

	public const ACTION_GET = 'get_brand';
	public const ACTION_ADD = 'add_brand';
	public const ACTION_UPDATE = 'update_brand';
	public const ACTION_DELETE = 'delete_brand';
	
	public static function getEntityNameReadable() : string
	{
		return 'brand';
	}
	
	public static function getEntityInstance(): Entity_WithShopData|Admin_Entity_WithShopData_Interface
	{
		return new Brand();
	}
	
}