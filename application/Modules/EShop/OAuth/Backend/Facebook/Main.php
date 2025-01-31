<?php
/**
 * @copyright Copyright (c) Miroslav Marek <mirek.marek@web-jet.cz>
 * @license EUPL 1.2  https://eupl.eu/1.2/en/
 * @author Miroslav Marek <mirek.marek@web-jet.cz>
 */
namespace JetApplicationModule\EShop\OAuth\Backend\Facebook;


use JetApplication\Admin_ControlCentre;
use JetApplication\Admin_ControlCentre_Module_Interface;
use JetApplication\Admin_ControlCentre_Module_Trait;
use JetApplication\EShopConfig_ModuleConfig_ModuleHasConfig_PerShop_Interface;
use JetApplication\EShopConfig_ModuleConfig_ModuleHasConfig_PerShop_Trait;
use JetApplication\EShop_ModuleUsingTemplate_Interface;
use JetApplication\EShop_ModuleUsingTemplate_Trait;
use JetApplication\EShop_OAuth_BackendModule;
use JetApplication\EShop_OAuth_UserHandler;
use JetApplication\EShopConfig_ModuleConfig_PerShop;
use JetApplication\EShops;
use JetApplication\EShop;


class Main extends EShop_OAuth_BackendModule implements
	EShop_ModuleUsingTemplate_Interface,
	EShopConfig_ModuleConfig_ModuleHasConfig_PerShop_Interface,
	Admin_ControlCentre_Module_Interface
{
	use EShop_ModuleUsingTemplate_Trait;
	use EShopConfig_ModuleConfig_ModuleHasConfig_PerShop_Trait;
	use Admin_ControlCentre_Module_Trait;
	
	public const OAUTH_SERVICE_ID = 'facebook';
	
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
	
	protected function getUserDetailURL( ?EShop $eshop = null ): string
	{
		return $this->getConfig( $eshop )->getUserDetailEndpointURL();
	}
	
	public function handleTokenResponse( EShop_OAuth_UserHandler $user_handler ) : bool
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