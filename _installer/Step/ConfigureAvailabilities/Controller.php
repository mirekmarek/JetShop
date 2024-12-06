<?php
/**
 *
 * @copyright Copyright (c) Miroslav Marek <mirek.marek@web-jet.cz>
 * @license http://www.php-jet.net/license/license.txt
 * @author Miroslav Marek <mirek.marek@web-jet.cz>
 */

namespace JetApplication\Installer;

use Jet\Form;
use Jet\Form_Field_RadioButton;
use Jet\Tr;
/**
 *
 */
class Installer_Step_ConfigureAvailabilities_Controller extends Installer_Step_Controller
{
	protected string $icon = 'circle-info';
	
	/**
	 * @var string
	 */
	protected string $label = 'Config Availabilities';
	
	
	/**
	 *
	 */
	public function main(): void
	{

		$filds = [];
		
		
		$strategy = new Form_Field_RadioButton('strategy', '');
		$strategy->setSelectOptions([
			'global' => Tr::_('One global goods availability info'),
			'by_locale' => Tr::_('Each localization has its own goods availability info'),
		]);
		$strategy->setDefaultValue( Installer::getAvailabilityStrategy() );
		$strategy->setFieldValueCatcher( function( string $strategy ) {
			Installer::setAvailabilityStrategy( $strategy );
		} );
		
		$filds[] = $strategy;
		
		$form = new Form('setup_form', $filds);
		
		if($form->catch()) {
			Installer::goToNext();
		}
		
		$this->view->setVar( 'form', $form );
		
		$this->render( 'default' );
	}
	
}
