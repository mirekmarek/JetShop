<?php
/**
 * @copyright Copyright (c) Miroslav Marek <mirek.marek@web-jet.cz>
 * @license EUPL 1.2  https://eupl.eu/1.2/en/
 * @author Miroslav Marek <mirek.marek@web-jet.cz>
 */
namespace JetApplicationModule\Translator\GTranslate;

use GTClient\Translator;
use Jet\Locale;
use JetApplication\Admin_ControlCentre;
use JetApplication\Admin_ControlCentre_Module_Interface;
use JetApplication\Admin_ControlCentre_Module_Trait;
use JetApplication\EShop;
use JetApplication\EShopConfig_ModuleConfig_General;
use JetApplication\EShopConfig_ModuleConfig_ModuleHasConfig_General_Interface;
use JetApplication\EShopConfig_ModuleConfig_ModuleHasConfig_General_Trait;
use JetApplication\Translator_Manager;

/**
 *
 */
class Main extends Translator_Manager implements
	EShopConfig_ModuleConfig_ModuleHasConfig_General_Interface,
	Admin_ControlCentre_Module_Interface
{
	use EShopConfig_ModuleConfig_ModuleHasConfig_General_Trait;
	use Admin_ControlCentre_Module_Trait;
	
	
	public function getControlCentreGroup(): string
	{
		return Admin_ControlCentre::GROUP_SYSTEM;
	}
	
	public function getControlCentreTitle(): string
	{
		return 'Google Translate';
	}
	
	public function getControlCentreIcon(): string
	{
		return 'earth-europe';
	}
	
	public function getControlCentrePriority(): int
	{
		return 99;
	}
	
	public function getControlCentrePerShopMode(): bool
	{
		return false;
	}
	
	public function getCongig(): Config_General|EShopConfig_ModuleConfig_General
	{
		return $this->getGeneralConfig();
	}
	
	protected function getClient() : ?Translator
	{
		$cfg = $this->getCongig();
		/**
		 * @var Config_General $cfg
		 */
		if(!$cfg->getGoogleApiKey()) {
			return null;
		}
		
		return new Translator( $cfg->getGoogleApiKey() );
	}
	
	public function translateShortText( Locale|EShop $from, Locale|EShop $to, string $text ): string
	{
		return $this->translate( $from, $to, $text, 'translateText' );
	}
	
	public function translateLongText( Locale|EShop $from, Locale|EShop $to, string $text ): string
	{
		return $this->translate( $from, $to, $text, 'translateHtml' );
	}
	
	protected function translate( Locale|EShop $from, Locale|EShop $to, string $text, string $method ) : string
	{
		$client = $this->getClient();
		if(!$client) {
			return $text;
		}
		
		if($from instanceof EShop) {
			$from_lg = $from->getLocale()->getLanguage();
		} else {
			$from_lg = $from->getLanguage();
		}
		
		if($to instanceof EShop) {
			$to_lg = $to->getLocale()->getLanguage();
		} else {
			$to_lg = $to->getLanguage();
		}
		
		if($from_lg==$to_lg) {
			$translation = $text;
		} else {
			$translation =  $client->{$method}( $from_lg, $to_lg, $text );
		}
		
		$translation = str_replace('<br />', '<br>', $translation);
		$translation = str_replace('<br/>', '<br>', $translation);
		$translation = str_replace("<br>\n", '<br>', $translation);
		$translation = str_replace("<br>\n", '<br>', $translation);
		$translation = str_replace("<br>\n", '<br>', $translation);
		$translation = str_replace("<br>", "<br>\n", $translation);
		
		return $translation;
	}
}