<?php
/**
 * @copyright Copyright (c) Miroslav Marek <mirek.marek@web-jet.cz>
 * @license EUPL 1.2  https://eupl.eu/1.2/en/
 * @author Miroslav Marek <mirek.marek@web-jet.cz>
 */
namespace JetApplicationModule\Admin\Discounts\CodesDefinition;


use JetApplication\Application_Service_Admin_DiscountCodesDefinition;
use JetApplication\Discounts_Code;
use JetApplication\EShopEntity_Basic;


class Main extends Application_Service_Admin_DiscountCodesDefinition
{
	public const ADMIN_MAIN_PAGE = 'discounts-codes-definition';
	
	public static function getEntityInstance(): EShopEntity_Basic
	{
		return new Discounts_Code();
	}
}