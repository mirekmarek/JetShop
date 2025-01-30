<?php
namespace JetApplicationModule\Admin\Content\Article\KindOf;

use JetApplication\Admin_EntityManager_Trait;
use Jet\Application_Module;
use JetApplication\Admin_Managers_Content_ArticleKindOfArticle;
use JetApplication\Content_Article_KindOfArticle;
use JetApplication\EShopEntity_Basic;

/**
 *
 */
class Main extends Application_Module implements Admin_Managers_Content_ArticleKindOfArticle
{
	use Admin_EntityManager_Trait;
	
	public const ADMIN_MAIN_PAGE = 'kind-of-article';
	
	public const ACTION_GET = 'get_kind_od_article';
	public const ACTION_ADD = 'add_kind_od_article';
	public const ACTION_UPDATE = 'update_kind_od_article';
	public const ACTION_DELETE = 'delete_kind_od_article';
	
	
	public static function getEntityInstance(): EShopEntity_Basic
	{
		return new Content_Article_KindOfArticle();
	}
	
	
}