<?php
/**
 *
 * @copyright 
 * @license  
 * @author  
 */
namespace JetApplicationModule\Admin\Payment\Methods;

use Jet\Application_Module;
use Jet\Auth;
use JetApplication\Admin_Module_Trait;
use JetApplication\Auth_Administrator_Role;
use JetApplication\Payment_Method_ManageModuleInterface;

/**
 *
 */
class Main extends Application_Module implements Payment_Method_ManageModuleInterface
{
	use Admin_Module_Trait;

	const ADMIN_MAIN_PAGE = 'payment-method';

	const ACTION_GET_PAYMENT_METHOD = 'get_payment_method';
	const ACTION_ADD_PAYMENT_METHOD = 'add_payment_method';
	const ACTION_UPDATE_PAYMENT_METHOD = 'update_payment_method';
	const ACTION_DELETE_PAYMENT_METHOD = 'delete_payment_method';


	public function getPaymentMethodEditURL( string $id ): string
	{
		return $this->getEditUrl(
			static::ACTION_GET_PAYMENT_METHOD,
			static::ACTION_UPDATE_PAYMENT_METHOD,
			static::ADMIN_MAIN_PAGE,
			$id
		);
	}

	public static function getCurrentUserCanEditPaymentMethod(): bool
	{
		return Auth::getCurrentUserHasPrivilege( Auth_Administrator_Role::PRIVILEGE_MODULE_ACTION, static::ACTION_UPDATE_PAYMENT_METHOD );
	}

	public static function getCurrentUserCanCreatePaymentMethod(): bool
	{
		return Auth::getCurrentUserHasPrivilege( Auth_Administrator_Role::PRIVILEGE_MODULE_ACTION, static::ACTION_ADD_PAYMENT_METHOD );
	}

}