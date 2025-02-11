<?php
/**
 * @copyright Copyright (c) Miroslav Marek <mirek.marek@web-jet.cz>
 * @license EUPL 1.2  https://eupl.eu/1.2/en/
 * @author Miroslav Marek <mirek.marek@web-jet.cz>
 */
namespace JetApplicationModule\Translator\GTranslate;

use Jet\Config;
use Jet\Config_Definition;
use Jet\Form_Definition;
use Jet\Form_Definition_Interface;
use Jet\Form_Definition_Trait;
use Jet\Form_Field;
use JetApplication\EShopConfig_ModuleConfig_General;

#[Config_Definition(
	name: 'Google Translate',
)]
class Config_General extends EShopConfig_ModuleConfig_General implements Form_Definition_Interface {
	use Form_Definition_Trait;
	
	#[Config_Definition(
		type: Config::TYPE_STRING,
		is_required: true,
	)]
	#[Form_Definition(
		type: Form_Field::TYPE_INPUT,
		label: 'Google API key:',
	)]
	protected string $google_api_key = '';
	
	public function getGoogleApiKey(): string
	{
		return $this->google_api_key;
	}
	
	public function setGoogleApiKey( string $google_api_key ): void
	{
		$this->google_api_key = $google_api_key;
	}
	
	
	
}