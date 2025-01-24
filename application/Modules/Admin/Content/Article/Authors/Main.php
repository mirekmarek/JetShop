<?php
namespace JetApplicationModule\Admin\Content\Article\Authors;

use JetApplication\Admin_Managers_ContentArticleAuthors;
use JetApplication\Admin_EntityManager_WithEShopData_Trait;
use Jet\Application_Module;
use JetApplication\Admin_Entity_WithEShopData_Interface;
use JetApplication\Content_Article_Author;
use JetApplication\Entity_WithEShopData;

/**
 *
 */
class Main extends Application_Module implements Admin_Managers_ContentArticleAuthors
{
	use Admin_EntityManager_WithEShopData_Trait;

	public const ADMIN_MAIN_PAGE = 'article-authors';

	public const ACTION_GET = 'get_author';
	public const ACTION_ADD = 'add_author';
	public const ACTION_UPDATE = 'update_author';
	public const ACTION_DELETE = 'delete_author';
	
	public static function getEntityNameReadable() : string
	{
		return 'Content - article author';
	}
	
	public static function getEntityInstance(): Entity_WithEShopData|Admin_Entity_WithEShopData_Interface
	{
		return new Content_Article_Author();
	}
	
	
}