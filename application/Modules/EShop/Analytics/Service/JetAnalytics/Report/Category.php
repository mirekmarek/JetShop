<?php
/**
 * @copyright Copyright (c) Miroslav Marek <mirek.marek@web-jet.cz>
 * @license EUPL 1.2  https://eupl.eu/1.2/en/
 * @author Miroslav Marek <mirek.marek@web-jet.cz>
 */
namespace JetApplicationModule\EShop\Analytics\Service\JetAnalytics;

use JetApplication\Category;

abstract class Report_Category extends Report
{
	protected Category $category;
	
	public function getCategory(): Category
	{
		return $this->category;
	}
	
	public function setCategory( Category $category ): void
	{
		$this->category = $category;
	}
	
	public function init( Report_Controller|Controller_Category $controller ) : void
	{
		parent::init( $controller );
		$this->view->setVar( 'category', $this->category );
	}

}