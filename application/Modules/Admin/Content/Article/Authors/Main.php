<?php
namespace JetApplicationModule\Admin\Content\Article\Authors;

use JetApplication\Admin_EntityManager_WithEShopData_Interface;
use JetApplication\Admin_EntityManager_WithEShopData_Trait;
use Jet\Application_Module;
use JetApplication\Admin_Entity_WithEShopData_Interface;
use JetApplication\Entity_WithEShopData;

/**
 *
 */
class Main extends Application_Module implements Admin_EntityManager_WithEShopData_Interface
{
	use Admin_EntityManager_WithEShopData_Trait;

	public const ADMIN_MAIN_PAGE = 'article-authors';

	public const ACTION_GET = 'get_author';
	public const ACTION_ADD = 'add_author';
	public const ACTION_UPDATE = 'update_author';
	public const ACTION_DELETE = 'delete_author';
	
	public static function getEntityNameReadable() : string
	{
		return 'article author';
	}
	
	public static function getEntityInstance(): Entity_WithEShopData|Admin_Entity_WithEShopData_Interface
	{
		return new Author();
	}
	
	
}