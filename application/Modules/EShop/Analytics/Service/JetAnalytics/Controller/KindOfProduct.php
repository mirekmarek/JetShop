<?php
/**
 * @copyright Copyright (c) Miroslav Marek <mirek.marek@web-jet.cz>
 * @license EUPL 1.2  https://eupl.eu/1.2/en/
 * @author Miroslav Marek <mirek.marek@web-jet.cz>
 */
namespace JetApplicationModule\EShop\Analytics\Service\JetAnalytics;

use JetApplication\KindOfProduct;

class Controller_KindOfProduct extends Report_Controller
{
	
	protected KindOfProduct $kind_of_product;
	
	/**
	 * @var Report_KindOfProduct[]
	 */
	protected array $reports;
	protected Report|Report_KindOfProduct $selected_report;
	
	public function setKindOfProduct( KindOfProduct $kind_of_product ): void
	{
		$this->kind_of_product = $kind_of_product;
	}
	
	protected function preinitReport() : void
	{
		$this->selected_report->setKindOfProduct( $this->kind_of_product );
	}
	
	public function getReportsList() : array
	{
		return Report_KindOfProduct::getList();
	}
	
	
	public function default_Action() : void
	{
		$this->output('kind-of-product');
	}
	
	
	
}