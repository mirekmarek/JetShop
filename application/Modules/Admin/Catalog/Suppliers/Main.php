<?php
namespace JetApplicationModule\Admin\Catalog\Suppliers;

use JetApplication\Admin_Managers_Trait;
use Jet\Application_Module;

/**
 *
 */
class Main extends Application_Module
{
	use Admin_Managers_Trait;

	public const ADMIN_MAIN_PAGE = 'suppliers';

	public const ACTION_GET = 'get_supplier';
	public const ACTION_ADD = 'add_supplier';
	public const ACTION_UPDATE = 'update_supplier';
	public const ACTION_DELETE = 'delete_supplier';

	
}