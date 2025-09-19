<?php
/**
 * @copyright Copyright (c) Miroslav Marek <mirek.marek@web-jet.cz>
 * @license EUPL 1.2  https://eupl.eu/1.2/en/
 * @author Miroslav Marek <mirek.marek@web-jet.cz>
 */
namespace JetApplicationModule\Admin\Payment\Methods;


use JetApplication\Application_Service_Admin_PaymentMethods;
use JetApplication\EShopEntity_Basic;
use JetApplication\Payment_Method;


class Main extends Application_Service_Admin_PaymentMethods
{
	public const ADMIN_MAIN_PAGE = 'payment-method';

	public const ACTION_SET_PRICE = 'set_price';
	
	public static function getEntityInstance(): EShopEntity_Basic
	{
		return new Payment_Method();
	}
	
	public static function getCurrentUserCanSetPrice() : bool
	{
		return static::getCurrentUserCanDoAction( static::ACTION_SET_PRICE );
	}
	

}