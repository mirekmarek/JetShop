<?php
/**
 * @copyright Copyright (c) Miroslav Marek <mirek.marek@web-jet.cz>
 * @license EUPL 1.2  https://eupl.eu/1.2/en/
 * @author Miroslav Marek <mirek.marek@web-jet.cz>
 */
namespace JetApplicationModule\Exports\Manager;

use Error;
use Jet\Application;
use Jet\Debug;
use Jet\ErrorPages;
use Jet\Http_Headers;
use Jet\Http_Request;
use Jet\Locale;
use Jet\Logger;
use Jet\MVC;
use Jet\SysConf_Jet_Debug;
use JetApplication\Admin_ControlCentre;
use JetApplication\Admin_ControlCentre_Module_Interface;
use JetApplication\Admin_ControlCentre_Module_Trait;
use JetApplication\Exports;
use JetApplication\Exports_Definition;
use JetApplication\Application_Service_General_ExportsManager;
use JetApplication\EShopConfig_ModuleConfig_ModuleHasConfig_General_Interface;
use JetApplication\EShopConfig_ModuleConfig_ModuleHasConfig_General_Trait;
use JetApplication\EShops;
use JetApplication\EShop;
use JetApplication\EShopConfig_ModuleConfig_General;
use JetApplication\Logger_Exports;


class Main extends Application_Service_General_ExportsManager implements
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
		
		$valid_key = $this->generateKey( $export_code );
		
		if($key!=$valid_key) {
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
				
				if(SysConf_Jet_Debug::getDevelMode()) {
					throw $e;
				} else {
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
		}
		
		
		Application::end();
	}
	
	public function getExportURL( Exports_Definition $export, ?EShop $eshop=null ) : string
	{
		$GET_params = [];
		if($eshop) {
			$GET_params['eshop'] = $eshop->getKey();
		}
		
		$base =  MVC::getBase( $this->getConfig()->getBaseId() );
		
		return $base->getHomepage( $base->getDefaultLocale() )->getURL(
			path_fragments: [
				$this->generateKey( $export->getCode() ),
				$export->getCode()
			],
			GET_params: $GET_params
		);
	}
	
	protected function generateKey( string $eport_code ) : string
	{
		return sha1( $this->getConfig()->getKey(). $eport_code );
	}
}