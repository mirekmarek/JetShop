<?php
/**
 * @copyright Copyright (c) Miroslav Marek <mirek.marek@web-jet.cz>
 * @license EUPL 1.2  https://eupl.eu/1.2/en/
 * @author Miroslav Marek <mirek.marek@web-jet.cz>
 */
namespace JetShop;


use Jet\Application_Modules;
use Jet\IO_Dir;
use Jet\MVC;
use Jet\SysConf_Path;
use Jet\SysConf_URI;
use JetApplication\EShop_ModuleUsingTemplate_Interface;

abstract class Core_EShop_Template
{
	
	protected static ?string $root_dir = null;
	
	protected static ?string $root_url = null;
	
	
	protected string $relative_dir;
	
	
	public static function getRootDir(): ?string
	{
		if( static::$root_dir === null ) {
			static::$root_dir = SysConf_Path::getBase() . 'templates/';
		}
		
		return static::$root_dir;
	}
	
	public static function setRootDir( ?string $root_dir ): void
	{
		static::$root_dir = $root_dir;
	}
	

	public static function getRootUrl(): ?string
	{
		if( static::$root_url === null ) {
			static::$root_url = '//'. $_SERVER['HTTP_HOST'].SysConf_URI::getBase() . 'templates/';
		}
		
		return static::$root_url;
	}
	

	public static function setRootUrl( ?string $root_url ): void
	{
		static::$root_url = $root_url;
	}
	
	
	
	
	public function __construct( string $relative_dir )
	{
		$this->relative_dir = $relative_dir;
	}
	
	public function getDir(): string
	{
		return static::getRootDir() . $this->relative_dir . '/';
	}
	
	public function getViewsDir(): string
	{
		return $this->getDir().'views/';
	}
	
	public function getUIViewsDir() : string
	{
		return $this->getViewsDir().'ui/';
	}
	
	public function getFormViewsDir() : string
	{
		return $this->getViewsDir().'form/';
	}
	
	public function getLayoutsDir(): string
	{
		return $this->getDir().'layouts/';
	}
	
	public function getErrorPagesDir(): string
	{
		return $this->getDir().'layouts/';
	}
	
	public function getImagesDir(): string
	{
		return $this->getDir().'images/';
	}
	
	public function getCssDir(): string
	{
		return $this->getDir().'css/';
	}
	
	public function getJsDir(): string
	{
		return $this->getDir().'js/';
	}
	
	public function getUrl() : string
	{
		return static::getRootUrl().$this->relative_dir.'/';
	}

	
	public function getImagesUrl(): string
	{
		return $this->getUrl().'images/';
	}
	
	public function getImagesPath(): string
	{
		return $this->getDir().'images/';
	}
	
	
	public function getCssUrl(): string
	{
		return $this->getUrl().'css/';
	}
	
	public function getJsUrl(): string
	{
		return $this->getUrl().'js/';
	}
	
	public function createFromDevelopmentScripts() : void
	{
		$base = MVC::getBase( 'eshop' );
		
		IO_Dir::copy( $base->getViewsPath(), $this->getViewsDir(), overwrite_if_exists: true );
		IO_Dir::copy( $base->getLayoutsPath(), $this->getLayoutsDir(), overwrite_if_exists: true );
		IO_Dir::copy( SysConf_Path::getCss().'eshop/', $this->getCssDir(), overwrite_if_exists: true );
		IO_Dir::copy( SysConf_Path::getJs().'eshop/', $this->getJsDir(), overwrite_if_exists: true );
		
		foreach( Application_Modules::activatedModulesList() as $manifest ) {
			$m = Application_Modules::moduleInstance( $manifest->getName() );
			if($m instanceof EShop_ModuleUsingTemplate_Interface) {
				$m->copyModuleDefaultViewsToTemplateDir( $this );
			}
		}
	}
	
}