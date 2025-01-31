<?php
/**
 *
 * @copyright Copyright (c) Miroslav Marek <mirek.marek@web-jet.cz>
 * @license http://www.php-jet.net/license/license.txt
 * @author Miroslav Marek <mirek.marek@web-jet.cz>
 */

namespace JetApplication\Installer;

use Error;
use Exception;
use Jet\AJAX;
use Jet\Application_Modules;
use Jet\DataModel;
use Jet\DataModel_Helper;
use Jet\Http_Request;
use Jet\IO_Dir;
use Jet\MVC;
use Jet\SysConf_Path;
use Jet\Tr;
use Jet\Translator;
use Jet\UI_messages;
use JetApplication\Availabilities;
use JetApplication\Availability;
use JetApplication\Currencies;
use JetApplication\EShop;
use JetApplication\EShop_Pages;
use JetApplication\EShop_Template;
use JetApplication\EShops;
use JetApplication\Pricelist;
use JetApplication\Pricelists;
use JetApplication\WarehouseManagement_Warehouse;
use ReflectionClass;


/**
 *
 */
class Installer_Step_Install_Controller extends Installer_Step_Controller
{
	protected string $icon = 'gears';
	
	protected string $label = 'Install';
	
	protected string $error_message = '';
	
	public function main(): void
	{
		$this->catchContinue();
		
		$steps = [
			'createDb' => Tr::_('Create database'),
			'modules'  => Tr::_('Install modules'),
			'bases'    => Tr::_('Create bases'),
			'config'   => Tr::_('Saving configuration'),
			'createTemplate'   => Tr::_('Creating default template'),
			'pages'    => Tr::_('Create pages'),
			'sampleContent' => Tr::_('Installing example content'),
		];
		
		$installation_step = Http_Request::GET()->getString('is', default_value: '', valid_values: array_keys( $steps ));
		
		if($installation_step) {
			$method = 'install_'.$installation_step;
			
			$res = $this->{$method}();
			if($res) {
				AJAX::commonResponse([
					'ok' => true
				]);
			} else {
				AJAX::commonResponse([
					'ok' => false,
					'error' => $this->error_message
				]);
				
			}
		}
		
		$this->view->setVar('steps', $steps);
		
		
		$this->render('default');
	}
	
	public function install_createDb() : bool
	{
		$classes = [];
		//TODO: class finder
		
		$finder = new class {
			/**
			 * @var array
			 */
			protected array $classes = [];
			protected string $dir = '';
			
			public function __construct()
			{
				$this->dir = SysConf_Path::getApplication() . 'Classes/';
				$this->readDir( $this->dir );
				
				asort( $this->classes );
			}
			
			protected function readDir( string $dir ): void
			{
				$dirs = IO_Dir::getList( $dir, '*', true, false );
				$files = IO_Dir::getList( $dir, '*.php', false, true );
				
				foreach( $files as $path => $name ) {
					$class = str_replace($this->dir, '', $path);
					$class = str_replace('.php', '', $class);
					
					$class = str_replace('/', '_', $class);
					$class = str_replace('\\', '_', $class);
					
					$class = '\\JetApplication\\'.$class;
					
					$reflection = new ReflectionClass( $class );
					
					if(
						$reflection->isSubclassOf( DataModel::class ) &&
						!$reflection->isAbstract()
					) {
						$this->classes[] = $reflection->getName();
					}
				}
				
				foreach( $dirs as $path => $name ) {
					$this->readDir( $path );
				}
			}
			
			/**
			 * @return array
			 */
			public function getClasses(): array
			{
				return $this->classes;
			}
		};
		
		$classes = $finder->getClasses();
		
		$result = [];
		$OK = true;
		
		foreach( $classes as $class ) {
			$result[$class] = true;
			try {
				DataModel_Helper::create( $class );
			} catch( Error|Exception $e ) {
				$result[$class] = $e->getMessage();
				$OK = false;
			}
			
		}
		
		
		if(!$OK) {
			$this->view->setVar( 'result', $result );
			$this->view->setVar( 'OK', false );
			
			$this->error_message = $this->view->render( 'create-db' );
		}
		
		return $OK;
	}
	
	public function install_modules() : bool
	{
		$all_modules = Application_Modules::allModulesList();
		$modules_scope = [];
		$selected_modules = [];
		foreach($all_modules as $module) {
			$modules_scope[$module->getName()] = $module->getLabel();
			$selected_modules[] = $module->getName();
		}
		
		
		$this->view->setVar( 'modules', $all_modules );
		
		
		$this->catchContinue();
		
		
		
		
		$result = [];
		
		$OK = true;
		
		$tr_dir = SysConf_Path::getDictionaries();
		SysConf_Path::setDictionaries(__APP_DICTIONARIES__);
		
		foreach( $selected_modules as $module_name ) {
			$result[$module_name] = true;
			
			if( $all_modules[$module_name]->isActivated() ) {
				continue;
			}
			
			try {
				Application_Modules::installModule( $module_name );
			} catch( Error|Exception $e ) {
				$result[$module_name] = $e->getMessage();
				
				$OK = false;
			}
			
			if( $result[$module_name] !== true ) {
				continue;
			}
			
			try {
				Application_Modules::activateModule( $module_name );
			} catch( Error|Exception $e ) {
				$result[$module_name] = $e->getMessage();
				$OK = false;
			}
			
		}
		
		SysConf_Path::setDictionaries( $tr_dir );
		

		if(!$OK) {
			$this->view->setVar( 'result', $result );
			$this->view->setVar( 'OK', false );
			
			$this->error_message = $this->view->render( 'modules-installation-result' );
		}
		
		return true;
		
		
	}
	
	public function install_bases() : bool
	{
		$bases = Installer::getBases();
		
		try {
			foreach( $bases as $base ) {
				$base->saveDataFile();
			}
			
		} catch( Error|Exception $e ) {
			$this->error_message = UI_messages::createDanger(
				Tr::_( 'Something went wrong: %error%', ['error' => $e->getMessage()], Translator::COMMON_DICTIONARY )
			);
			
			return false;
		}
		
		return true;
	}
	
	public function install_pages() : bool
	{
		foreach(Installer::getSelectedEshopLocales() as $locale) {
			EShop_Pages::createPages( EShops::get( EShop::generateKey('default', $locale) ) );
		}
		
		return true;
	}
	
	
	public function install_config() : bool
	{
		try {
			$this->install_config_pricelists();
			$this->install_config_availabilities();
			$this->install_config_eshops();
			
		} catch( Error|Exception $e ) {
			$this->error_message = UI_messages::createDanger(
				Tr::_( 'Something went wrong: %error%', ['error' => $e->getMessage()], Translator::COMMON_DICTIONARY )
			);
			
			return false;
		}
		
		
		return true;
	}
	
	public function install_config_pricelists() : void
	{
		foreach(Installer::getSelectedEshopLocales() as $locale) {
			$code = 'default_'.$locale->toString();
			if(!Pricelists::exists( $code )) {
				
				$currency_code = Installer::getCurrencies()[$locale->toString()];
				$currency = Currencies::get( $currency_code );
				$vat_rates = Installer::getVATRates( $locale );
				
				$pl = new Pricelist();
				$pl->setCode( $code );
				$pl->setName( $locale->getRegionName().' - '.$currency->getCode() );
				$pl->setCurrencyCode( $currency->getCode() );
				$pl->setVatRates( $vat_rates );
				$pl->setDefaultVatRate( $vat_rates[0] );
				
				Pricelists::addPricelist( $pl );
			}
		}
		
		Pricelists::saveCfg();
	}
	
	public function install_config_availabilities() : void
	{
		switch(Installer::getAvailabilityStrategy()) {
			case 'global':
				$wh_code = 'default';
				if(!($wh=WarehouseManagement_Warehouse::getByInternalCode($wh_code))) {
					$wh = new WarehouseManagement_Warehouse();
					$wh->setInternalName( $wh_code );
					$wh->setInternalCode( $wh_code );
					$wh->save();
				}
				
				$code = 'default';
				if(!Availabilities::exists($code)) {
					$avl = new Availability();
					$avl->setCode( $code );
					$avl->setName( 'default' );
					$avl->setWarehouseIds( [$wh->getId()] );
					
					Availabilities::addAvailability( $avl );
				}
				break;
			case 'by_locale':
				
				foreach(Installer::getSelectedEshopLocales() as $locale) {
					$wh_code = 'default_'.$locale;
					if(!($wh=WarehouseManagement_Warehouse::getByInternalCode($wh_code))) {
						$wh = new WarehouseManagement_Warehouse();
						$wh->setInternalName( $locale->getRegionName() );
						$wh->setInternalCode( $wh_code );
						$wh->save();
					}
					
					$code = 'default_'.$locale;
					if(!Availabilities::exists($code)) {
						$avl = new Availability();
						$avl->setCode( $code );
						$avl->setName( $locale->getRegionName() );
						$avl->setWarehouseIds( [$wh->getId()] );
						
						Availabilities::addAvailability( $avl );
					}
					
				}
				
				break;
		}
		
		Availabilities::saveCfg();
	}
	
	public function install_config_eshops() : void
	{
		$code = 'default';
		
		$base = MVC::getBase('eshop');
		
		$has_default = false;
		foreach(Installer::getSelectedEshopLocales() as $locale) {
			
			$key = EShop::generateKey( $code, $locale );
			if(!EShops::exists( $key )) {
				$eshop = new EShop();
				$eshop->setCode( $code );
				$eshop->setLocale( $locale );
				$eshop->setBaseId( $base->getId() );
				$eshop->setName( $locale->getRegionName() );
			} else {
				$eshop = EShops::get( $key );
			}
			
			$eshop->setIsActive( true );
			$eshop->setURLs( $base->getLocalizedData( $locale )->getURLs() );
			
			if(!$has_default) {
				$eshop->setIsDefault( true );
				$has_default = true;
			} else {
				$eshop->setIsDefault( false );
			}
			
			$pricelist_code = 'default_'.$locale->toString();
			$eshop->setPricelistCodes([$pricelist_code]);
			$eshop->setDefaultPricelistCode( $pricelist_code );
			
			
			$wh_code = 'default';
			$avl_code = 'default';
			
			if(Installer::getAvailabilityStrategy()=='by_locale') {
				$wh_code = 'default_'.$locale;
				$avl_code = 'default_'.$locale;
			}
			
			$wh=WarehouseManagement_Warehouse::getByInternalCode($wh_code);
			$eshop->setAvailabilityCodes( [$avl_code] );
			$eshop->setDefaultAvailabilityCode( $avl_code );
			$eshop->setDefaultWarehouseId( $wh->getId() );
			
			$eshop->setUseTemplate( true );
			$eshop->setTemplateRelativeDir( 'default' );
			
			
			$eshop->save();
			
			
		}
	}
	
	public function install_createTemplate() : bool
	{
		$template = new EShop_Template('default');
		
		$template->createFromDevelopmentScripts();
		
		return true;
	}
	
	public function install_sampleContent() : bool
	{
		//TODO:
		return true;
	}
}