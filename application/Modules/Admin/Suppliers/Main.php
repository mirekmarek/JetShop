<?php
namespace JetApplicationModule\Admin\Suppliers;

use JetApplication\Admin_Entity_Common_Manager_Interface;
use JetApplication\Admin_Entity_Common_Manager_Trait;
use Jet\Application_Module;
use JetApplication\Admin_Entity_Common_Interface;
use JetApplication\Entity_Common;

/**
 *
 */
class Main extends Application_Module implements Admin_Entity_Common_Manager_Interface
{
	use Admin_Entity_Common_Manager_Trait;

	public const ADMIN_MAIN_PAGE = 'suppliers';

	public const ACTION_GET = 'get_supplier';
	public const ACTION_ADD = 'add_supplier';
	public const ACTION_UPDATE = 'update_supplier';
	public const ACTION_DELETE = 'delete_supplier';
	
	
	public static function getEntityInstance(): Entity_Common|Admin_Entity_Common_Interface
	{
		return new Supplier();
	}
	
	public static function getEntityNameReadable() : string
	{
		return 'supplier';
	}

}