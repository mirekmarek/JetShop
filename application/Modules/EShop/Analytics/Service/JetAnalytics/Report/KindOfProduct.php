<?php
/**
 * @copyright Copyright (c) Miroslav Marek <mirek.marek@web-jet.cz>
 * @license EUPL 1.2  https://eupl.eu/1.2/en/
 * @author Miroslav Marek <mirek.marek@web-jet.cz>
 */
namespace JetApplicationModule\EShop\Analytics\Service\JetAnalytics;

use JetApplication\KindOfProduct;

abstract class Report_KindOfProduct extends Report
{
	protected KindOfProduct $kind_of_product;
	
	public function getKindOfProduct(): KindOfProduct
	{
		return $this->kind_of_product;
	}
	
	public function setKindOfProduct( KindOfProduct $customer ): void
	{
		$this->kind_of_product = $customer;
	}
	
	public function init( Report_Controller|Controller_KindOfProduct $controller ) : void
	{
		parent::init( $controller );
		$this->view->setVar( 'kind_of_product', $this->kind_of_product );
	}
	
	
}