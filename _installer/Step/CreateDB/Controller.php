<?php
/**
 *
 * @copyright Copyright (c) Miroslav Marek <mirek.marek@web-jet.cz>
 * @license http://www.php-jet.net/license/license.txt
 * @author Miroslav Marek <mirek.marek@web-jet.cz>
 */

namespace JetApplication\Installer;

use Exception;
use Jet\DataModel_Helper;

/**
 *
 */
class Installer_Step_CreateDB_Controller extends Installer_Step_Controller
{
	protected string $icon = 'square-plus';

	/**
	 * @var string
	 */
	protected string $label = 'Create database';

	/**
	 * @return bool
	 */
	public function getIsAvailable(): bool
	{
		return !Installer_Step_CreateBases_Controller::basesCreated();
	}


	/**
	 *
	 */
	public function main(): void
	{
		$this->catchContinue();


		$classes = [];

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
