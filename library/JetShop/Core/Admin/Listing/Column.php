<?php
/**
 * @copyright Copyright (c) Miroslav Marek <mirek.marek@web-jet.cz>
 * @license EUPL 1.2  https://eupl.eu/1.2/en/
 * @author Miroslav Marek <mirek.marek@web-jet.cz>
 */
namespace JetShop;

use Jet\Autoloader;
use Jet\DataListing_Column;
use Jet\Factory_MVC;
use Jet\MVC_View;

abstract class Core_Admin_Listing_Column extends DataListing_Column
{
	public const KEY = null;
	
	public function getKey(): string
	{
		return static::KEY;
	}
	
	protected function getView() : MVC_View
	{
		$view_dir = dirname( Autoloader::getScriptPath( static::class ), 3 ).'/views/list/column/';
		
		$view = Factory_MVC::getViewInstance( $view_dir );
		$view->setVar('listing', $this->listing );
		$view->setVar('column', $this );
		
		return $view;
	}
	
	public function render( mixed $item ): string
	{
		$view = $this->getView();
		$view->setVar('item', $item);
		
		return $view->render( $this->getKey() );
	}
	
	
}