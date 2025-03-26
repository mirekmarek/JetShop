<?php
/**
 * @copyright Copyright (c) Miroslav Marek <mirek.marek@web-jet.cz>
 * @license EUPL 1.2  https://eupl.eu/1.2/en/
 * @author Miroslav Marek <mirek.marek@web-jet.cz>
 */
namespace JetApplicationModule\EShop\Analytics\Service\JetAnalytics;

use JetApplication\Customer;

class Controller_Customer extends Report_Controller
{
	
	protected Customer $customer;
	
	/**
	 * @var Report_Customer[]
	 */
	protected array $reports;
	protected Report|Report_Customer $selected_report;
	
	public function setCustomer( Customer $customer ): void
	{
		$this->customer = $customer;
	}
	
	protected function preinitReport() : void
	{
		$this->selected_report->setCustomer( $this->customer );
	}
	
	public function getReportsList() : array
	{
		return Report_Customer::getList();
	}
	
	
	public function default_Action() : void
	{
		$this->output('customer');
	}
	

	
}