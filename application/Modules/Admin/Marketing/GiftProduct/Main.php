<?php
namespace JetApplicationModule\Admin\Marketing\GiftProduct;

use JetApplication\Admin_EntityManager_Marketing_Interface;
use JetApplication\Admin_EntityManager_Marketing_Trait;

use Jet\Application_Module;
use JetApplication\Admin_Entity_Marketing_Interface;
use JetApplication\Entity_Marketing;

/**
 *
 */
class Main extends Application_Module implements Admin_EntityManager_Marketing_Interface
{
	use Admin_EntityManager_Marketing_Trait;

	public const ADMIN_MAIN_PAGE = 'gifts-products';

	public const ACTION_GET = 'get_gift_product';
	public const ACTION_ADD = 'add_gift_product';
	public const ACTION_UPDATE = 'update_gift_product';
	public const ACTION_DELETE = 'delete_gift_product';
	
	
	public static function getEntityInstance(): Entity_Marketing|Admin_Entity_Marketing_Interface
	{
		return new Gift();
	}
	
	public static function getEntityNameReadable() : string
	{
		return 'Gift - product';
	}

}