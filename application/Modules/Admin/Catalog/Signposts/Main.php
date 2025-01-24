<?php
namespace JetApplicationModule\Admin\Catalog\Signposts;

use JetApplication\Admin_EntityManager_WithEShopData_Interface;
use JetApplication\Admin_EntityManager_WithEShopData_Trait;

use Jet\Application_Module;
use JetApplication\Admin_Entity_WithEShopData_Interface;
use JetApplication\Entity_WithEShopData;
use JetApplication\Signpost;
use JetApplication\Admin_Managers_Signpost;

/**
 *
 */
class Main extends Application_Module implements Admin_EntityManager_WithEShopData_Interface, Admin_Managers_Signpost
{
	use Admin_EntityManager_WithEShopData_Trait;

	public const ADMIN_MAIN_PAGE = 'signposts';

	public const ACTION_GET = 'get_signpost';
	public const ACTION_ADD = 'add_signpost';
	public const ACTION_UPDATE = 'update_signpost';
	public const ACTION_DELETE = 'delete_signpost';
	
	
	public static function getEntityInstance(): Entity_WithEShopData|Admin_Entity_WithEShopData_Interface
	{
		return new Signpost();
	}
	
	public static function getEntityNameReadable() : string
	{
		return 'Signpost';
	}

}