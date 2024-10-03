<?php
namespace JetApplicationModule\Admin\Content\Article\Articles;

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

	public const ADMIN_MAIN_PAGE = 'articles';

	public const ACTION_GET = 'get_article';
	public const ACTION_ADD = 'add_article';
	public const ACTION_UPDATE = 'update_article';
	public const ACTION_DELETE = 'delete_article';
	
	public static function getEntityNameReadable() : string
	{
		return 'article';
	}
	
	public static function getEntityInstance(): Entity_WithShopData|Admin_Entity_WithShopData_Interface
	{
		return new Article();
	}
	
}