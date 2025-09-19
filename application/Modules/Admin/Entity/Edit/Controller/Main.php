<?php
/**
 * @copyright Copyright (c) Miroslav Marek <mirek.marek@web-jet.cz>
 * @license EUPL 1.2  https://eupl.eu/1.2/en/
 * @author Miroslav Marek <mirek.marek@web-jet.cz>
 */
namespace JetApplicationModule\Admin\Entity\Edit;


use Jet\AJAX;
use Jet\Http_Request;
use Jet\Locale;
use Jet\MVC_Controller_Default;
use Jet\Tr;
use JetApplication\EShops;
use JetApplication\Application_Service_General;
use Error;
use Exception;


class Controller_Main extends MVC_Controller_Default
{
	public function resolve() : bool|string
	{
		$action = Http_Request::GET()->getString( 'action', '', valid_values: ['translate'] );
		if(!$action) {
			return false;
		}
		
		return $action;
	}
	
	protected function getLocale( string $key ) : ?Locale
	{
		$POST = Http_Request::POST();
		$eshops = array_keys( EShops::getList() );
		$locales = array_keys( EShops::getAvailableLocales() );
		
		$eshop = $POST->getString( $key.'_eshop', default_value: '', valid_values: $eshops );
		$eshop = $eshop ? EShops::get( $eshop ) : null;
		$locale = $POST->getString( $key.'_locale', default_value: '', valid_values: $locales );
		$locale = $locale ? new Locale( $locale ) : null;
		if(
			!$locale &&
			$eshop
		) {
			$locale = $eshop->getLocale();
		}
		
		return $locale??null;
	}
	
	public function translate_Action() : void
	{
		$translator = Application_Service_General::Translator();
		if(!$translator) {
			AJAX::operationResponse(false, data: ['error_message' => Tr::_('Translator is not available')]);
		}
		
		$from_locale = $this->getLocale('from');
		$to_locale = $this->getLocale('to');
		
		if(!$from_locale) {
			AJAX::operationResponse(false, data: ['error_message' => Tr::_('Unknown locale: from')]);
		}
		
		if(!$to_locale) {
			AJAX::operationResponse(false, data: ['error_message' => Tr::_('Unknown locale: to')]);
		}
		
		$POST = Http_Request::POST();
		$text = $POST->getRaw('text', default_value: '');
		$translation = '';
		if($text) {
			try {
				if( strlen($text)<=255 ) {
					$translation = $translator->translateShortText( $from_locale, $to_locale, $text );
				} else {
					$translation = $translator->translateLongText( $from_locale, $to_locale, $text );
				}
			} catch( Error|Exception $e ) {
				AJAX::operationResponse(false, data: [
					'error_message' =>
						Tr::_('Error during translation: %ERROR%', data: ['ERROR' => $e->getMessage()])
				]);
			}
		}
		
		
		AJAX::operationResponse(true, data: ['translation' => $translation]);
	}
}