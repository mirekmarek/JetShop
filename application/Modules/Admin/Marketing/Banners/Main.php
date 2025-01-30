<?php
/**
 *
 * @copyright 
 * @license  
 * @author  
 */
namespace JetApplicationModule\Admin\Marketing\Banners;

use Jet\Application_Module;
use JetApplication\Admin_EntityManager_Trait;
use JetApplication\Admin_Managers_Marketing_Banners;
use JetApplication\EShopEntity_Basic;
use JetApplication\Marketing_Banner;

/**
 *
 */
class Main extends Application_Module implements Admin_Managers_Marketing_Banners
{
	use Admin_EntityManager_Trait;
	
	public const ADMIN_MAIN_PAGE = 'banners';

	public const ACTION_GET = 'get_banner';
	public const ACTION_ADD = 'add_banner';
	public const ACTION_UPDATE = 'update_banner';
	public const ACTION_DELETE = 'delete_banner';
	
	public static function getEntityInstance(): EShopEntity_Basic
	{
		return new Marketing_Banner();
	}

}