<?php
/**
 * @copyright Copyright (c) Miroslav Marek <mirek.marek@web-jet.cz>
 * @license EUPL 1.2  https://eupl.eu/1.2/en/
 * @author Miroslav Marek <mirek.marek@web-jet.cz>
 */
namespace JetApplicationModule\Admin\Entity\Listing;


use Jet\DataListing_Column;
use Jet\Factory_MVC;
use JetApplication\Admin_Managers;

abstract class Listing_Column_Abstract extends DataListing_Column
{

	public function render( mixed $item ): string
	{
		
		$view = Factory_MVC::getViewInstance( Admin_Managers::EntityListing()->getViewsDir().'list/column/' );
		$view->setVar('item', $item);
		$view->setVar('listing', $this->listing );
		$view->setVar('column', $this );
		
		return $view->render( $this->getKey() );
	}
	
}