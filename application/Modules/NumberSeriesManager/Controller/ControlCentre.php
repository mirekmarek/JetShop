<?php
/**
 * @copyright Copyright (c) Miroslav Marek <mirek.marek@web-jet.cz>
 * @license EUPL 1.2  https://eupl.eu/1.2/en/
 * @author Miroslav Marek <mirek.marek@web-jet.cz>
 */
namespace JetApplicationModule\NumberSeriesManager;


use Jet\Http_Headers;
use Jet\IO_Dir;
use Jet\Logger;
use Jet\SysConf_Path;
use Jet\Tr;
use Jet\UI_messages;
use JetApplication\Admin_ControlCentre_Module_Controller;
use JetApplication\EShops;
use Error;
use JetApplication\EShopEntity_HasNumberSeries_Interface;
use ReflectionClass;


class Controller_ControlCentre extends Admin_ControlCentre_Module_Controller
{
	
	public function default_Action() : void
	{
		
		$finder = new class {
			/**
			 * @var ReflectionClass[]
			 */
			protected array $classes = [];
			protected string $dir = '';
			
			public function __construct()
			{
				$this->dir = SysConf_Path::getApplication() . 'Classes/';
				$this->find();
			}
			
			
			public function find(): void
			{
				$this->readDir( $this->dir );
				
				ksort( $this->classes );
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
						!$reflection->isAbstract() &&
						$reflection->implementsInterface( EShopEntity_HasNumberSeries_Interface::class )
					) {
						$this->classes[$class] = $reflection;
					}
				}
				
				foreach( $dirs as $path => $name ) {
					$this->readDir( $path );
				}
			}
			
			/**
			 * @return ReflectionClass[]
			 */
			public function getClasses(): array
			{
				return $this->classes;
			}
		};
		
		
		$entities = [];
		
		foreach( $finder->getClasses() as $class_name=>$reflection ) {
			/**
			 * @var EShopEntity_HasNumberSeries_Interface $class_name
			 */
			
			$entities[] = [
				'entity'     => $class_name::getNumberSeriesEntityType(),
				'per_eshop'  => $class_name::getNumberSeriesEntityIsPerShop(),
				'title'      => Tr::_( $class_name::getNumberSeriesEntityTitle() )
			];
		}
		
		uasort( $entities, function( array $a, array $b ) {
			return strcasecmp( $a['title'], $b['title'] );
		} );
		
		
		
		$forms = [];
		
		$save = function( EntityConfig $config, $c_id ) {
			$ok = true;
			
			try {
				$config->saveConfigFile();
			} catch(Error $e) {
				$ok = false;
				UI_messages::danger( Tr::_('Error during configuration saving: ').$e->getMessage(), context: 'CC' );
			}
			
			if($ok) {
				Logger::info(
					event: 'number_series_config_updated',
					event_message: 'Number series configuration updated',
					context_object_id: $c_id,
					context_object_data: $config
				);
				UI_messages::success( Tr::_('Configuration has been saved'), context: 'CC' );
			}
			
			Http_Headers::reload();
		};
		
		foreach( $entities as $entity ) {
			$e = $entity['entity'];
			
			if($entity['per_eshop']) {
				foreach( EShops::getList() as $eshop ) {
					$config = new EntityConfig( $e, $eshop );
					
					$forms[$e.'_'.$eshop->getKey()] = $config->createForm( $e.'_'.$eshop->getKey() );
					
					if($forms[$e.'_'.$eshop->getKey()]->catch()) {
						$save( $config, $e.'_'.$eshop->getKey() );
					}
				}
				
				continue;
			}
			
			$config = new EntityConfig( $e );
			
			$forms[$e] = $config->createForm( $e );
			
			if($forms[$e]->catch()) {
				$save( $config, $e );
			}
			
		}
		
		$this->view->setVar('entities', $entities);
		$this->view->setVar('forms', $forms);
		
		$this->output('control-centre/default');

	}
}