<?php
/**
 *
 * @copyright 
 * @license  
 * @author  
 */
namespace JetApplicationModule\Admin\Content\Email\Layouts;

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
	
	public const ADMIN_MAIN_PAGE = 'content-email-layouts';

	public const ACTION_GET = 'get_email_layout';
	public const ACTION_ADD = 'add_email_layout';
	public const ACTION_UPDATE = 'update_email_layout';
	public const ACTION_DELETE = 'delete_email_layout';
	
	
	public static function getEntityNameReadable() : string
	{
		return 'E-mail layout';
	}
	
	public static function getEntityInstance(): Entity_WithShopData|Admin_Entity_WithShopData_Interface
	{
		return new EmailLayout();
	}

}