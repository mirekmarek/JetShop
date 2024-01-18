<?php
namespace JetApplicationModule\Admin\Catalog\Stickers;

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

	public const ADMIN_MAIN_PAGE = 'stickers';

	public const ACTION_GET = 'get_sticker';
	public const ACTION_ADD = 'add_sticker';
	public const ACTION_UPDATE = 'update_sticker';
	public const ACTION_DELETE = 'delete_sticker';
	
	
	public static function getEntityInstance(): Entity_WithShopData|Admin_Entity_WithShopData_Interface
	{
		return new Sticker();
	}
	
	public static function getEntityNameReadable() : string
	{
		return 'sticker';
	}

}