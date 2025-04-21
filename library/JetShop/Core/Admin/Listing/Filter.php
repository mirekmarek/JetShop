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

abstract class Core_Admin_Listing_Filter extends DataListing_Filter
{
	
	public function renderForm(): string
	{
		
		$view_dir = dirname( Autoloader::getScriptPath( static::class ), 3 ).'/views/list/filter/';
		
		$view = Factory_MVC::getViewInstance( $view_dir );
		$view->setVar('filter', $this );
		$view->setVar('listing', $this->listing);
		
		return $view->render( $this->getKey() );
	}
	
}