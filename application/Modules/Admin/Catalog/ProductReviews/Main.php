<?php
/**
 *
 * @copyright 
 * @license  
 * @author  
 */
namespace JetApplicationModule\Admin\Catalog\ProductReviews;

use Jet\Application_Module;
use JetApplication\Admin_Entity_WithShopRelation_Interface;
use JetApplication\Admin_EntityManager_WithShopRelation_Interface;
use JetApplication\Admin_EntityManager_WithShopRelation_Trait;
use JetApplication\Entity_WithShopRelation;

/**
 *
 */
class Main extends Application_Module implements Admin_EntityManager_WithShopRelation_Interface
{
	use Admin_EntityManager_WithShopRelation_Trait;
	
	public const ADMIN_MAIN_PAGE = 'product-reviews';

	public const ACTION_GET = 'get_product_review';
	public const ACTION_ADD = 'add_product_review';
	public const ACTION_UPDATE = 'update_product_review';
	public const ACTION_DELETE = 'delete_product_review';
	
	
	public static function getEntityNameReadable() : string
	{
		return 'Product review';
	}
	
	public static function getEntityInstance(): Entity_WithShopRelation|Admin_Entity_WithShopRelation_Interface
	{
		return new ProductReview();
	}
	
	public static function getCurrentUserCanCreate() : bool
	{
		return false;
	}

}