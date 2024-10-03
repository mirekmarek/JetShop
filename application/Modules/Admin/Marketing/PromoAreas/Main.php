<?php
/**
 *
 * @copyright 
 * @license  
 * @author  
 */
namespace JetApplicationModule\Admin\Marketing\PromoAreas;

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
	
	public const ADMIN_MAIN_PAGE = 'promo-areas';

	public const ACTION_GET = 'get_promo_area';
	public const ACTION_ADD = 'add_promo_area';
	public const ACTION_UPDATE = 'update_promo_area';
	public const ACTION_DELETE = 'delete_promo_area';
	
	
	public static function showActiveState( int $id ): string
	{
		return '';
	}
	
	public static function getEntityInstance(): Admin_Entity_Marketing_Interface|Entity_Marketing
	{
		return new PromoArea();
	}
	
	public static function getEntityNameReadable(): string
	{
		return 'Promo area';
	}


}