<?php
/**
 * @copyright Copyright (c) Miroslav Marek <mirek.marek@web-jet.cz>
 * @license EUPL 1.2  https://eupl.eu/1.2/en/
 * @author Miroslav Marek <mirek.marek@web-jet.cz>
 */
namespace JetShop;



use Jet\Application_Module;
use Jet\Attributes;
use Jet\IO_Dir;
use Jet\SysConf_Path;
use JetApplication\Managers;
use ReflectionClass;
use JetApplication\Manager_MetaInfo;

abstract class Core_Manager_MetaInfo {
	
	public const GROUP_GENERAL = 'general';
	public const GROUP_ADMIN = 'admin';
	public const GROUP_ESHOP = 'eshop';
	
	
	protected string $interface_class_name;
	protected string $group = '';
	protected string $module_name_prefix;
	protected bool $is_mandatory;
	protected string $name;
	protected string $description;
	
	protected static ?array $definitions = null;
	
	
	public function __construct( ...$definitions )
	{
	
	}

	
	public function getGroup(): string
	{
		return $this->group;
	}
	
	public function setGroup( string $group ): void
	{
		$this->group = $group;
	}
	
	public function setInterfaceClassName( string $interface_class_name ): void
	{
		$this->interface_class_name = $interface_class_name;
	}
	
	public function getInterfaceClassName(): string
	{
		return $this->interface_class_name;
	}
	
	
	public function getModuleNamePrefix(): string
	{
		return $this->module_name_prefix;
	}
	
	public function setModuleNamePrefix( string $module_name_prefix ): void
	{
		$this->module_name_prefix = $module_name_prefix;
	}
	
	public function setIsMandatory( bool $is_mandatory ): void
	{
		$this->is_mandatory = $is_mandatory;
	}
	
	public function isMandatory(): bool
	{
		return $this->is_mandatory;
	}
	
	public function setName( string $name ): void
	{
		$this->name = $name;
	}
	
	public function getName(): string
	{
		return $this->name;
	}
	
	public function setDescription( string $description ): void
	{
		$this->description = $description;
	}
	
	public function getDescription(): string
	{
		return $this->description;
	}
	
	/**
	 * @return Application_Module[]
	 */
	public function getPossibleModulesScope() : array
	{
		$scope = [];
		if(!$this->is_mandatory) {
			$scope[''] = '';
		}
		
		$managers = Managers::findManagers( $this->interface_class_name, $this->module_name_prefix );
		
		foreach($managers as $manager) {
			$scope[$manager->getModuleManifest()->getName()] = $manager;
		}
		
		return $scope;
	}
	
	public static function create( ReflectionClass $class, array $attributes ) : static
	{
		$definition = new static();
		$definition->setInterfaceClassName( $class->getName() );
		
		$definition->setGroup( $attributes['group'] );
		$definition->setIsMandatory( (bool)$attributes['is_mandatory'] );
		$definition->setName( $attributes['name'] );
		$definition->setDescription( $attributes['description'] );
		$definition->setModuleNamePrefix( $attributes['module_name_prefix'] );
		
		return $definition;
	}
	
	/**
	 * @param ?string $group
	 * @return static[]
	 */
	public static function getManagers( ?string $group='' ) : array
	{
		if(static::$definitions===null) {
			$finder = new class {
				/**
				 * @var Manager_MetaInfo[]
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
							$reflection->isInterface() ||
							$reflection->isAbstract()
						) {
							$attributes = Attributes::getClassDefinition(
								$reflection,
								Manager_MetaInfo::class
							);
							
							if($attributes) {
								$this->classes[$class] = Manager_MetaInfo::create( $reflection, $attributes );
							}
						}
					}
					
					foreach( $dirs as $path => $name ) {
						$this->readDir( $path );
					}
				}
				
				/**
				 * @return Manager_MetaInfo[]
				 */
				public function getClasses(): array
				{
					return $this->classes;
				}
			};
			
			static::$definitions = $finder->getClasses();
		}
		
		if(!$group) {
			return static::$definitions;
		}
		
		$res = [];
		
		foreach(static::$definitions as $ifc_class=>$def) {
			if($def->getGroup() === $group) {
				$res[$ifc_class] = $def;
			}
		}
		
		return $res;
	}
	
}