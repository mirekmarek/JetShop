<?php
namespace JetApplicationModule\Admin\Content\Article\Authors;

use JetApplication\Admin_Managers_ContentArticleAuthors;
use JetApplication\Admin_EntityManager_Trait;
use Jet\Application_Module;
use JetApplication\Content_Article_Author;
use JetApplication\Entity_Basic;

/**
 *
 */
class Main extends Application_Module implements Admin_Managers_ContentArticleAuthors
{
	use Admin_EntityManager_Trait;

	public const ADMIN_MAIN_PAGE = 'article-authors';

	public const ACTION_GET = 'get_author';
	public const ACTION_ADD = 'add_author';
	public const ACTION_UPDATE = 'update_author';
	public const ACTION_DELETE = 'delete_author';
	
	public static function getEntityInstance(): Entity_Basic
	{
		return new Content_Article_Author();
	}
	
	
}