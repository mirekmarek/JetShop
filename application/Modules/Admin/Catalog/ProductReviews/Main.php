<?php
/**
 * @copyright Copyright (c) Miroslav Marek <mirek.marek@web-jet.cz>
 * @license EUPL 1.2  https://eupl.eu/1.2/en/
 * @author Miroslav Marek <mirek.marek@web-jet.cz>
 */
namespace JetApplicationModule\Admin\Catalog\ProductReviews;


use JetApplication\Application_Service_Admin_ProductReviews;
use JetApplication\EShopEntity_Basic;
use JetApplication\ProductReview;


class Main extends Application_Service_Admin_ProductReviews
{
	public const ADMIN_MAIN_PAGE = 'product-reviews';
	
	public static function getEntityInstance(): EShopEntity_Basic
	{
		return new ProductReview();
	}
	
	public static function getCurrentUserCanCreate() : bool
	{
		return false;
	}

}