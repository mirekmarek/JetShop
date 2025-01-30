<?php
/**
 *
 * @copyright 
 * @license  
 * @author  
 */
namespace JetApplicationModule\Admin\Content\Email\Templates;

use Jet\Application_Module;
use JetApplication\Admin_EntityManager_Trait;
use JetApplication\Admin_Managers_Content_EMailTemplates;
use JetApplication\EShopEntity_Basic;
use JetApplication\EMail_TemplateText;

/**
 *
 */
class Main extends Application_Module implements Admin_Managers_Content_EMailTemplates
{
	use Admin_EntityManager_Trait;
	
	public const ADMIN_MAIN_PAGE = 'content-email-templates';

	public const ACTION_GET = 'get_email_template';
	public const ACTION_ADD = 'add_email_template';
	public const ACTION_UPDATE = 'update_email_template';
	public const ACTION_DELETE = 'delete_email_template';
	
	
	public static function getEntityInstance(): EShopEntity_Basic
	{
		return new EMail_TemplateText();
	}
	
	public static function getCurrentUserCanCreate() : bool
	{
		return false;
	}

	
}