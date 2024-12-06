<?php
/**
 *
 * @copyright Copyright (c) Miroslav Marek <mirek.marek@web-jet.cz>
 * @license http://www.php-jet.net/license/license.txt
 * @author Miroslav Marek <mirek.marek@web-jet.cz>
 */

namespace JetApplication\Installer;

use Jet\Form;
use Jet\Form_Field_Checkbox;
use Jet\Locale;
use JetApplication\DataList;

/**
 *
 */
class Installer_Step_SelectLocales_Controller extends Installer_Step_Controller
{
	protected string $icon = 'language';
	
	/**
	 * @var string
	 */
	protected string $label = 'Select Locales';

	/**
	 * @return bool
	 */
	public function getIsAvailable(): bool
	{
		return true;
	}

	/**
	 *
	 */
	public function main(): void
	{

		$locale_fields = [];

		$selected_locales = Installer::getSelectedEshopLocales();

		
		foreach( DataList::locales() as $locale_code=>$locale_name ) {

			$field = new Form_Field_Checkbox( 'locale_' . $locale_code, $locale_name );
			$field->setDefaultValue( isset( $selected_locales[$locale_code] ) );

			$locale_fields[] = $field;
		}

		$select_locale_form = new Form( 'select_locale_form', $locale_fields );

		$select_locale_form->setDoNotTranslateTexts( true );


		if( $select_locale_form->catchInput() && $select_locale_form->validate() ) {
			$selected_locales = [];
			
			foreach( DataList::locales() as $locale_code=>$locale_name ) {
				$field = $select_locale_form->field( 'locale_' . $locale_code );
				if( $field->getValue() ) {
					$selected_locales[] = new Locale( $locale_code );
				}
			}

			Installer::setSelectedEshopLocales( $selected_locales );
			Installer::initBases();
			
			Installer::goToNext();
		}


		$this->view->setVar( 'form', $select_locale_form );

		$this->render( 'default' );
	}

}
