<?php
/**
 * @copyright Copyright (c) Miroslav Marek <mirek.marek@web-jet.cz>
 * @license EUPL 1.2  https://eupl.eu/1.2/en/
 * @author Miroslav Marek <mirek.marek@web-jet.cz>
 */
namespace JetApplicationModule\EShop\OAuth\Backend\Facebook;


use Jet\Config_Definition;
use Jet\Form_Definition;
use Jet\Form_Definition_Interface;
use Jet\Form_Definition_Trait;
use Jet\Form_Field;
use JetApplication\EShopConfig_ModuleConfig_PerShop;
use Jet\Config;

#[Config_Definition(
	name: 'GoogleOAuth'
)]
class Config_PerShop extends EShopConfig_ModuleConfig_PerShop implements Form_Definition_Interface {
	use Form_Definition_Trait;
	
	#[Config_Definition(
		type: Config::TYPE_STRING,
		is_required: true,
	)]
	#[Form_Definition(
		type: Form_Field::TYPE_INPUT,
		label: 'Client ID: ',
		is_required: true,
	)]
	protected string $client_id = '';
	
	
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
	
	#[Config_Definition(
		type: Config::TYPE_STRING,
	)]
	#[Form_Definition(
		type: Form_Field::TYPE_INPUT,
		label: 'Auth Endpoint URL: ',
		is_required: true,
	)]
	protected string $oauth_endpoint_URL = 'https://www.facebook.com/dialog/oauth';
	
	#[Config_Definition(
		type: Config::TYPE_STRING,
	)]
	#[Form_Definition(
		type: Form_Field::TYPE_INPUT,
		label: 'Token Endpoint URL: ',
		is_required: true,
	)]
	protected string $token_endpoint_URL = 'https://graph.facebook.com/oauth/access_token';
	
	#[Config_Definition(
		type: Config::TYPE_STRING,
	)]
	#[Form_Definition(
		type: Form_Field::TYPE_INPUT,
		label: 'User detail Endpoint URL: ',
		is_required: true,
	)]
	protected string $user_detail_endpoint_URL = 'https://graph.facebook.com/v20.0/me?fields=email';
	
	
	public function getClientId(): string
	{
		return $this->client_id;
	}
	
	public function setClientId( string $client_id ): void
	{
		$this->client_id = $client_id;
	}
	
	public function getClientSecret(): string
	{
		return $this->client_secret;
	}
	
	public function setClientSecret( string $client_secret ): void
	{
		$this->client_secret = $client_secret;
	}
	
	public function getOauthEndpointURL(): string
	{
		return $this->oauth_endpoint_URL;
	}
	
	public function setOauthEndpointURL( string $oauth_endpoint_URL ): void
	{
		$this->oauth_endpoint_URL = $oauth_endpoint_URL;
	}
	
	public function getTokenEndpointURL(): string
	{
		return $this->token_endpoint_URL;
	}
	
	public function setTokenEndpointURL( string $token_endpoint_URL ): void
	{
		$this->token_endpoint_URL = $token_endpoint_URL;
	}
	
	public function getUserDetailEndpointURL(): string
	{
		return $this->user_detail_endpoint_URL;
	}
	
	public function setUserDetailEndpointURL( string $user_detail_endpoint_URL ): void
	{
		$this->user_detail_endpoint_URL = $user_detail_endpoint_URL;
	}
	
	
}