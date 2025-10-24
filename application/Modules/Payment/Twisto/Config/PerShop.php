<?php
/**
 * @copyright Copyright (c) Miroslav Marek <mirek.marek@web-jet.cz>
 * @license EUPL 1.2  https://eupl.eu/1.2/en/
 * @author Miroslav Marek <mirek.marek@web-jet.cz>
 */
namespace JetApplicationModule\Payment\Twisto;



use Jet\Config_Definition;
use Jet\Form_Definition;
use Jet\Form_Definition_Interface;
use Jet\Form_Definition_Trait;
use Jet\Form_Field;
use JetApplication\EShopConfig_ModuleConfig_PerShop;
use Jet\Config;

#[Config_Definition(
	name: 'Twisto'
)]
class Config_PerShop extends EShopConfig_ModuleConfig_PerShop implements Form_Definition_Interface {
	use Form_Definition_Trait;
	
	#[Config_Definition(
		type: Config::TYPE_STRING,
		
	)]
	#[Form_Definition(
		type: Form_Field::TYPE_INPUT,
		label: 'API URL: ',
		
	)]
	protected string $API_URL = '';
	
	#[Config_Definition(
		type: Config::TYPE_STRING,
		
	)]
	#[Form_Definition(
		type: Form_Field::TYPE_INPUT,
		label: 'Secret key: ',
		
	)]
	protected string $secret_key = '';

	
	#[Config_Definition(
		type: Config::TYPE_STRING,
		
	)]
	#[Form_Definition(
		type: Form_Field::TYPE_INPUT,
		label: 'Public key: ',
		
	)]
	protected string $public_key = '';
	
	public function getApiUrl(): string
	{
		return $this->API_URL;
	}
	
	public function setApiUrl( string $API_URL ): void
	{
		$this->API_URL = $API_URL;
	}
	

	public function getSecretkey(): string
	{
		return $this->secret_key;
	}
	

	public function setSecretkey( string $secret_key ): void
	{
		$this->secret_key = $secret_key;
	}

	
	public function getPublickey(): string
	{
		return $this->public_key;
	}
	
	public function setPublickey( string $public_key ): void
	{
		$this->public_key = $public_key;
	}
	
	
}