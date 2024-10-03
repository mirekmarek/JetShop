<?php
/**
 *
 * @copyright 
 * @license  
 * @author  
 */
namespace JetApplicationModule\Admin\Content\InfoBoxes;

use Jet\Application_Module;
use JetApplication\Admin_Entity_WithShopData_Interface;
use JetApplication\Admin_EntityManager_WithShopData_Interface;
use JetApplication\Admin_EntityManager_WithShopData_Trait;
use JetApplication\Entity_WithShopData;

/**
 *
 */
class Main extends Application_Module implements Admin_EntityManager_WithShopData_Interface
{
	use Admin_EntityManager_WithShopData_Trait;
	
	public const ADMIN_MAIN_PAGE = 'content-info-box';

	public const ACTION_GET = 'get_content_info_box';
	public const ACTION_ADD = 'add_content_info_box';
	public const ACTION_UPDATE = 'update_content_info_box';
	public const ACTION_DELETE = 'delete_content_info_box';
	
	
	public static function getEntityNameReadable() : string
	{
		return 'Content - Info Box';
	}
	
	public static function getEntityInstance(): Entity_WithShopData|Admin_Entity_WithShopData_Interface
	{
		return new InfoBox();
	}

}