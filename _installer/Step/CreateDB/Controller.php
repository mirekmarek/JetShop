<?php
/**
 *
 * @copyright Copyright (c) 2011-2021 Miroslav Marek <mirek.marek.2m@gmail.com>
 * @license http://www.php-jet.net/license/license.txt
 * @author Miroslav Marek <mirek.marek.2m@gmail.com>
 */

namespace JetShop\Installer;

use Exception;
use Jet\DataModel_Helper;

use JetShop\Auth_Administrator_Role;
use JetShop\Auth_Administrator_Role_Privilege;
use JetShop\Auth_Administrator_User;
use JetShop\Auth_Administrator_User_Roles;

use JetShop\Auth_RESTClient_Role;
use JetShop\Auth_RESTClient_Role_Privilege;
use JetShop\Auth_RESTClient_User;
use JetShop\Auth_RESTClient_User_Roles;

use JetShop\Customer;
use JetShop\Logger_Admin_Event;
use JetShop\Logger_Shop_Event;
use JetShop\Logger_REST_Event;

/**
 *
 */
class Installer_Step_CreateDB_Controller extends Installer_Step_Controller
{

	/**
	 * @var string
	 */
	protected string $label = 'Create database';

	/**
	 * @return bool
	 */
	public function getIsAvailable(): bool
	{
		return !Installer_Step_CreateSite_Controller::sitesCreated();
	}


	/**
	 *
	 */
	public function main(): void
	{
		$this->catchContinue();


		$classes = [
			//TODO: to zdaleka neni vse ..

			Auth_Administrator_Role::class,
			Auth_Administrator_Role_Privilege::class,
			Auth_Administrator_User::class,
			Auth_Administrator_User_Roles::class,

			Customer::class,

			Auth_RESTClient_Role::class,
			Auth_RESTClient_Role_Privilege::class,
			Auth_RESTClient_User::class,
			Auth_RESTClient_User_Roles::class,

			Logger_Admin_Event::class,
			Logger_Shop_Event::class,
			Logger_REST_Event::class,
		];

		$result = [];
		$OK = true;

		foreach( $classes as $class ) {
			$result[$class] = true;
			try {
				DataModel_Helper::create( $class );
			} catch( Exception $e ) {
				$result[$class] = $e->getMessage();
				$OK = false;
			}

		}

		$this->view->setVar( 'result', $result );
		$this->view->setVar( 'OK', $OK );

		$this->render( 'default' );
	}

}
