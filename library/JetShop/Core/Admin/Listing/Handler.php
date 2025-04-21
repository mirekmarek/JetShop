<?php
/**
 * @copyright Copyright (c) Miroslav Marek <mirek.marek@web-jet.cz>
 * @license EUPL 1.2  https://eupl.eu/1.2/en/
 * @author Miroslav Marek <mirek.marek@web-jet.cz>
 */
namespace JetShop;


use Jet\DataListing_ElementBase;
use Jet\MVC_View;


abstract class Core_Admin_Listing_Handler extends DataListing_ElementBase
{
	protected string $base_dir;
	protected MVC_View $view;
	protected MVC_View $main_view;
	
	protected bool $has_dialog = false;
	
	public const KEY = null;
	
	public static function getKey(): string
	{
		return static::KEY;
	}
	
	public function __construct()
	{
		/*
		$this->base_dir = $base_dir;
		$this->main_view = $main_view;
		$this->listing_manager = $listing_manager;
		
		$this->view = Factory_MVC::getViewInstance( $base_dir.'views/' );
		$this->view->setVar('listing_manager', $listing_manager);
		$this->view->setVar('handler', $this);
		
		$this->init();
		*/
	}
	
	
	abstract public function canBeHandled() : bool;
	
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
	
}