<?php
/**
 * @copyright Copyright (c) Miroslav Marek <mirek.marek@web-jet.cz>
 * @license EUPL 1.2  https://eupl.eu/1.2/en/
 * @author Miroslav Marek <mirek.marek@web-jet.cz>
 */
namespace JetApplicationModule\Payment\HomeCredit;



use Jet\Config_Definition;
use Jet\Form_Definition;
use Jet\Form_Definition_Interface;
use Jet\Form_Definition_Trait;
use Jet\Form_Field;
use JetApplication\EShopConfig_ModuleConfig_PerShop;
use Jet\Config;

#[Config_Definition(
	name: 'HomeCredit'
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
		label: 'API - user name: ',
		
	)]
	protected string $username = '';

	
	#[Config_Definition(
		type: Config::TYPE_STRING,
		
	)]
	#[Form_Definition(
		type: Form_Field::TYPE_INPUT,
		label: 'API - password: ',
		
	)]
	protected string $password = '';
	
	#[Config_Definition(
		type: Config::TYPE_STRING,
	)]
	#[Form_Definition(
		type: Form_Field::TYPE_INPUT,
		label: 'Calc - URL: ',
	)]
	protected string $calc_URL = '';
	
	#[Config_Definition(
		type: Config::TYPE_STRING,
	)]
	#[Form_Definition(
		type: Form_Field::TYPE_INPUT,
		label: 'Calc - key: ',
	)]
	protected string $calc_key = '';
	
	public function getApiUrl(): string
	{
		return $this->API_URL;
	}
	
	public function setApiUrl( string $API_URL ): void
	{
		$this->API_URL = $API_URL;
	}
	

	public function getUsername(): string
	{
		return $this->username;
	}
	

	public function setUsername( string $username ): void
	{
		$this->username = $username;
	}

	
	public function getPassword(): string
	{
		return $this->password;
	}
	
	public function setPassword( string $password ): void
	{
		$this->password = $password;
	}
	
	public function getCalcURL(): string
	{
		return $this->calc_URL;
	}
	
	public function setCalcURL( string $calc_URL ): void
	{
		$this->calc_URL = $calc_URL;
	}
	
	public function getCalcKey(): string
	{
		return $this->calc_key;
	}
	
	public function setCalcKey( string $calc_key ): void
	{
		$this->calc_key = $calc_key;
	}
	
	
}