<?php
/**
 *
 * @copyright 
 * @license  
 * @author  
 */
namespace JetApplicationModule\Admin\Content\Email\Layouts;

use Jet\Application_Module;
use JetApplication\Admin_EntityManager_Trait;
use JetApplication\Admin_Managers_ContentEMailLayouts;
use JetApplication\EMail_Layout;
use JetApplication\Entity_Basic;

/**
 *
 */
class Main extends Application_Module implements Admin_Managers_ContentEMailLayouts
{
	use Admin_EntityManager_Trait;
	
	public const ADMIN_MAIN_PAGE = 'content-email-layouts';

	public const ACTION_GET = 'get_email_layout';
	public const ACTION_ADD = 'add_email_layout';
	public const ACTION_UPDATE = 'update_email_layout';
	public const ACTION_DELETE = 'delete_email_layout';
	
	
	public static function getEntityInstance(): Entity_Basic
	{
		return new EMail_Layout();
	}

}