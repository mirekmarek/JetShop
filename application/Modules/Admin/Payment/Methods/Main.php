<?php
/**
 *
 * @copyright 
 * @license  
 * @author  
 */
namespace JetApplicationModule\Admin\Payment\Methods;

use Jet\Application_Module;
use JetApplication\Admin_Managers_Trait;

/**
 *
 */
class Main extends Application_Module
{
	use Admin_Managers_Trait;

	public const ADMIN_MAIN_PAGE = 'payment-method';

	public const ACTION_GET = 'get_payment_method';
	public const ACTION_ADD = 'add_payment_method';
	public const ACTION_UPDATE = 'update_payment_method';
	public const ACTION_DELETE = 'delete_payment_method';
	
}