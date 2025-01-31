<?php
/**
 * @copyright Copyright (c) Miroslav Marek <mirek.marek@web-jet.cz>
 * @license EUPL 1.2  https://eupl.eu/1.2/en/
 * @author Miroslav Marek <mirek.marek@web-jet.cz>
 */
namespace JetApplicationModule\Admin\Orders;


use Jet\Factory_MVC;
use Jet\IO_Dir;
use Jet\MVC_View;
use JetApplication\Admin_Managers_EShopEntity_Listing;


abstract class ListingHandler {
	protected string $base_dir;
	protected MVC_View $view;
	protected MVC_View $main_view;
	protected ?Admin_Managers_EShopEntity_Listing $listing_manager = null;
	
	protected bool $has_dialog = false;
	
	public const KEY = null;
	
	public static function getKey(): string
	{
		return static::KEY;
	}
	
	public function __construct( string $base_dir, MVC_View $main_view, Admin_Managers_EShopEntity_Listing $listing_manager )
	{
		$this->base_dir = $base_dir;
		$this->main_view = $main_view;
		$this->listing_manager = $listing_manager;
		
		$this->view = Factory_MVC::getViewInstance( $base_dir.'views/' );
		$this->view->setVar('listing_manager', $listing_manager);
		$this->view->setVar('handler', $this);
		
		$this->init();
	}
	
	
	public function canBeHandled() : bool
	{
		if(!Main::getCurrentUserCanEdit()) {
			return false;
		}
		
		
		return true;
	}
	
	public function hasDialog() : bool
	{
		return $this->has_dialog;
	}
	
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
	
	protected static ?array $handlers = null;
	
	public static function initHandlers( MVC_View $main_view, Admin_Managers_EShopEntity_Listing $listing_manager ) : void
	{
		static::$handlers = [];
		
		$dirs = IO_Dir::getSubdirectoriesList( __DIR__.'/ListingHandler' );
		
		foreach($dirs as $path=>$dir) {
			$class_name = ListingHandler::class.'_'.$dir.'_Main';
			
			/**
			 * @var Handler $handler
			 */
			$handler = new $class_name( $path, $main_view, $listing_manager );
			
			static::$handlers[$handler::getKey()] = $handler;
		}
		
	}
	
	/**
	 * @return ListingHandler[]
	 */
	public static function getHandlers() : array
	{
		return static::$handlers;
	}
	
	public static function getHandler( string $key ) : ?ListingHandler
	{
		return static::$handlers[$key]??null;
	}
	
	public static function handleHandlers() : void
	{
		foreach(static::getHandlers() as $handler) {
			if( $handler->canBeHandled() ) {
				$handler->handle();
			}
		}
	}
	
}