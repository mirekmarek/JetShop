<?php
/**
 * @copyright Copyright (c) Miroslav Marek <mirek.marek@web-jet.cz>
 * @license EUPL 1.2  https://eupl.eu/1.2/en/
 * @author Miroslav Marek <mirek.marek@web-jet.cz>
 */
namespace JetApplicaTionModule\Exports\Manager;


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
use JetApplication\EShopConfig_ModuleConfig_ModuleHasConfig_General_Interface;
use JetApplication\EShopConfig_ModuleConfig_ModuleHasConfig_General_Trait;
use JetApplication\EShops;
use JetApplication\EShop;
use JetApplication\EShopConfig_ModuleConfig_General;
use JetApplication\Logger_Exports;


class Main extends Exports_Manager implements
	Admin_ControlCentre_Module_Interface,
	EShopConfig_ModuleConfig_ModuleHasConfig_General_Interface
{
	use Admin_ControlCentre_Module_Trait;
	use EShopConfig_ModuleConfig_ModuleHasConfig_General_Trait;
	
	
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
	
	protected function getConfig() : EShopConfig_ModuleConfig_General|Config_General
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
		
		
		
		if( $eshop_key = Http_Request::GET()->getString('eshop', valid_values: array_keys(EShops::getScope())) ) {
			EShops::setCurrent( EShops::get( $eshop_key ) );
		} else {
			EShops::setCurrent( EShops::getDefault() );
		}
		Locale::setCurrentLocale( EShops::getCurrent()->getLocale() );
		
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
	
	public function getExportURL( Exports_Definition $export, ?EShop $eshop=null ) : string
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
				$export->getCode()
			],
			GET_params: $GET_params
		);
	}
}