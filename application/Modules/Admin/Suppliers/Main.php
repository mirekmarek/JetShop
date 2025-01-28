<?php
namespace JetApplicationModule\Admin\Suppliers;

use JetApplication\Admin_Managers_Supplier;
use JetApplication\Admin_EntityManager_Trait;
use Jet\Application_Module;
use JetApplication\Entity_Basic;
use JetApplication\Supplier;

/**
 *
 */
class Main extends Application_Module implements Admin_Managers_Supplier
{
	use Admin_EntityManager_Trait;

	public const ADMIN_MAIN_PAGE = 'suppliers';

	public const ACTION_GET = 'get_supplier';
	public const ACTION_ADD = 'add_supplier';
	public const ACTION_UPDATE = 'update_supplier';
	public const ACTION_DELETE = 'delete_supplier';
	
	
	public static function getEntityInstance(): Entity_Basic
	{
		return new Supplier();
	}
}