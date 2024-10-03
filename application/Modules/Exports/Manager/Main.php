<?php
/**
 *
 * @copyright 
 * @license  
 * @author  
 */
namespace JetApplicationModule\Exports\Manager;

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
use JetApplication\Admin_ControlCentre;
use JetApplication\Admin_ControlCentre_Module_Interface;
use JetApplication\Admin_ControlCentre_Module_Trait;
use JetApplication\Exports;
use JetApplication\Exports_Definition;
use JetApplication\Exports_Manager;
use JetApplication\ShopConfig_ModuleConfig_ModuleHasConfig_General_Interface;
use JetApplication\ShopConfig_ModuleConfig_ModuleHasConfig_General_Trait;
use JetApplication\Shops;
use JetApplication\Shops_Shop;
use JetApplication\ShopConfig_ModuleConfig_General;
use JetApplication\Logger_Exports;

/**
 *
 */
class Main extends Exports_Manager implements
	Admin_ControlCentre_Module_Interface,
	ShopConfig_ModuleConfig_ModuleHasConfig_General_Interface
{
	use Admin_ControlCentre_Module_Trait;
	use ShopConfig_ModuleConfig_ModuleHasConfig_General_Trait;
	
	
	public function getControlCentreGroup(): string
	{
		return Admin_ControlCentre::GROUP_MAIN;
	}
	
	public function getControlCentreTitle(): string
	{
		return 'Exports';
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
	
	protected function getConfig() : ShopConfig_ModuleConfig_General|Config_General
	{
		return $this->getGeneralConfig();
	}
	
	public function handleExports(): void
	{
		Logger::setLogger( new Logger_Exports() );
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
			
			public function getCurrentUser(): Auth_User_Interface|bool
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
		
		
		
		if( $shop_key = Http_Request::GET()->getString('shop', valid_values: array_keys(Shops::getScope())) ) {
			Shops::setCurrent( Shops::get( $shop_key ) );
		} else {
			Shops::setCurrent( Shops::getDefault() );
		}
		Locale::setCurrentLocale( Shops::getCurrent()->getLocale() );
		
		$URL_path = explode('/', MVC::getRouter()->getUrlPath());
		
		if(count($URL_path)!=2) {
			ErrorPages::handleNotFound( true );
		}
		
		[$key, $export_code] = $URL_path;
		
		if($key!=$this->getConfig()->getKey()) {
			ErrorPages::handleNotFound( true );
		}
		
		$export = Exports::getExport( $export_code );
		
		if( !$export ) {
			ErrorPages::handleNotFound( true );
		}
		
		Debug::setOutputIsJSON( true );
		
		
		if(!$export->isActive()) {
			Http_Headers::response(
				code: Http_Headers::CODE_503_SERVICE_UNAVAILABLE,
				headers: [
					'Content-Type' => 'text/plain; charset=UTF-8'
				]
			);
			
			echo 'Export deactivated';
		} else {
			try {
				$export->perform();
			} catch( Error $e) {
				Logger::danger(
					event: 'export_fault',
					event_message: 'Problem during export '.$export->getName(),
					context_object_id: $export->getCode(),
					context_object_data: [
						'URL' => Http_Request::currentURL(),
						'error_message' => $e->getMessage()
					]
 				);
			}
		}
		
		
		Application::end();
	}
	
	public function getExportURL( Exports_Definition $export, ?Shops_Shop $shop=null ) : string
	{
		$key = $this->getConfig()->getKey();
		$GET_params = [];
		if($shop) {
			$GET_params['shop'] = $shop->getKey();
		}
		
		$base =  MVC::getBase( $this->getConfig()->getBaseId() );
		
		return $base->getHomepage( $base->getDefaultLocale() )->getURL(
			path_fragments: [
				$key,
				$export->getCode()
			],
			GET_params: $GET_params
		);
	}
}