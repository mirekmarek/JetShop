<?php
/**
 * @copyright Copyright (c) Miroslav Marek <mirek.marek@web-jet.cz>
 * @license EUPL 1.2  https://eupl.eu/1.2/en/
 * @author Miroslav Marek <mirek.marek@web-jet.cz>
 */
namespace JetApplicaTionModule\Admin\Marketing\GiftShoppingCart;


use JetApplication\Admin_Managers_Marketing_GiftsShoppingCart;
use JetApplication\EShopEntity_Basic;
use JetApplication\Marketing_Gift_ShoppingCart;


class Main extends Admin_Managers_Marketing_GiftsShoppingCart
{
	public const ADMIN_MAIN_PAGE = 'gifts-shopping-cart';
	
	public static function getEntityInstance(): EShopEntity_Basic
	{
		return new Marketing_Gift_ShoppingCart();
	}
	
}