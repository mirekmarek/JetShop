<?php
/**
 *
 * @copyright 
 * @license  
 * @author  
 */
namespace JetApplicationModule\Admin\Delivery\Methods;

use Jet\Application_Module;
use JetApplication\Admin_Managers_Trait;

/**
 *
 */
class Main extends Application_Module
{
	use Admin_Managers_Trait;

	public const ADMIN_MAIN_PAGE = 'delivery-method';

	public const ACTION_GET = 'get_delivery_method';
	public const ACTION_ADD = 'add_delivery_method';
	public const ACTION_UPDATE = 'update_delivery_method';
	public const ACTION_DELETE = 'delete_delivery_method';
	

}