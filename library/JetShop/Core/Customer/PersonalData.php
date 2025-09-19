<?php
/**
 * @copyright Copyright (c) Miroslav Marek <mirek.marek@web-jet.cz>
 * @license EUPL 1.2  https://eupl.eu/1.2/en/
 * @author Miroslav Marek <mirek.marek@web-jet.cz>
 */
namespace JetShop;

use ReflectionClass;
use Jet\IO_Dir;
use Jet\SysConf_Path;
use JetApplication\EShopEntity_HasPersonalData_Interface;

abstract class Core_Customer_PersonalData {
	
	/**
	 * @var array<EShopEntity_HasPersonalData_Interface>
	 */
	protected static ?array $entities = null;
	
	public static function findEntities() : void
	{
		$finder = new class {
			/**
			 * @var array<EShopEntity_HasPersonalData_Interface>
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
					if($reflection->isInterface()) {
						continue;
					}
					
					if($reflection->implementsInterface(EShopEntity_HasPersonalData_Interface::class)) {
						$this->classes[$class] = $class;
					}
				}
				
				foreach( $dirs as $path => $name ) {
					$this->readDir( $path );
				}
			}
			
			public function getClasses(): array
			{
				return $this->classes;
			}
		};
		
		static::$entities = $finder->getClasses();
		
	}
	
	
	/**
	 * @return array<EShopEntity_HasPersonalData_Interface>
	 */
	public static function getEntities() : array
	{
		if(static::$entities===null) {
			static::findEntities();
		}
		
		return static::$entities;
	}
	
	public static function delete( int $customer_id ) : void
	{
		foreach(static::getEntities() as $entity) {
			$entity::findAndDeletePersonalData( $customer_id );
		}
		
	}
}