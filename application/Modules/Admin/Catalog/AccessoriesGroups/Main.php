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
use JetApplication\Entity_Basic;
use JetApplication\Admin_Managers_AccessoriesGroups;
use JetApplication\Admin_EntityManager_Trait;

/**
 *
 */
class Main extends Application_Module implements Admin_Managers_AccessoriesGroups
{
	use Admin_EntityManager_Trait;
	
	public const ADMIN_MAIN_PAGE = 'accesories-groups';
	
	public const ACTION_GET = 'get_accessories_group';
	public const ACTION_ADD = 'add_accessories_group';
	public const ACTION_UPDATE = 'update_accessories_group';
	public const ACTION_DELETE = 'delete_accessories_group';
	
	
	public static function getEntityInstance(): Entity_Basic
	{
		return new Accessories_Group();
	}
	
}