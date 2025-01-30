<?php
namespace JetApplicationModule\Admin\Marketing\GiftProduct;

use JetApplication\Admin_Managers_Marketing_GiftsProducts;
use JetApplication\Admin_EntityManager_Trait;
use Jet\Application_Module;
use JetApplication\EShopEntity_Basic;
use JetApplication\Marketing_Gift_Product;

/**
 *
 */
class Main extends Application_Module implements Admin_Managers_Marketing_GiftsProducts
{
	use Admin_EntityManager_Trait;

	public const ADMIN_MAIN_PAGE = 'gifts-products';

	public const ACTION_GET = 'get_gift_product';
	public const ACTION_ADD = 'add_gift_product';
	public const ACTION_UPDATE = 'update_gift_product';
	public const ACTION_DELETE = 'delete_gift_product';
	
	
	public static function getEntityInstance(): EShopEntity_Basic
	{
		return new Marketing_Gift_Product();
	}
	
}