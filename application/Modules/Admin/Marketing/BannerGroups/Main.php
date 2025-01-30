<?php
namespace JetApplicationModule\Admin\Marketing\BannerGroups;

use JetApplication\Admin_Managers_Marketing_BannerGroups;
use JetApplication\Admin_EntityManager_Trait;
use Jet\Application_Module;
use JetApplication\EShopEntity_Basic;
use JetApplication\Marketing_BannerGroup;

/**
 *
 */
class Main extends Application_Module implements Admin_Managers_Marketing_BannerGroups
{
	use Admin_EntityManager_Trait;

	public const ADMIN_MAIN_PAGE = 'banner-groups';

	public const ACTION_GET = 'get_banner_group';
	public const ACTION_ADD = 'add_banner_group';
	public const ACTION_UPDATE = 'update_banner_group';
	public const ACTION_DELETE = 'delete_banner_group';
	
	
	public static function getEntityInstance(): EShopEntity_Basic
	{
		return new Marketing_BannerGroup();
	}
}