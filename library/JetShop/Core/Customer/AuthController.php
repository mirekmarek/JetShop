<?php
/**
 *
 * @copyright Copyright (c) 2011-2021 Miroslav Marek <mirek.marek@web-jet.cz>
 *
 * @author Miroslav Marek <mirek.marek@web-jet.cz>
 */
/**
 * @copyright Copyright (c) Miroslav Marek <mirek.marek@web-jet.cz>
 * @license EUPL 1.2  https://eupl.eu/1.2/en/
 * @author Miroslav Marek <mirek.marek@web-jet.cz>
 */
namespace JetShop;


use Jet\Auth_User_Interface;
use Jet\BaseObject;
use Jet\Auth_Controller_Interface;

use Jet\MVC_Page_Interface;
use Jet\Session;
use Jet\Data_DateTime;

use JetApplication\Customer;
use JetApplication\EShop_Managers;
use JetApplication\EShops;

/**
 *
 */
abstract class Core_Customer_AuthController extends BaseObject implements Auth_Controller_Interface
{
	public const EVENT_LOGIN_FAILED = 'login_failed';
	public const EVENT_LOGIN_SUCCESS = 'login_success';
	public const EVENT_LOGOUT = 'logout';


	protected Customer|bool|null $current_user = null;
	
	public function checkCurrentUser() : bool
	{

		$user = $this->getCurrentUser();
		if( !$user ) {
			return false;
		}

		if( !$user->isActivated() ) {
			return false;
		}

		if( $user->isBlocked() ) {
			$till = $user->isBlockedTill();
			if( $till!==null&&$till<=Data_DateTime::now() ) {
				$user->unBlock();
				$user->save();
			} else {
				return false;
			}
		}

		if( !$user->getPasswordIsValid() ) {
			return false;
		}

		if( ( $pwd_valid_till = $user->getPasswordIsValidTill() )!==null&&$pwd_valid_till<=Data_DateTime::now() ) {
			$user->setPasswordIsValid( false );
			$user->save();

			return false;
		}

		return true;
	}
	
	public function getCurrentUser() : Customer|bool
	{
		if( $this->current_user!==null ) {
			return $this->current_user;
		}

		$user_id = $this->getSession()->getValue( 'user_id' );
		if(!$user_id) {
			return false;
		}


		$this->current_user = Customer::get( $user_id );


		if(!$this->current_user) {
			$this->current_user = false;
		} else {
			if(
				$this->current_user->isBlocked() ||
				$this->current_user->getEshop()->getKey()!=EShops::getCurrentKey()
			) {
				$this->current_user = false;
			}
		}
		
		

		return $this->current_user;
	}
	
	protected function getSession() : Session
	{
		return new Session( 'auth_eshop' );

	}
	
	public function handleLogin() : void
	{
		
		$module = EShop_Managers::CustomerLogin();
		
		$user = $this->getCurrentUser();

		if( $user ) {
			if( !$user->isActivated() ) {
				$module->handleIsNotActivated( $user );
				return;
			}
			
			if( $user->isBlocked() ) {
				$module->handleIsBlocked( $user );
				return;
			}
			
			if( !$user->getPasswordIsValid() ) {
				$module->handleMustChangePassword( $user );
				return;
			}
		}
		
		$module->handleLogin();
	}
	
	public function logout() : void
	{
		$this->getSession()->unsetValue( 'user_id' );
		$this->current_user = null;
	}
	
	public function login( string $username, string $password ) : bool
	{

		$customer = Customer::getByIdentity( $username, $password );

		if( !$customer ) {
			return false;
		}


		return $this->loginUser( $customer );
	}

	
	public function getCurrentUserHasPrivilege( string $privilege, mixed $value=null ): bool
	{
		$current_user = $this->getCurrentUser();

		if( !$current_user ) {
			return false;
		}

		return $current_user->hasPrivilege($privilege, $value);
	}
	
	public function checkModuleActionAccess( string $module_name, string $action ) : bool
	{
		return false;
	}

	public function checkPageAccess( MVC_Page_Interface $page ) : bool
	{
		return true;
	}
	
	public function loginUser( Auth_User_Interface $user ): bool
	{
		if(
			!$user instanceof Customer ||
			$user->getEshopKey()!=EShops::getCurrentKey() ||
			$user->isBlocked()
		) {
			return false;
		}
		
		$session = $this->getSession();
		$session->setValue( 'user_id', $user->getId() );
		
		$this->current_user = $user;
		
		return true;
	}

}