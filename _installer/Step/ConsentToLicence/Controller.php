<?php
/**
 *
 * @copyright Copyright (c) Miroslav Marek <mirek.marek@web-jet.cz>
 * @license http://www.php-jet.net/license/license.txt
 * @author Miroslav Marek <mirek.marek@web-jet.cz>
 */

namespace JetApplication\Installer;

use Jet\IO_Dir;
use Jet\Locale;

/**
 *
 */
class Installer_Step_ConsentToLicence_Controller extends Installer_Step_Controller
{
	
	/**
	 * @var string
	 */
	protected string $icon = 'scale-balanced';

	/**
	 * @var string
	 */
	protected string $label = 'Consent to the licence';

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
		$this->catchContinue();
		
		$default_language = 'en';
		$supported_languages = [];
		
		$files = IO_Dir::getFilesList( __DIR__.'/view/l/', '*.html' );
		foreach($files as $path=>$file) {
			$supported_languages[] = pathinfo($file, PATHINFO_FILENAME);
		}
		
		$language = Locale::getCurrentLocale()->getLanguage();
		if(!in_array($language, $supported_languages)) {
			$language = $default_language;
		}
		
		$this->view->setVar('language', $language);
		
		$this->render( 'default' );
	}

}
