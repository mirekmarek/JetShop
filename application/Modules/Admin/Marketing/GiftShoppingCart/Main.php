<?php
namespace JetApplicationModule\Admin\Marketing\GiftShoppingCart;

use JetApplication\Admin_Managers_Marketing_GiftsShoppingCart;
use JetApplication\Admin_EntityManager_Trait;

use Jet\Application_Module;
use JetApplication\EShopEntity_Basic;
use JetApplication\Marketing_Gift_ShoppingCart;

/**
 *
 */
class Main extends Application_Module implements Admin_Managers_Marketing_GiftsShoppingCart
{
	use Admin_EntityManager_Trait;

	public const ADMIN_MAIN_PAGE = 'gifts-shopping-cart';

	public const ACTION_GET = 'get_gift_shopping_cart';
	public const ACTION_ADD = 'add_gift_shopping_cart';
	public const ACTION_UPDATE = 'update_gift_shopping_cart';
	public const ACTION_DELETE = 'delete_gift_shopping_cart';
	
	
	public static function getEntityInstance(): EShopEntity_Basic
	{
		return new Marketing_Gift_ShoppingCart();
	}
	
}