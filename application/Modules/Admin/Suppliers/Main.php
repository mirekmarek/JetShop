<?php
/**
 * @copyright Copyright (c) Miroslav Marek <mirek.marek@web-jet.cz>
 * @license EUPL 1.2  https://eupl.eu/1.2/en/
 * @author Miroslav Marek <mirek.marek@web-jet.cz>
 */
namespace JetApplicationModule\Admin\Suppliers;


use JetApplication\Application_Service_Admin_Supplier;
use JetApplication\EShopEntity_Basic;
use JetApplication\Supplier;


class Main extends Application_Service_Admin_Supplier
{
	public const ADMIN_MAIN_PAGE = 'suppliers';
	
	public static function getEntityInstance(): EShopEntity_Basic
	{
		return new Supplier();
	}
}