<?php

/**
 *
 * @copyright 
 * @license  
 * @author  
 */
namespace JetApplicationModule\Shop\OAuth\Backend\Facebook;

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
	
	public const OAUTH_SERVICE_ID = 'facebook';
	
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
	
	protected function getUserDetailURL( ?Shops_Shop $shop = null ): string
	{
		return $this->getConfig( $shop )->getUserDetailEndpointURL();
	}
	
	public function handleTokenResponse( Shop_OAuth_UserHandler $user_handler ) : bool
	{
		
		if(empty($this->last_response_data['access_token'])) {
			return false;
		}
		
		if($this->_request(
			static::METHOD_GET,
			$this->getUserDetailURL(),
			auth_token: $this->last_response_data['access_token']
		)) {
			
			$profile = $this->last_response_data;
			
			if(
				$profile['id']??'' &&
				$profile['email']??''
			) {
				
				$user_handler->setOauthUserId( $profile['id'] );
				$user_handler->setOauthUserEmail( $profile['email'] );
				
				return true;
			}
		}
		
		
		return false;
	}
	
	
	public function getControlCentreGroup(): string
	{
		return Admin_ControlCentre::GROUP_MAIN;
	}
	
	
	public function getControlCentreTitle(): string
	{
		return 'OAuth - Facebook';
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