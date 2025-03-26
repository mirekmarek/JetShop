<?php
/**
 * @copyright Copyright (c) Miroslav Marek <mirek.marek@web-jet.cz>
 * @license EUPL 1.2  https://eupl.eu/1.2/en/
 * @author Miroslav Marek <mirek.marek@web-jet.cz>
 */
namespace JetApplicationModule\EShop\Analytics\Service\JetAnalytics;

use JetApplication\Category;

class Controller_Category extends Report_Controller
{
	
	protected Category $category;
	
	/**
	 * @var Report_Category[]
	 */
	protected array $reports;
	protected Report|Report_Category $selected_report;
	
	public function setCategory( Category $category ): void
	{
		$this->category = $category;
	}
	
	protected function preinitReport() : void
	{
		$this->selected_report->setCategory( $this->category );
	}
	
	public function getReportsList() : array
	{
		return Report_Category::getList();
	}
	
	
	public function default_Action() : void
	{
		$this->output('category');
	}
	
	
	
}