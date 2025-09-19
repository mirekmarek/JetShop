<?php
/**
 * @copyright Copyright (c) Miroslav Marek <mirek.marek@web-jet.cz>
 * @license EUPL 1.2  https://eupl.eu/1.2/en/
 * @author Miroslav Marek <mirek.marek@web-jet.cz>
 */
namespace JetApplicationModule\Admin\Delivery\Methods;


use JetApplication\Application_Service_Admin_DeliveryMethods;
use JetApplication\Delivery_Method;
use JetApplication\EShopEntity_Basic;


class Main extends Application_Service_Admin_DeliveryMethods
{
	public const ADMIN_MAIN_PAGE = 'delivery-method';

	public const ACTION_SET_PRICE = 'set_price';
	
	
	public static function getEntityInstance(): EShopEntity_Basic
	{
		return new Delivery_Method();
	}
	
	public static function getCurrentUserCanSetPrice() : bool
	{
		return static::getCurrentUserCanDoAction( static::ACTION_SET_PRICE );
	}
	
}