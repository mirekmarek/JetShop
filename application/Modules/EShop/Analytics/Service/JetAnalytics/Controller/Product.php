<?php
/**
 * @copyright Copyright (c) Miroslav Marek <mirek.marek@web-jet.cz>
 * @license EUPL 1.2  https://eupl.eu/1.2/en/
 * @author Miroslav Marek <mirek.marek@web-jet.cz>
 */
namespace JetApplicationModule\EShop\Analytics\Service\JetAnalytics;

use JetApplication\Product;

class Controller_Product extends Report_Controller
{
	
	protected Product $product;
	
	/**
	 * @var Report_Product[]
	 */
	protected array $reports;
	protected Report|Report_Product $selected_report;
	
	public function setProduct( Product $product ): void
	{
		$this->product = $product;
	}
	
	protected function preinitReport() : void
	{
		$this->selected_report->setProduct( $this->product );
	}
	
	public function getReportsList() : array
	{
		return Report_Product::getList();
	}
	
	
	public function default_Action() : void
	{
		$this->output('product');
	}
	
	
	
}