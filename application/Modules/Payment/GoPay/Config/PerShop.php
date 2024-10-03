<?php
/**
 *
 * @copyright
 * @license
 * @author
 */
namespace JetApplicationModule\Payment\GoPay;


use Jet\Config_Definition;
use Jet\Form_Definition;
use Jet\Form_Definition_Interface;
use Jet\Form_Definition_Trait;
use Jet\Form_Field;
use JetApplication\ShopConfig_ModuleConfig_PerShop;
use Jet\Config;

#[Config_Definition(
	name: 'GoPay'
)]
class Config_PerShop extends ShopConfig_ModuleConfig_PerShop implements Form_Definition_Interface {
	use Form_Definition_Trait;
	
	#[Config_Definition(
		type: Config::TYPE_STRING,
		is_required: true,
	)]
	#[Form_Definition(
		type: Form_Field::TYPE_INPUT,
		label: 'GoPay API URL: ',
		is_required: true,
	)]
	protected string $API_URL = '';
	
	#[Config_Definition(
		type: Config::TYPE_STRING,
		is_required: true,
	)]
	#[Form_Definition(
		type: Form_Field::TYPE_INPUT,
		label: 'Client ID: ',
		is_required: true,
	)]
	protected string $client_ID = '';
	
	
	#[Config_Definition(
		type: Config::TYPE_STRING,
		is_required: true,
	)]
	#[Form_Definition(
		type: Form_Field::TYPE_INPUT,
		label: 'Go ID: ',
		is_required: true,
	)]
	protected string $go_ID = '';
	
	#[Config_Definition(
		type: Config::TYPE_STRING,
		is_required: true,
	)]
	#[Form_Definition(
		type: Form_Field::TYPE_INPUT,
		label: 'Client secret: ',
		is_required: true,
	)]
	protected string $client_secret = '';
	
	public function getApiUrl(): string
	{
		return $this->API_URL;
	}
	
	public function setApiUrl( string $API_URL ): void
	{
		$this->API_URL = $API_URL;
	}
	

	public function getClientID(): string
	{
		return $this->client_ID;
	}
	

	public function setClientID( string $client_ID ): void
	{
		$this->client_ID = $client_ID;
	}
	
	public function getGoID(): string
	{
		return $this->go_ID;
	}
	
	public function setGoID( string $go_ID ): void
	{
		$this->go_ID = $go_ID;
	}
	
	public function getClientSecret(): string
	{
		return $this->client_secret;
	}
	
	public function setClientSecret( string $client_secret ): void
	{
		$this->client_secret = $client_secret;
	}
	
	
	public function getGoPayConfig() : GoPay_Config
	{
		$gopay_config = new GoPay_Config();
		
		$gopay_config->setAPIUrl( $this->API_URL );
		$gopay_config->setClientID( $this->client_ID );
		$gopay_config->setGoID( $this->go_ID );
		$gopay_config->setClientSecret( $this->client_secret );
		
		return $gopay_config;
	}
	
}