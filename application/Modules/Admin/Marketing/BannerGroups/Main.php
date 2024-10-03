<?php
namespace JetApplicationModule\Admin\Marketing\BannerGroups;

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

	public const ADMIN_MAIN_PAGE = 'banner-groups';

	public const ACTION_GET = 'get_banner_group';
	public const ACTION_ADD = 'add_banner_group';
	public const ACTION_UPDATE = 'update_banner_group';
	public const ACTION_DELETE = 'delete_banner_group';
	
	
	public static function getEntityInstance(): Entity_Common|Admin_Entity_Common_Interface
	{
		return new BannerGroup();
	}
	
	public static function getEntityNameReadable() : string
	{
		return 'banner group';
	}

}