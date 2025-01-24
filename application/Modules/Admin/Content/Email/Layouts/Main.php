<?php
/**
 *
 * @copyright 
 * @license  
 * @author  
 */
namespace JetApplicationModule\Admin\Content\Email\Layouts;

use Jet\Application_Module;
use JetApplication\Admin_Entity_WithEShopData_Interface;
use JetApplication\Admin_EntityManager_WithEShopData_Trait;
use JetApplication\Admin_Managers_ContentEMailLayouts;
use JetApplication\EMail_Layout;
use JetApplication\Entity_WithEShopData;

/**
 *
 */
class Main extends Application_Module implements Admin_Managers_ContentEMailLayouts
{
	use Admin_EntityManager_WithEShopData_Trait;
	
	public const ADMIN_MAIN_PAGE = 'content-email-layouts';

	public const ACTION_GET = 'get_email_layout';
	public const ACTION_ADD = 'add_email_layout';
	public const ACTION_UPDATE = 'update_email_layout';
	public const ACTION_DELETE = 'delete_email_layout';
	
	
	public static function getEntityNameReadable() : string
	{
		return 'E-mail layout';
	}
	
	public static function getEntityInstance(): Entity_WithEShopData|Admin_Entity_WithEShopData_Interface
	{
		return new EMail_Layout();
	}

}