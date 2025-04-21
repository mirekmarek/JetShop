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

abstract class Core_Admin_Listing_Column extends DataListing_Column
{
	public const KEY = null;
	
	public function getKey(): string
	{
		return static::KEY;
	}
	
	public function render( mixed $item ): string
	{
		
		$view_dir = dirname( Autoloader::getScriptPath( static::class ), 3 ).'/views/list/column/';
		
		$view = Factory_MVC::getViewInstance( $view_dir );
		$view->setVar('item', $item);
		$view->setVar('listing', $this->listing );
		$view->setVar('column', $this );
		
		return $view->render( $this->getKey() );
	}
	
	
}