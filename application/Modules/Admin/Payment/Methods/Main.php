<?php
/**
 * @copyright Copyright (c) Miroslav Marek <mirek.marek@web-jet.cz>
 * @license EUPL 1.2  https://eupl.eu/1.2/en/
 * @author Miroslav Marek <mirek.marek@web-jet.cz>
 */
namespace JetApplicaTionModule\Admin\Payment\Methods;


use Jet\Auth;
use JetApplication\Admin_Managers_PaymentMethods;
use JetApplication\Auth_Administrator_Role;
use JetApplication\EShopEntity_Basic;
use JetApplication\Payment_Method;


class Main extends Admin_Managers_PaymentMethods
{
	public const ADMIN_MAIN_PAGE = 'payment-method';

	public const ACTION_SET_PRICE = 'set_price';
	
	public static function getEntityInstance(): EShopEntity_Basic
	{
		return new Payment_Method();
	}
	
	public static function getCurrentUserCanSetPrice() : bool
	{
		return Auth::getCurrentUserHasPrivilege( Auth_Administrator_Role::PRIVILEGE_MODULE_ACTION, static::ACTION_SET_PRICE );
	}
	

}