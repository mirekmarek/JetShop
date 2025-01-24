<?php
/**
 *
 * @copyright 
 * @license  
 * @author  
 */
namespace JetApplicationModule\Admin\Marketing\Banners;

use Jet\Application_Module;
use JetApplication\Admin_Entity_Marketing_Interface;
use JetApplication\Admin_EntityManager_Marketing_Trait;
use JetApplication\Admin_Managers_MarketingBanners;
use JetApplication\Entity_Marketing;
use JetApplication\Marketing_Banner;

/**
 *
 */
class Main extends Application_Module implements Admin_Managers_MarketingBanners
{
	use Admin_EntityManager_Marketing_Trait;
	
	public const ADMIN_MAIN_PAGE = 'banners';

	public const ACTION_GET = 'get_banner';
	public const ACTION_ADD = 'add_banner';
	public const ACTION_UPDATE = 'update_banner';
	public const ACTION_DELETE = 'delete_banner';
	
	public static function getEntityInstance(): Admin_Entity_Marketing_Interface|Entity_Marketing
	{
		return new Marketing_Banner();
	}
	
	public static function getEntityNameReadable(): string
	{
		return 'Marketing - banner';
	}


}