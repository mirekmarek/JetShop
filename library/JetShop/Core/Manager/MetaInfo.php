<?php
namespace JetShop;


use Jet\Application_Module;
use JetApplication\Managers;

abstract class Core_Manager_MetaInfo {
	
	protected string $interface_class_name;
	protected string $module_name_prefix;
	protected bool $is_mandatory;
	protected string $name;
	protected string $description;
	
	public function __construct( string $interface_class_name, bool $is_mandatory, string $name, string $description, string $module_name_prefix )
	{
		$this->interface_class_name = $interface_class_name;
		$this->module_name_prefix = $module_name_prefix;
		$this->is_mandatory = $is_mandatory;
		$this->name = $name;
		$this->description = $description;
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
	
	public function isMandatory(): bool
	{
		return $this->is_mandatory;
	}
	
	public function getName(): string
	{
		return $this->name;
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
	
	
}