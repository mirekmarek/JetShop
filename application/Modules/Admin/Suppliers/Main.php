<?php
/**
 * @copyright Copyright (c) Miroslav Marek <mirek.marek@web-jet.cz>
 * @license EUPL 1.2  https://eupl.eu/1.2/en/
 * @author Miroslav Marek <mirek.marek@web-jet.cz>
 */
namespace JetApplicaTionModule\Admin\Suppliers;


use JetApplication\Admin_Managers_Supplier;
use JetApplication\EShopEntity_Basic;
use JetApplication\Supplier;


class Main extends Admin_Managers_Supplier
{
	public const ADMIN_MAIN_PAGE = 'suppliers';
	
	public static function getEntityInstance(): EShopEntity_Basic
	{
		return new Supplier();
	}
}