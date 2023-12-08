<?php
/**
 *
 * @copyright 
 * @license  
 * @author  
 */
namespace JetApplicationModule\Admin\Services\Services;

use Jet\Application_Module;
use JetApplication\Admin_Managers_Trait;

/**
 *
 */
class Main extends Application_Module
{
	use Admin_Managers_Trait;

	public const ADMIN_MAIN_PAGE = 'service';

	public const ACTION_GET = 'get_service';
	public const ACTION_ADD = 'add_service';
	public const ACTION_UPDATE = 'update_service';
	public const ACTION_DELETE = 'delete_service';
	
}