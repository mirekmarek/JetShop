<?php
/**
 *
 * @copyright 
 * @license  
 * @author  
 */
namespace JetApplicationModule\Admin\Catalog\KindsOfProductFile;

use Jet\Application_Module;
use JetApplication\Admin_Entity_WithShopData_Interface;
use JetApplication\Admin_EntityManager_WithShopData_Trait;
use JetApplication\Admin_Managers_KindOfProductFile;
use JetApplication\Entity_WithShopData;

/**
 *
 */
class Main extends Application_Module implements Admin_Managers_KindOfProductFile
{
	use Admin_EntityManager_WithShopData_Trait;
	
	
	public const ADMIN_MAIN_PAGE = 'kind-of-product-file';
	
	public const ACTION_GET = 'get_kind_of_product_file';
	public const ACTION_ADD = 'add_kind_of_product_file';
	public const ACTION_UPDATE = 'update_kind_of_product_file';
	public const ACTION_DELETE = 'delete_kind_of_product_file';
	
	public static function getEntityInstance(): Entity_WithShopData|Admin_Entity_WithShopData_Interface
	{
		return new KindOfProductFile();
	}
	
	public static function getEntityNameReadable() : string
	{
		return 'kind of product file';
	}
	
}