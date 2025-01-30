<?php
/**
 *
 * @copyright 
 * @license  
 * @author  
 */
namespace JetApplicationModule\Admin\Marketing\LandingPages;

use Jet\Application_Module;
use JetApplication\Admin_Managers_Marketing_LandingPages;
use JetApplication\Admin_EntityManager_Trait;
use JetApplication\EShopEntity_Basic;
use JetApplication\Marketing_LandingPage;

/**
 *
 */
class Main extends Application_Module implements Admin_Managers_Marketing_LandingPages
{
	use Admin_EntityManager_Trait;
	
	public const ADMIN_MAIN_PAGE = 'landing-pages';

	public const ACTION_GET = 'get_landing_page';
	public const ACTION_ADD = 'add_landing_page';
	public const ACTION_UPDATE = 'update_landing_page';
	public const ACTION_DELETE = 'delete_landing_page';
	
	public static function getEntityInstance(): EShopEntity_Basic
	{
		return new Marketing_LandingPage();
	}
}