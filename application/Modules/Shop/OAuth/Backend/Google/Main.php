<?php

/**
 *
 * @copyright 
 * @license  
 * @author  
 */
namespace JetApplicationModule\Shop\OAuth\Backend\Google;

use JetApplication\Admin_ControlCentre;
use JetApplication\Admin_ControlCentre_Module_Interface;
use JetApplication\Admin_ControlCentre_Module_Trait;
use JetApplication\ShopConfig_ModuleConfig_ModuleHasConfig_PerShop_Interface;
use JetApplication\ShopConfig_ModuleConfig_ModuleHasConfig_PerShop_Trait;
use JetApplication\Shop_ModuleUsingTemplate_Interface;
use JetApplication\Shop_ModuleUsingTemplate_Trait;
use JetApplication\Shop_OAuth_BackendModule;
use JetApplication\Shop_OAuth_UserHandler;
use JetApplication\ShopConfig_ModuleConfig_PerShop;
use JetApplication\Shops;
use JetApplication\Shops_Shop;

/**
 *
 */
class Main extends Shop_OAuth_BackendModule implements
	Shop_ModuleUsingTemplate_Interface,
	ShopConfig_ModuleConfig_ModuleHasConfig_PerShop_Interface,
	Admin_ControlCentre_Module_Interface
{
	use Shop_ModuleUsingTemplate_Trait;
	use ShopConfig_ModuleConfig_ModuleHasConfig_PerShop_Trait;
	use Admin_ControlCentre_Module_Trait;
	
	public const OAUTH_SERVICE_ID = 'google';
	
	protected function getConfig( ?Shops_Shop $shop=null ) : Config_PerShop|ShopConfig_ModuleConfig_PerShop
	{
		$shop = $shop ? : Shops::getCurrent();
		
		return $this->getShopConfig( $shop );
	}
	
	protected function getClientId( ?Shops_Shop $shop = null ): string
	{
		return $this->getConfig( $shop )->getClientId();
	}
	
	protected function getClientSecret( ?Shops_Shop $shop = null ): string
	{
		return $this->getConfig( $shop )->getClientSecret();
	}
	
	protected function getOAuthURL( ?Shops_Shop $shop = null ): string
	{
		return $this->getConfig( $shop )->getOauthEndpointURL();
	}
	
	protected function getTokenURL( ?Shops_Shop $shop = null ): string
	{
		return $this->getConfig( $shop )->getTokenEndpointURL();
	}
	
	public function handleTokenResponse( Shop_OAuth_UserHandler $user_handler ) : bool
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
		return 'OAuth - Google';
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