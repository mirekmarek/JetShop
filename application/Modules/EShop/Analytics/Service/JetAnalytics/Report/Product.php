<?php
/**
 * @copyright Copyright (c) Miroslav Marek <mirek.marek@web-jet.cz>
 * @license EUPL 1.2  https://eupl.eu/1.2/en/
 * @author Miroslav Marek <mirek.marek@web-jet.cz>
 */
namespace JetApplicationModule\EShop\Analytics\Service\JetAnalytics;

use JetApplication\Product;

abstract class Report_Product extends Report
{
	protected Product $product;
	
	public function getProduct(): Product
	{
		return $this->product;
	}
	
	public function setProduct( Product $product ): void
	{
		$this->product = $product;
	}
	
	public function init( Report_Controller|Controller_Product $controller ) : void
	{
		parent::init( $controller );
		$this->view->setVar( 'product', $this->product );
	}
	
}