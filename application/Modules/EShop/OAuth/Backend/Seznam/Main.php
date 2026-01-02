<?php
/**
 * @copyright Copyright (c) Miroslav Marek <mirek.marek@web-jet.cz>
 * @license EUPL 1.2  https://eupl.eu/1.2/en/
 * @author Miroslav Marek <mirek.marek@web-jet.cz>
 */
namespace JetApplicationModule\EShop\OAuth\Backend\Seznam;


use JetApplication\Admin_ControlCentre;
use JetApplication\Admin_ControlCentre_Module_Interface;
use JetApplication\Admin_ControlCentre_Module_Trait;
use JetApplication\EShopConfig_ModuleConfig_ModuleHasConfig_PerShop_Interface;
use JetApplication\EShopConfig_ModuleConfig_ModuleHasConfig_PerShop_Trait;
use JetApplication\EShop_ModuleUsingTemplate_Interface;
use JetApplication\EShop_ModuleUsingTemplate_Trait;
use JetApplication\Application_Service_EShop_OAuthBackendModule;
use JetApplication\EShop_OAuth_UserHandler;
use JetApplication\EShopConfig_ModuleConfig_PerShop;
use JetApplication\EShops;
use JetApplication\EShop;


class Main extends Application_Service_EShop_OAuthBackendModule implements
	EShop_ModuleUsingTemplate_Interface,
	EShopConfig_ModuleConfig_ModuleHasConfig_PerShop_Interface,
	Admin_ControlCentre_Module_Interface
{
	use EShop_ModuleUsingTemplate_Trait;
	use EShopConfig_ModuleConfig_ModuleHasConfig_PerShop_Trait;
	use Admin_ControlCentre_Module_Trait;
	
	public const OAUTH_SERVICE_ID = 'seznam';
	
	protected function getConfig( ?EShop $eshop=null ) : Config_PerShop|EShopConfig_ModuleConfig_PerShop
	{
		$eshop = $eshop ? : EShops::getCurrent();
		
		return $this->getEshopConfig( $eshop );
	}
	
	protected function getClientId( ?EShop $eshop = null ): string
	{
		return $this->getConfig( $eshop )->getClientId();
	}
	
	protected function getClientSecret( ?EShop $eshop = null ): string
	{
		return $this->getConfig( $eshop )->getClientSecret();
	}
	
	protected function getOAuthURL( ?EShop $eshop = null ): string
	{
		return $this->getConfig( $eshop )->getOauthEndpointURL();
	}
	
	protected function getTokenURL( ?EShop $eshop = null ): string
	{
		return $this->getConfig( $eshop )->getTokenEndpointURL();
	}
	
	public function getOAuthServiceAuthorizationLink() : string
	{
		$data = [
			'client_id'     => $this->getClientId(),
			'redirect_uri'  => $this->getHandlerUrl(),
			'scope'         => 'identity',
			'response_type' => 'code'
		];
		
		return  $this->getOAuthURL().'?'.http_build_query($data);
	}
	
	
	public function handleTokenResponse( EShop_OAuth_UserHandler $user_handler ) : bool
	{
		$email = strtolower($this->last_response_data['account_name']??'');
		$oauth_user_id = $this->last_response_data['oauth_user_id']??'';
		
		if(!$email || !$oauth_user_id) {
			return false;
		}
		
		$user_handler->setOauthUserId( $oauth_user_id );
		$user_handler->setOauthUserEmail( $email );
		
		return true;
	}
	
	
	public function getControlCentreGroup(): string
	{
		return Admin_ControlCentre::GROUP_MAIN;
	}
	
	
	public function getControlCentreTitle(): string
	{
		return 'OAuth - Seznam';
	}
	
	public function getControlCentreIcon(): string
	{
		return 'key';
	}
	
	public function getControlCentrePriority(): int
	{
		return 99;
	}
	
	public function getControlCentrePerShopMode(): bool
	{
		return true;
	}
	
}