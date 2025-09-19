<?php
/**
 * @copyright Copyright (c) Miroslav Marek <mirek.marek@web-jet.cz>
 * @license EUPL 1.2  https://eupl.eu/1.2/en/
 * @author Miroslav Marek <mirek.marek@web-jet.cz>
 */
namespace JetApplicationModule\SysServices\Manager;


use Error;
use Jet\Application;
use Jet\Auth;
use Jet\Auth_Controller_Interface;
use Jet\Auth_User_Interface;
use Jet\Debug;
use Jet\ErrorPages;
use Jet\Http_Headers;
use Jet\Http_Request;
use Jet\Locale;
use Jet\Logger;
use Jet\MVC;
use Jet\MVC_Page_Interface;
use Jet\Tr;
use JetApplication\Admin_ControlCentre;
use JetApplication\Admin_ControlCentre_Module_Interface;
use JetApplication\Admin_ControlCentre_Module_Trait;
use JetApplication\Logger_SysServices;
use JetApplication\EShopConfig_ModuleConfig_ModuleHasConfig_General_Interface;
use JetApplication\EShopConfig_ModuleConfig_ModuleHasConfig_General_Trait;
use JetApplication\EShops;
use JetApplication\EShop;
use JetApplication\SysServices;
use JetApplication\SysServices_Definition;
use JetApplication\Application_Service_General_SysServices;
use JetApplication\EShopConfig_ModuleConfig_General;


class Main extends Application_Service_General_SysServices implements
	Admin_ControlCentre_Module_Interface,
	EShopConfig_ModuleConfig_ModuleHasConfig_General_Interface
{
	use Admin_ControlCentre_Module_Trait;
	use EShopConfig_ModuleConfig_ModuleHasConfig_General_Trait;
	
	
	public function getControlCentreGroup(): string
	{
		return Admin_ControlCentre::GROUP_SYSTEM;
	}
	
	public function getControlCentreTitle(): string
	{
		return Tr::_('System services');
	}
	
	public function getControlCentreIcon(): string
	{
		return 'gears';
	}
	
	public function getControlCentrePriority(): int
	{
		return 1;
	}
	
	public function getControlCentrePerShopMode(): bool
	{
		return false;
	}
	
	protected function getConfig() : EShopConfig_ModuleConfig_General|Config_General
	{
		return $this->getGeneralConfig();
	}
	
	public function handleSysServices(): void
	{
		Logger::setLogger( new Logger_SysServices() );
		Auth::setController( new class implements Auth_Controller_Interface {
			
			public function handleLogin(): void {
			}
			
			public function login( string $username, string $password ): bool
			{
				return false;
			}
			
			public function loginUser( Auth_User_Interface $user ): bool
			{
				return false;
			}
			
			public function logout(): void
			{
			}
			
			public function checkCurrentUser(): bool
			{
				return false;
			}
			
			public function getCurrentUser(): Auth_User_Interface|false
			{
				return false;
			}
			
			public function getCurrentUserHasPrivilege( string $privilege, mixed $value = null ): bool
			{
				return false;
			}
			
			public function checkModuleActionAccess( string $module_name, string $action ): bool
			{
				return false;
			}
			
			public function checkPageAccess( MVC_Page_Interface $page ): bool
			{
				return false;
			}
		});
		
		
		if( $eshop_key = Http_Request::GET()->getString('eshop', valid_values: array_keys(EShops::getScope())) ) {
			EShops::setCurrent( EShops::get( $eshop_key ) );
		} else {
			EShops::setCurrent( EShops::getDefault() );
		}
		
		Locale::setCurrentLocale( EShops::getCurrent()->getLocale() );
		
		$URL_path = explode('/', MVC::getRouter()->getUrlPath());
		
		
		if(count($URL_path)<2) {
			ErrorPages::handleNotFound( true );
		}
		
		[$key, $service_code] = $URL_path;
		
		if($key!=$this->getConfig()->getKey()) {
			ErrorPages::handleNotFound( true );
		}
		
		$service = SysServices::getService( $service_code );
		if( !$service ) {
			ErrorPages::handleNotFound( true );
		}
		
		unset($URL_path[0]);
		unset($URL_path[1]);
		
		$_SERVER['REQUEST_URI'] = '/'.implode('/', $URL_path);
		
		Debug::setOutputIsJSON( true );
		
		
		if(!$service->isActive()) {
			Http_Headers::response(
				code: Http_Headers::CODE_503_SERVICE_UNAVAILABLE,
				headers: [
					'Content-Type' => 'text/plain; charset=UTF-8'
				]
			);
			
			echo 'Service deactivated';
		} else {
			Http_Headers::response(
				code: Http_Headers::CODE_200_OK,
				headers: [
					'Content-Type' => 'text/plain; charset=UTF-8'
				]
			);
			
			try {
				set_time_limit(-1);
				$service->perform();
				echo "\n\nDONE\n\n";
			} catch( Error $e) {
				echo 'Error: '.$e->getMessage();
				
				Logger::danger(
					event: 'system_service_fault',
					event_message: 'Problem during system service '.$service->getName(),
					context_object_id: $service->getCode(),
					context_object_data: [
						'URL' => Http_Request::currentURL(),
						'error_message' => $e->getMessage()
					]
				);
			}
		}
		
		
		Application::end();
	}
	
	public function getSysServiceURL( SysServices_Definition $service, ?EShop $eshop=null ) : string
	{
		$key = $this->getConfig()->getKey();
		$GET_params = [];
		if($eshop) {
			$GET_params['eshop'] = $eshop->getKey();
		}
		
		$base =  MVC::getBase( $this->getConfig()->getBaseId() );
		
		return $base->getHomepage( $base->getDefaultLocale() )->getURL(
			path_fragments: [
				$key,
				$service->getCode()
			],
			GET_params: $GET_params
		);
	}
}