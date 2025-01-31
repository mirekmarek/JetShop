<?php
/**
 * @copyright Copyright (c) Miroslav Marek <mirek.marek@web-jet.cz>
 * @license EUPL 1.2  https://eupl.eu/1.2/en/
 * @author Miroslav Marek <mirek.marek@web-jet.cz>
 */
namespace JetApplicaTionModule\Admin\Catalog\KindsOfProductFile;


use JetApplication\Admin_Managers_KindOfProductFile;
use JetApplication\EShopEntity_Basic;
use JetApplication\Product_KindOfFile;


class Main extends Admin_Managers_KindOfProductFile
{
	public const ADMIN_MAIN_PAGE = 'kind-of-product-file';
	
	public static function getEntityInstance(): EShopEntity_Basic
	{
		return new Product_KindOfFile();
	}
}