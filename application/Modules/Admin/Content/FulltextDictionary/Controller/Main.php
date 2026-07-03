<?php
/**
 * @copyright Copyright (c) Miroslav Marek <mirek.marek@web-jet.cz>
 * @license EUPL 1.2  https://eupl.eu/1.2/en/
 * @author Miroslav Marek <mirek.marek@web-jet.cz>
 */
namespace JetApplicationModule\Admin\Content\FulltextDictionary;


use Jet\AJAX;
use Jet\Http_Request;

use Jet\Locale;
use Jet\MVC_Controller_Default;
use JetApplication\EShops;
use JetApplication\FulltextSearch_Dictionary;


class Controller_Main extends MVC_Controller_Default
{
	
	public function default_Action() : void
	{
		
		$GET = Http_Request::GET();
		$POST = Http_Request::POST();
		
		$locales = [];
		$default_locale = null;
		foreach(EShops::getList() as $eshop) {
			$locale = $eshop->getLocale();
			$locale_str = $locale->toString();
			$locales[$locale_str] = $locale;
			if(!$default_locale) {
				$default_locale = $locale;
			}
		}
		
		$selected_locale = new Locale($GET->getString('locale', default_value: $default_locale->toString(),valid_values: array_keys($locales)));
		
		
		
		$this->view->setVar('locales', $locales );
		$this->view->setVar('selected_locale', $selected_locale );
		
		switch($GET->getString('action')) {
			case 'add':
				$note = $POST->getString('note');
				$words = $POST->getString('words');
				
				$rec = new FulltextSearch_Dictionary();
				$rec->setNote( $note );
				$rec->setWords( $words );
				$rec->setLocale( $selected_locale );
				$rec->save();
				
				
				AJAX::operationResponse(true, data: $rec->jsonSerialize() );
				
				break;
			case 'save':
				$id = $POST->getString('id');
				$note = $POST->getString('note');
				$words = $POST->getString('words');
				
				$rec = FulltextSearch_Dictionary::load( $id );
				$rec->setNote( $note );
				$rec->setWords( $words );
				$rec->save();
				
				AJAX::operationResponse(true, data: $rec->jsonSerialize() );
				
				break;
			case 'delete':
				$id = $POST->getString('id');
				
				$rec = FulltextSearch_Dictionary::load( $id );
				$rec->delete();
				
				AJAX::operationResponse(true);
				break;
		}
		
		
		$this->output('default');
	}
}