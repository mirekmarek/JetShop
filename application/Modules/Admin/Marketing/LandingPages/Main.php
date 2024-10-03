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
use JetApplication\Admin_EntityManager_Marketing_Interface;
use JetApplication\Admin_EntityManager_Marketing_Trait;
use JetApplication\Entity_Marketing;

/**
 *
 */
class Main extends Application_Module implements Admin_EntityManager_Marketing_Interface
{
	use Admin_EntityManager_Marketing_Trait;
	
	public const ADMIN_MAIN_PAGE = 'landing-pages';

	public const ACTION_GET = 'get_landing_page';
	public const ACTION_ADD = 'add_landing_page';
	public const ACTION_UPDATE = 'update_landing_page';
	public const ACTION_DELETE = 'delete_landing_page';
	
	public static function showActiveState( int $id ): string
	{
		return '';
	}
	
	public static function getEntityInstance(): Admin_Entity_Marketing_Interface|Entity_Marketing
	{
		return new LandingPage();
	}
	
	public static function getEntityNameReadable(): string
	{
		return 'Landing page';
	}


}