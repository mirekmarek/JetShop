<?php
/**
 * @copyright Copyright (c) Miroslav Marek <mirek.marek@web-jet.cz>
 * @license EUPL 1.2  https://eupl.eu/1.2/en/
 * @author Miroslav Marek <mirek.marek@web-jet.cz>
 */
namespace JetShop;


use Jet\Autoloader;
use Jet\DataListing_ElementBase;
use Jet\Factory_MVC;
use Jet\MVC_View;


abstract class Core_Admin_Listing_Handler extends DataListing_ElementBase
{
	protected bool $has_dialog = false;
	
	public const KEY = null;
	
	public static function getKey(): string
	{
		return static::KEY;
	}
	
	public function hasDialog() : bool
	{
		return $this->has_dialog;
	}
	
	
	protected function getView() : MVC_View
	{
		$view_dir = dirname( Autoloader::getScriptPath( static::class ), 3 ).'/views/list/handler/'.static::getKey().'/';
		
		$view = Factory_MVC::getViewInstance( $view_dir );
		$view->setVar('filter', $this );
		$view->setVar('listing', $this->listing);
		
		return $view;
	}
	
	
	public function renderDialog() : string
	{
		if(
			!$this->hasDialog() ||
			!$this->canBeHandled()
		) {
			return '';
		}
		
		$view = $this->getView();
		
		return $view->render('dialog');
	}
	
	public function renderButton() : string
	{
		if(
			!$this->canBeHandled()
		) {
			return '';
		}
		
		$view = $this->getView();
		
		
		return $view->render('button');
	}
	
	abstract public function canBeHandled() : bool;

	abstract public function handle() : void;
	
}