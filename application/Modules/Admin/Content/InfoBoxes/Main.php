<?php
/**
 *
 * @copyright 
 * @license  
 * @author  
 */
namespace JetApplicationModule\Admin\Content\InfoBoxes;

use Jet\Application_Module;
use JetApplication\Admin_EntityManager_Trait;
use JetApplication\Admin_Managers_Content_InfoBoxes;
use JetApplication\Content_InfoBox;
use JetApplication\EShopEntity_Basic;

/**
 *
 */
class Main extends Application_Module implements Admin_Managers_Content_InfoBoxes
{
	use Admin_EntityManager_Trait;
	
	public const ADMIN_MAIN_PAGE = 'content-info-box';

	public const ACTION_GET = 'get_content_info_box';
	public const ACTION_ADD = 'add_content_info_box';
	public const ACTION_UPDATE = 'update_content_info_box';
	public const ACTION_DELETE = 'delete_content_info_box';
	
	
	public static function getEntityInstance(): EShopEntity_Basic
	{
		return new Content_InfoBox();
	}

}