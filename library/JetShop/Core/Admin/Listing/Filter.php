<?php
/**
 * @copyright Copyright (c) Miroslav Marek <mirek.marek@web-jet.cz>
 * @license EUPL 1.2  https://eupl.eu/1.2/en/
 * @author Miroslav Marek <mirek.marek@web-jet.cz>
 */
namespace JetShop;

use Jet\Autoloader;
use Jet\DataListing_Filter;
use Jet\Factory_MVC;
use Jet\MVC_View;

abstract class Core_Admin_Listing_Filter extends DataListing_Filter
{
	public const KEY = null;
	
	public function getKey(): string
	{
		return static::KEY;
	}
	
	protected function getView() : MVC_View
	{
		$view_dir = dirname( Autoloader::getScriptPath( static::class ), 3 ).'/views/list/filter/';
		
		$view = Factory_MVC::getViewInstance( $view_dir );
		$view->setVar('filter', $this );
		$view->setVar('listing', $this->listing);
		
		return $view;
	}
	
	
	public function renderForm(): string
	{
		return $this->getView()->render( $this->getKey() );
	}
	
	abstract public function isActive(): bool;

}