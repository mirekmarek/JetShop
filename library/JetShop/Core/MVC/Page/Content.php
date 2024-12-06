<?php
namespace JetShop;

use Jet\MVC_Page_Content;
use JetApplication\Admin_Managers;
use JetApplication\EShop_Managers;
use JetApplication\Managers_General;

abstract class Core_MVC_Page_Content extends MVC_Page_Content {
	
	protected string $manager_group = '';
	
	protected string $manager_interface = '';
	
	public function getManagerGroup(): string
	{
		return $this->manager_group;
	}
	
	public function setManagerGroup( string $manager_group ): void
	{
		$this->manager_group = $manager_group;
	}
	
	public function getManagerInterface(): string
	{
		return $this->manager_interface;
	}
	
	public function setManagerInterface( string $manager_interface ): void
	{
		$this->manager_interface = $manager_interface;
	}
	
	public function getModuleName() : string
	{

		if(!$this->manager_group || !$this->manager_interface) {
			return parent::getModuleName();
		}
		
		/**
		 * @var EShop_Managers|Managers_General|Admin_Managers $m
		 */
		$m = $this->manager_group;
		$manager = $m::get( $this->manager_interface );
		if( !$manager ) {
			return '';
		}
		
		return $manager->getModuleManifest()->getName();
	}
	
	
}