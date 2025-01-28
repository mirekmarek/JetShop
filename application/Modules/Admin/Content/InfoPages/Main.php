<?php
/**
 *
 * @copyright 
 * @license  
 * @author  
 */
namespace JetApplicationModule\Admin\Content\InfoPages;

use Jet\Application_Module;
use JetApplication\Admin_EntityManager_Trait;
use JetApplication\Admin_Managers_ContentInfoPages;
use JetApplication\Content_InfoPage;
use JetApplication\Entity_Basic;

/**
 *
 */
class Main extends Application_Module implements Admin_Managers_ContentInfoPages
{
	use Admin_EntityManager_Trait;
	
	public const ADMIN_MAIN_PAGE = 'content-info-page';

	public const ACTION_GET = 'get_content_info_page';
	public const ACTION_ADD = 'add_content_info_page';
	public const ACTION_UPDATE = 'update_content_info_page';
	public const ACTION_DELETE = 'delete_content_info_page';
	

	
	public static function getEntityInstance(): Entity_Basic
	{
		return new Content_InfoPage();
	}

}