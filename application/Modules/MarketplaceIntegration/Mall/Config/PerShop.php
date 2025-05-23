<?php
/**
 * @copyright Copyright (c) Miroslav Marek <mirek.marek@web-jet.cz>
 * @license EUPL 1.2  https://eupl.eu/1.2/en/
 * @author Miroslav Marek <mirek.marek@web-jet.cz>
 */
namespace JetApplicationModule\MarketplaceIntegration\Mall;



use Jet\Config_Definition;
use Jet\Form_Definition;
use Jet\Form_Definition_Interface;
use Jet\Form_Definition_Trait;
use Jet\Form_Field;
use JetApplication\EShopConfig_ModuleConfig_PerShop;
use Jet\Config;

#[Config_Definition(
	name: 'Mall'
)]
class Config_PerShop extends EShopConfig_ModuleConfig_PerShop implements Form_Definition_Interface {
	use Form_Definition_Trait;
	
	#[Config_Definition(
		type: Config::TYPE_STRING,
		is_required: false,
	)]
	#[Form_Definition(
		type: Form_Field::TYPE_INPUT,
		label: 'API URL: ',
		is_required: false,
	)]
	protected string $API_URL = '';
	
	#[Config_Definition(
		type: Config::TYPE_STRING,
		is_required: false,
	)]
	#[Form_Definition(
		type: Form_Field::TYPE_INPUT,
		label: 'Client ID: ',
		is_required: false,
	)]
	protected string $client_id = '';
	
	#[Config_Definition(
		type: Config::TYPE_STRING,
		is_required: false,
	)]
	#[Form_Definition(
		type: Form_Field::TYPE_INPUT,
		label: 'Country code: ',
		is_required: false,
	)]
	protected string $country_code = '';
	
	
	#[Config_Definition(
		type: Config::TYPE_BOOL,
		is_required: false,
	)]
	#[Form_Definition(
		type: Form_Field::TYPE_CHECKBOX,
		label: 'Test mode'
	)]
	protected bool $test_mode = false;
	

	public function getApiUrl(): string
	{
		return $this->API_URL;
	}
	
	public function setApiUrl( string $API_URL ): void
	{
		$this->API_URL = $API_URL;
	}

	public function getClientId(): string
	{
		return $this->client_id;
	}

	public function setClientId( string $client_id ): void
	{
		$this->client_id = $client_id;
	}

	public function getCountryCode(): string
	{
		return $this->country_code;
	}
	
	public function setCountryCode( string $country_code ): void
	{
		$this->country_code = $country_code;
	}
	
	public function getTestMode(): bool
	{
		return $this->test_mode;
	}
	
	public function setTestMode( bool $test_mode ): void
	{
		$this->test_mode = $test_mode;
	}
}