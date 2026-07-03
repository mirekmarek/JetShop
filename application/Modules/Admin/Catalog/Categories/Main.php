<?php
/**
 * @copyright Copyright (c) Miroslav Marek <mirek.marek@web-jet.cz>
 * @license EUPL 1.2  https://eupl.eu/1.2/en/
 * @author Miroslav Marek <mirek.marek@web-jet.cz>
 */
namespace JetApplicationModule\Admin\Catalog\Categories;

use JetApplication\Application_Service_Admin_Category;
use JetApplication\Category;
use JetApplication\EShopEntity_Basic;


class Main extends Application_Service_Admin_Category
{
	public const ADMIN_MAIN_PAGE = 'categories';
	
	public static function getEntityInstance(): EShopEntity_Basic
	{
		return new Category();
	}
	
}