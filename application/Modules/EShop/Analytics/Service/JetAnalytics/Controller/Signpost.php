<?php
/**
 * @copyright Copyright (c) Miroslav Marek <mirek.marek@web-jet.cz>
 * @license EUPL 1.2  https://eupl.eu/1.2/en/
 * @author Miroslav Marek <mirek.marek@web-jet.cz>
 */
namespace JetApplicationModule\EShop\Analytics\Service\JetAnalytics;

use JetApplication\Signpost;

class Controller_Signpost extends Report_Controller
{
	
	protected Signpost $signpost;
	
	/**
	 * @var Report_Signpost[]
	 */
	protected array $reports;
	protected Report|Report_Signpost $selected_report;
	
	public function setSignpost( Signpost $signpost ): void
	{
		$this->signpost = $signpost;
	}
	
	protected function preinitReport() : void
	{
		$this->selected_report->setSignpost( $this->signpost );
	}
	
	public function getReportsList() : array
	{
		return Report_Signpost::getList();
	}
	
	
	public function default_Action() : void
	{
		$this->output('signpost');
	}
	
	
	
}