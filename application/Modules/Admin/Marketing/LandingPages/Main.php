<?php
/**
 *
 * @copyright 
 * @license  
 * @author  
 */
namespace JetApplicationModule\Admin\Marketing\LandingPages;

use Jet\Application_Module;
use JetApplication\Admin_Entity_Marketing_Interface;
use JetApplication\Admin_Managers_MarketingLandingPages;
use JetApplication\Admin_EntityManager_Marketing_Trait;
use JetApplication\Entity_Marketing;
use JetApplication\Marketing_LandingPage;

/**
 *
 */
class Main extends Application_Module implements Admin_Managers_MarketingLandingPages
{
	use Admin_EntityManager_Marketing_Trait;
	
	public const ADMIN_MAIN_PAGE = 'landing-pages';

	public const ACTION_GET = 'get_landing_page';
	public const ACTION_ADD = 'add_landing_page';
	public const ACTION_UPDATE = 'update_landing_page';
	public const ACTION_DELETE = 'delete_landing_page';
	
	public static function getEntityInstance(): Admin_Entity_Marketing_Interface|Entity_Marketing
	{
		return new Marketing_LandingPage();
	}
	
	public static function getEntityNameReadable(): string
	{
		return 'Marketing - Landing page';
	}


}