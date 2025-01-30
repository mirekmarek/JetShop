<?php
namespace JetApplicationModule\Admin\Content\Article\Articles;

use JetApplication\Admin_EntityManager_Trait;
use Jet\Application_Module;
use JetApplication\Admin_Managers_Content_Articles;
use JetApplication\EShopEntity_Basic;
use JetApplication\Content_Article;

/**
 *
 */
class Main extends Application_Module implements Admin_Managers_Content_Articles
{
	use Admin_EntityManager_Trait;

	public const ADMIN_MAIN_PAGE = 'articles';

	public const ACTION_GET = 'get_article';
	public const ACTION_ADD = 'add_article';
	public const ACTION_UPDATE = 'update_article';
	public const ACTION_DELETE = 'delete_article';
	
	public static function getEntityInstance(): EShopEntity_Basic
	{
		return new Content_Article();
	}
	
}