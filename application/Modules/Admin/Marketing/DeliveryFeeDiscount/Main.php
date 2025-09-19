<?php
/**
 * @copyright Copyright (c) Miroslav Marek <mirek.marek@web-jet.cz>
 * @license EUPL 1.2  https://eupl.eu/1.2/en/
 * @author Miroslav Marek <mirek.marek@web-jet.cz>
 */
namespace JetApplicationModule\Admin\Marketing\DeliveryFeeDiscount;


use JetApplication\Application_Service_Admin_Marketing_DeliveryFeeDiscounts;
use JetApplication\EShopEntity_Basic;
use JetApplication\Marketing_DeliveryFeeDiscount;


class Main extends Application_Service_Admin_Marketing_DeliveryFeeDiscounts
{
	
	public const ADMIN_MAIN_PAGE = 'delivery-fee-discounts';
	
	public static function getEntityInstance(): EShopEntity_Basic
	{
		return new Marketing_DeliveryFeeDiscount();
	}

}