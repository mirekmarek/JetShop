<?php
/**
 * @copyright Copyright (c) Miroslav Marek <mirek.marek@web-jet.cz>
 * @license EUPL 1.2  https://eupl.eu/1.2/en/
 * @author Miroslav Marek <mirek.marek@web-jet.cz>
 */
namespace JetShop;

use Jet\Autoloader;
use Jet\Factory_MVC;
use Jet\IO_Dir;
use Jet\MVC_View;
use JetApplication\EShopEntity_Basic;
use JetApplication\Admin_EntityManager_EditorPlugin;

abstract class Core_Admin_EntityManager_EditorPlugin {
	protected string $base_dir;
	protected MVC_View $view;
	protected MVC_View $main_view;
	protected EShopEntity_Basic $item;
	
	public const KEY = null;
	
	public static function getKey(): string
	{
		return static::KEY;
	}
	
	public function __construct( string $base_dir, MVC_View $main_view, EShopEntity_Basic $item )
	{
		$this->base_dir = $base_dir;
		$this->main_view = $main_view;
		$this->item = $item;
		
		$this->view = Factory_MVC::getViewInstance( $base_dir.'views/' );
		$this->view->setVar('item', $item);
		$this->view->setVar('handler', $this);
		
		$this->init();
	}
	
	public function handleOnlyIfItemIsEditable() : bool
	{
		return true;
	}
	
	public function canBeHandled() : bool
	{
		if(
			$this->handleOnlyIfItemIsEditable() &&
			(
				!$this->item->isEditable() ||
				!$this->currentUserCanEdit()
			)
		) {
			return false;
		}
		
		return true;
	}
	
	abstract protected function currentUserCanEdit() : bool;
	
	abstract public function hasDialog() : bool;
	
	abstract protected function init();
	
	public function renderDialog() : string
	{
		if(
			!$this->canBeHandled() ||
			!$this->hasDialog()
		) {
			return '';
		}
		
		return $this->view->render('dialog');
	}
	
	public function renderButton() : string
	{
		if(
			!$this->canBeHandled()
		) {
			return '';
		}
		
		return $this->view->render('button');
	}
	
	
	
	abstract public function handle() : void;
	
	protected static ?array $plugins = null;
	
	public static function initPlugins(
		MVC_View $main_view,
		EShopEntity_Basic $item
	) : void
	{
		static::$plugins = [];
		
		$base_dir = dirname(Autoloader::getScriptPath( static::class )).'/Plugin';
		
		$dirs = IO_Dir::getSubdirectoriesList( $base_dir );
		
		foreach($dirs as $path=>$dir) {
			$class_name = static::class.'_'.$dir.'_Main';
			
			/**
			 * @var Admin_EntityManager_EditorPlugin $handler
			 */
			$handler = new $class_name( $path, $main_view, $item );
			
			static::$plugins[$handler::getKey()] = $handler;
		}
		
	}
	
	/**
	 * @return Admin_EntityManager_EditorPlugin[]
	 */
	public static function getPlugins() : array
	{
		return static::$plugins;
	}
	
	public static function get( string $key ) : ?Admin_EntityManager_EditorPlugin
	{
		return static::$plugins[$key]??null;
	}
	
	public static function handlePlugins() : void
	{
		foreach( static::getPlugins() as $handler) {
			if( $handler->canBeHandled() ) {
				$handler->handle();
			}
		}
	}
	
}