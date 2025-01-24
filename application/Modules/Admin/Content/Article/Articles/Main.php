<?php
namespace JetApplicationModule\Admin\Content\Article\Articles;

use JetApplication\Admin_EntityManager_WithEShopData_Trait;
use Jet\Application_Module;
use JetApplication\Admin_Entity_WithEShopData_Interface;
use JetApplication\Admin_Managers_ContentArticles;
use JetApplication\Entity_WithEShopData;
use JetApplication\Content_Article;

/**
 *
 */
class Main extends Application_Module implements Admin_Managers_ContentArticles
{
	use Admin_EntityManager_WithEShopData_Trait;

	public const ADMIN_MAIN_PAGE = 'articles';

	public const ACTION_GET = 'get_article';
	public const ACTION_ADD = 'add_article';
	public const ACTION_UPDATE = 'update_article';
	public const ACTION_DELETE = 'delete_article';
	
	public static function getEntityNameReadable() : string
	{
		return 'Content - Article';
	}
	
	public static function getEntityInstance(): Entity_WithEShopData|Admin_Entity_WithEShopData_Interface
	{
		return new Content_Article();
	}
	
}