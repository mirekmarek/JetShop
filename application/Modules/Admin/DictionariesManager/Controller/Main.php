<?php
/**
 * @copyright Copyright (c) Miroslav Marek <mirek.marek@web-jet.cz>
 * @license EUPL 1.2  https://eupl.eu/1.2/en/
 * @author Miroslav Marek <mirek.marek@web-jet.cz>
 */
namespace JetApplicationModule\Admin\DictionariesManager;


use Jet\AJAX;
use Jet\Http_Request;
use Jet\MVC_Controller_Default;
use Jet\Translator;


class Controller_Main extends MVC_Controller_Default
{

	/**
	 *
	 */
	public function default_Action() : void
	{
		$GET = Http_Request::GET();
		$POST = Http_Request::POST();
		
		$locale = null;
		$dictionaries = [];
		$dictionary = null;
		$prev_dictionary = null;
		$next_dictionary = null;
		
		$locales = Translator::getKnownLocales();
		$locale_code = $GET->getString('locale', valid_values: array_keys($locales));
		if($locale_code) {
			$locale = $locales[$locale_code];
		}
		
		if($locale) {
			$dictionaries = Translator::getKnownDictionaries( $locale );
			$dictionary_name = $GET->getString('dictionary', valid_values: array_keys($dictionaries));
			if($dictionary_name) {
				$dictionary = Translator::loadDictionary( $dictionary_name, $locale, true );
				
				$_dictionaries = array_keys($dictionaries);
				$idx = array_search( $dictionary->getName(), $_dictionaries );
				if($idx>0) {
					$prev_dictionary = $_dictionaries[$idx-1];
				}
				$idx++;
				if(isset($_dictionaries[$idx])) {
					$next_dictionary = $_dictionaries[$idx];
				}
			}
		}
		
		$this->view->setVar('locales', $locales);
		$this->view->setVar('locale', $locale);
		$this->view->setVar('dictionaries', $dictionaries);
		$this->view->setVar('dictionary', $dictionary);
		$this->view->setVar('prev_dictionary', $prev_dictionary);
		$this->view->setVar('next_dictionary', $next_dictionary);
		
		if($dictionary) {
			switch($GET->getString('action')) {
				case 'save_translation':
					$hash = base64_decode($POST->getString('hash'));
					$translation = $POST->getRaw('translation');
					foreach($dictionary->getPhrases() as $phrase) {
						if($phrase->getHash()==$hash) {
							$phrase->setTranslation( $translation );
							$phrase->setIsTranslated( (bool)$translation );
							
							Translator::saveDictionary( $dictionary );
						}
					}
					AJAX::operationResponse(true);
					break;
				case 'remove_phrase':
					$hash = base64_decode($POST->getString('hash'));
					$translation = $POST->getRaw('translation');
					foreach($dictionary->getPhrases() as $phrase) {
						if($phrase->getHash()==$hash) {
							$dictionary->removePhrase( $phrase );
							Translator::saveDictionary( $dictionary );
						}
					}
					AJAX::operationResponse(true);
					break;
			}
		}
		
		$this->output('default');
	}
}