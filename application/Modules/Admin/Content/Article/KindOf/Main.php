<?php
namespace JetApplicationModule\Admin\Content\Article\KindOf;

use JetApplication\Admin_EntityManager_Common_Interface;
use JetApplication\Admin_EntityManager_Common_Trait;
use Jet\Application_Module;
use JetApplication\Admin_Entity_Common_Interface;
use JetApplication\Entity_Common;

/**
 *
 */
class Main extends Application_Module implements Admin_EntityManager_Common_Interface
{
	use Admin_EntityManager_Common_Trait;

	public const ADMIN_MAIN_PAGE = 'kind-of-article';

	public const ACTION_GET = 'get_kind_od_article';
	public const ACTION_ADD = 'add_kind_od_article';
	public const ACTION_UPDATE = 'update_kind_od_article';
	public const ACTION_DELETE = 'delete_kind_od_article';
	
	
	public static function getEntityInstance(): Entity_Common|Admin_Entity_Common_Interface
	{
		return new KindOfArticle();
	}
	
	public static function getEntityNameReadable() : string
	{
		return 'kind of article';
	}

}