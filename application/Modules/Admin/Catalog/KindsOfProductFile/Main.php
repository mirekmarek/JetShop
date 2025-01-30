<?php
/**
 *
 * @copyright 
 * @license  
 * @author  
 */
namespace JetApplicationModule\Admin\Catalog\KindsOfProductFile;

use Jet\Application_Module;
use JetApplication\Admin_EntityManager_Trait;
use JetApplication\Admin_Managers_KindOfProductFile;
use JetApplication\EShopEntity_Basic;
use JetApplication\Product_KindOfFile;

/**
 *
 */
class Main extends Application_Module implements Admin_Managers_KindOfProductFile
{
	use Admin_EntityManager_Trait;
	
	
	public const ADMIN_MAIN_PAGE = 'kind-of-product-file';
	
	public const ACTION_GET = 'get_kind_of_product_file';
	public const ACTION_ADD = 'add_kind_of_product_file';
	public const ACTION_UPDATE = 'update_kind_of_product_file';
	public const ACTION_DELETE = 'delete_kind_of_product_file';
	
	public static function getEntityInstance(): EShopEntity_Basic
	{
		return new Product_KindOfFile();
	}
}