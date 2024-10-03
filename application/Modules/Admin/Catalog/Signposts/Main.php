<?php
namespace JetApplicationModule\Admin\Catalog\Signposts;

use JetApplication\Admin_EntityManager_WithShopData_Interface;
use JetApplication\Admin_EntityManager_WithShopData_Trait;

use Jet\Application_Module;
use JetApplication\Admin_Entity_WithShopData_Interface;
use JetApplication\Entity_WithShopData;

/**
 *
 */
class Main extends Application_Module implements Admin_EntityManager_WithShopData_Interface
{
	use Admin_EntityManager_WithShopData_Trait;

	public const ADMIN_MAIN_PAGE = 'signposts';

	public const ACTION_GET = 'get_signpost';
	public const ACTION_ADD = 'add_signpost';
	public const ACTION_UPDATE = 'update_signpost';
	public const ACTION_DELETE = 'delete_signpost';
	
	
	public static function getEntityInstance(): Entity_WithShopData|Admin_Entity_WithShopData_Interface
	{
		return new Signpost();
	}
	
	public static function getEntityNameReadable() : string
	{
		return 'signpost';
	}

}