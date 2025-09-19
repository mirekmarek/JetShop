<?php
/**
 * @copyright Copyright (c) Miroslav Marek <mirek.marek@web-jet.cz>
 * @license EUPL 1.2  https://eupl.eu/1.2/en/
 * @author Miroslav Marek <mirek.marek@web-jet.cz>
 */
namespace JetApplicationModule\Admin\Marketing\GiftProduct;


use JetApplication\Application_Service_Admin_Marketing_GiftsProducts;
use JetApplication\EShopEntity_Basic;
use JetApplication\Marketing_Gift_Product;


class Main extends Application_Service_Admin_Marketing_GiftsProducts
{
	public const ADMIN_MAIN_PAGE = 'gifts-products';
	
	public static function getEntityInstance(): EShopEntity_Basic
	{
		return new Marketing_Gift_Product();
	}
	
}