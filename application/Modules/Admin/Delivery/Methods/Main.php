<?php
/**
 * @copyright Copyright (c) Miroslav Marek <mirek.marek@web-jet.cz>
 * @license EUPL 1.2  https://eupl.eu/1.2/en/
 * @author Miroslav Marek <mirek.marek@web-jet.cz>
 */
namespace JetApplicationModule\Admin\Delivery\Methods;


use Jet\Auth;
use JetApplication\Admin_Managers_DeliveryMethods;
use JetApplication\Auth_Administrator_Role;
use JetApplication\Delivery_Method;
use JetApplication\EShopEntity_Basic;


class Main extends Admin_Managers_DeliveryMethods
{
	public const ADMIN_MAIN_PAGE = 'delivery-method';

	public const ACTION_SET_PRICE = 'set_price';
	
	
	public static function getEntityInstance(): EShopEntity_Basic
	{
		return new Delivery_Method();
	}
	
	public static function getCurrentUserCanSetPrice() : bool
	{
		return Auth::getCurrentUserHasPrivilege( Auth_Administrator_Role::PRIVILEGE_MODULE_ACTION, static::ACTION_SET_PRICE );
	}
	
}