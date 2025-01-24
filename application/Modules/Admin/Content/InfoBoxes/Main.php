<?php
/**
 *
 * @copyright 
 * @license  
 * @author  
 */
namespace JetApplicationModule\Admin\Content\InfoBoxes;

use Jet\Application_Module;
use JetApplication\Admin_Entity_WithEShopData_Interface;
use JetApplication\Admin_EntityManager_WithEShopData_Trait;
use JetApplication\Admin_Managers_ContentInfoBoxes;
use JetApplication\Content_InfoBox;
use JetApplication\Entity_WithEShopData;

/**
 *
 */
class Main extends Application_Module implements Admin_Managers_ContentInfoBoxes
{
	use Admin_EntityManager_WithEShopData_Trait;
	
	public const ADMIN_MAIN_PAGE = 'content-info-box';

	public const ACTION_GET = 'get_content_info_box';
	public const ACTION_ADD = 'add_content_info_box';
	public const ACTION_UPDATE = 'update_content_info_box';
	public const ACTION_DELETE = 'delete_content_info_box';
	
	
	public static function getEntityNameReadable() : string
	{
		return 'Content - Info Box';
	}
	
	public static function getEntityInstance(): Entity_WithEShopData|Admin_Entity_WithEShopData_Interface
	{
		return new Content_InfoBox();
	}

}