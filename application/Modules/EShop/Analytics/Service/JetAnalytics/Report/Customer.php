<?php
/**
 * @copyright Copyright (c) Miroslav Marek <mirek.marek@web-jet.cz>
 * @license EUPL 1.2  https://eupl.eu/1.2/en/
 * @author Miroslav Marek <mirek.marek@web-jet.cz>
 */
namespace JetApplicationModule\EShop\Analytics\Service\JetAnalytics;

use JetApplication\Customer;

abstract class Report_Customer extends Report
{
	protected Customer $customer;
	
	public function getCustomer(): Customer
	{
		return $this->customer;
	}
	
	public function setCustomer( Customer $customer ): void
	{
		$this->customer = $customer;
	}
	
	public function init( Report_Controller|Controller_Customer $controller ) : void
	{
		parent::init( $controller );
		$this->view->setVar( 'customer', $this->customer );
	}
	
	
}