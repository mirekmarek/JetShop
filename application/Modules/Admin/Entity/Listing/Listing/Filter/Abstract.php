<?php
/**
 * @copyright Copyright (c) Miroslav Marek <mirek.marek@web-jet.cz>
 * @license EUPL 1.2  https://eupl.eu/1.2/en/
 * @author Miroslav Marek <mirek.marek@web-jet.cz>
 */
namespace JetApplicaTionModule\Admin\Entity\Listing;


use Jet\DataListing_Filter;
use Jet\Factory_MVC;
use JetApplication\Admin_Managers;


abstract class Listing_Filter_Abstract extends DataListing_Filter
{
	public function renderForm(): string
	{
		$view = Factory_MVC::getViewInstance( Admin_Managers::EntityListing()->getViewsDir().'list/filter/' );
		$view->setVar('filter', $this );
		$view->setVar('listing', $this->listing);
		
		return $view->render( $this->getKey() );
	}
	
}