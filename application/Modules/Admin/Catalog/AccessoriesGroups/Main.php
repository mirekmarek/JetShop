<?php
/**
 *
 * @copyright 
 * @license  
 * @author  
 */
namespace JetApplicationModule\Admin\Catalog\AccessoriesGroups;

use Jet\Application_Module;
use JetApplication\Accessories_Group;
use JetApplication\Entity_Common;
use JetApplication\Admin_EntityManager_Common_Interface;
use JetApplication\Admin_Entity_Common_Interface;
use JetApplication\Admin_EntityManager_Common_Trait;

/**
 *
 */
class Main extends Application_Module implements Admin_EntityManager_Common_Interface
{
	use Admin_EntityManager_Common_Trait;
	
	public const ADMIN_MAIN_PAGE = 'accesories-groups';
	
	public const ACTION_GET = 'get_accessories_group';
	public const ACTION_ADD = 'add_accessories_group';
	public const ACTION_UPDATE = 'update_accessories_group';
	public const ACTION_DELETE = 'delete_accessories_group';
	
	
	public static function getEntityInstance(): Entity_Common|Admin_Entity_Common_Interface
	{
		return new Accessories_Group();
	}
	
	public static function getEntityNameReadable() : string
	{
		return 'accessories group';
	}
	
}