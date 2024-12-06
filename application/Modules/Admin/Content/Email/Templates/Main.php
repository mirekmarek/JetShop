<?php
/**
 *
 * @copyright 
 * @license  
 * @author  
 */
namespace JetApplicationModule\Admin\Content\Email\Templates;

use Jet\Application_Module;
use JetApplication\Admin_Entity_WithEShopData_Interface;
use JetApplication\Admin_EntityManager_WithEShopData_Interface;
use JetApplication\Admin_EntityManager_WithEShopData_Trait;
use JetApplication\Entity_WithEShopData;

/**
 *
 */
class Main extends Application_Module implements Admin_EntityManager_WithEShopData_Interface
{
	use Admin_EntityManager_WithEShopData_Trait;
	
	public const ADMIN_MAIN_PAGE = 'content-email-templates';

	public const ACTION_GET = 'get_email_template';
	public const ACTION_ADD = 'add_email_template';
	public const ACTION_UPDATE = 'update_email_template';
	public const ACTION_DELETE = 'delete_email_template';
	
	
	public static function getEntityNameReadable() : string
	{
		return 'E-mail template';
	}
	
	public static function getEntityInstance(): Entity_WithEShopData|Admin_Entity_WithEShopData_Interface
	{
		return new EmailTemplateText();
	}
	
	public static function getCurrentUserCanCreate() : bool
	{
		return false;
	}

	
}