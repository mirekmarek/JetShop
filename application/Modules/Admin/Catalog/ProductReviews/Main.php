<?php
/**
 *
 * @copyright 
 * @license  
 * @author  
 */
namespace JetApplicationModule\Admin\Catalog\ProductReviews;

use Jet\Application_Module;
use JetApplication\Admin_Entity_WithEShopRelation_Interface;
use JetApplication\Admin_EntityManager_WithEShopRelation_Interface;
use JetApplication\Admin_EntityManager_WithEShopRelation_Trait;
use JetApplication\Entity_WithEShopRelation;

/**
 *
 */
class Main extends Application_Module implements Admin_EntityManager_WithEShopRelation_Interface
{
	use Admin_EntityManager_WithEShopRelation_Trait;
	
	public const ADMIN_MAIN_PAGE = 'product-reviews';

	public const ACTION_GET = 'get_product_review';
	public const ACTION_ADD = 'add_product_review';
	public const ACTION_UPDATE = 'update_product_review';
	public const ACTION_DELETE = 'delete_product_review';
	
	
	public static function getEntityNameReadable() : string
	{
		return 'Product review';
	}
	
	public static function getEntityInstance(): Entity_WithEShopRelation|Admin_Entity_WithEShopRelation_Interface
	{
		return new ProductReview();
	}
	
	public static function getCurrentUserCanCreate() : bool
	{
		return false;
	}

}