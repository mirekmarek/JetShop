<?php
/**
 * @copyright Copyright (c) Miroslav Marek <mirek.marek@web-jet.cz>
 * @license EUPL 1.2  https://eupl.eu/1.2/en/
 * @author Miroslav Marek <mirek.marek@web-jet.cz>
 */
namespace JetShop;


use Jet\Application_Module;
use Jet\Locale;
use JetApplication\EShop;
use JetApplication\Managers_General;
use JetApplication\Translator_Manager;

abstract class Core_Translator {
	
	public static function getManager() : Application_Module|Translator_Manager|null
	{
		return Managers_General::Translator();
	}
	
	public function translateShortText( EShop|Locale $from, EShop|Locale $to, string $text ): string
	{
		return static::getManager()?->translateShortText( $from, $to, $text )??$text;
	}
	
	public function translateLongText( EShop|Locale $from, EShop|Locale $to, string $text ): string
	{
		return static::getManager()?->translateLongText( $from, $to, $text )??$text;
	}
	

}