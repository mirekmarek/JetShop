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
	
	public function handleTokenResponse( EShop_OAuth_UserHandler $user_handler ) : bool
	{
		
		$id_token = $this->last_response_data['id_token'];
		
		if( substr_count( $id_token, '.' ) != 2 ) {
			return false;
		}
		
		$parts = explode( '.', $id_token );
		
		$payload = json_decode(base64_decode($parts[1]), true);
		
		
		$user_handler->setOauthUserId( $payload['sub'] );
		$user_handler->setOauthUserEmail( $payload['email'] );
		
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
	
	public function getHandlerUrl(): string
	{
		return $this->handler_url.'/';
	}
	
}