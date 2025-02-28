<?php

/**
 * @copyright Copyright (c) Miroslav Marek <mirek.marek@web-jet.cz>
 * @license EUPL 1.2  https://eupl.eu/1.2/en/
 * @author Miroslav Marek <mirek.marek@web-jet.cz>
 */

namespace JetShop;

use Jet\Attributes;
use Jet\DataModel_Related;
use Jet\IO_Dir;
use Jet\SysConf_Path;
use JetApplication\EShopEntity_Basic;
use JetApplication\EShopEntity_CanNotBeDeletedReason;
use JetApplication\EShopEntity_Definition;
use ReflectionClass;

abstract class Core_EShopEntity {
	
	protected static ?array $known_entities = null;
	
	/**
	 * @return EShopEntity_Definition[]
	 */
	public static function findEntities() : array
	{
		
		if(static::$known_entities === null) {
			$finder = new class {
				/**
				 * @var EShopEntity_Definition[]
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
						
						if($reflection->isSubclassOf(DataModel_Related::class)) {
							continue;
						}
						
						$attributes = Attributes::getClassDefinition(
							$reflection,
							EShopEntity_Definition::class,
						);
						
						if($attributes) {
							$this->classes[$class] = EShopEntity_Definition::get( $class );
						}
						
					}
					
					foreach( $dirs as $path => $name ) {
						$this->readDir( $path );
					}
				}
				
				/**
				 * @return EShopEntity_Definition[]
				 */
				public function getClasses(): array
				{
					return $this->classes;
				}
			};
			
			static::$known_entities = $finder->getClasses();
		}
		
		return static::$known_entities;
	}
	
	public static function getEntityDefinitionByType( string $type ): ?EShopEntity_Definition
	{
		foreach(static::findEntities() as $definition) {
			if($definition->getEntityType()==$type) {
				return $definition;
			}
		}
		
		return null;
	}
	
	
	/**
	 * @param EShopEntity_Basic $entity_to_be_deleted
	 * @param EShopEntity_CanNotBeDeletedReason[] &$reasons
	 * @return bool
	 */
	public static function checkIfItCanBeDeleted( EShopEntity_Basic $entity_to_be_deleted, array &$reasons=[] ) : bool
	{
		$res = true;
		
		foreach( static::findEntities() as $class_name=>$definition ) {
			/**
			 * @var EShopEntity_Basic $class_name
			 */
			if( !$class_name::checkIfItCanBeDeleted( $entity_to_be_deleted, $reasons ) ) {
				$res = false;
			}
		}
		
		return $res;
	}
	
}