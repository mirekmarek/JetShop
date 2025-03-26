<?php
/**
 * @copyright Copyright (c) Miroslav Marek <mirek.marek@web-jet.cz>
 * @license EUPL 1.2  https://eupl.eu/1.2/en/
 * @author Miroslav Marek <mirek.marek@web-jet.cz>
 */
namespace JetApplicationModule\EShop\Analytics\Service\JetAnalytics;

use Jet\Application_Module;
use Jet\Data_DateTime;
use Jet\Http_Request;
use Jet\MVC_Controller_Default;

abstract class Report_Controller extends MVC_Controller_Default
{
	protected Application_Module|Main|null $module;
	
	/**
	 * @var Report[]
	 */
	protected array $reports;
	protected Report $selected_report;
	
	/**
	 * @var Report_TimePeriod[]
	 */
	protected array $time_periods;
	protected Report_TimePeriod $selected_time_period;
	
	protected Data_DateTime $date_from;
	protected Data_DateTime $date_to;
	
	protected array $subreports;
	
	protected string $selected_subreport;
	
	public function getReports(): array
	{
		return $this->reports;
	}
	
	public function getSelectedReport(): Report
	{
		return $this->selected_report;
	}
	
	public function getTimePeriods(): array
	{
		return $this->time_periods;
	}
	
	public function getSelectedTimePeriod(): Report_TimePeriod
	{
		return $this->selected_time_period;
	}
	
	public function getDateFrom(): Data_DateTime
	{
		return $this->date_from;
	}
	
	public function getDateTo(): Data_DateTime
	{
		return $this->date_to;
	}
	
	public function getSubreports(): array
	{
		return $this->subreports;
	}
	
	public function getSelectedSubreport(): string
	{
		return $this->selected_subreport;
	}
	
	
	
	
	abstract public function getReportsList() : array;
	
	public function resolve() : string
	{
		
		$this->module->setReportController( $this );
		
		$this->reports = $this->getReportsList();
		
		$GET = Http_Request::GET();
		
		$selected_report_key = $GET->getString(
			'report',
			default_value: '',
			valid_values: array_keys($this->reports)
		);
		
		if($selected_report_key) {
			$this->selected_report = $this->reports[$selected_report_key];
		} else {
			foreach($this->reports as $report) {
				if($report->isDefault()) {
					$this->selected_report = $report;
					break;
				}
			}
		}
		
		$this->time_periods = $this->selected_report->getTimePeriods();
		
		$selected_tp_key = $GET->getString(
			'tp',
			default_value: '',
			valid_values: array_keys($this->time_periods)
		);
		if($selected_tp_key) {
			$this->selected_time_period = $this->time_periods[$selected_tp_key];
		} else {
			foreach($this->time_periods as $tp) {
				if($tp->isDefault()) {
					$this->selected_time_period = $tp;
				}
			}
		}
		
		$this->date_from = new Data_DateTime($GET->getString( 'from', default_value: $this->selected_time_period->getFrom() ));
		$this->date_to = new Data_DateTime($GET->getString( 'to', default_value: $this->selected_time_period->getTo() ));
		
		$this->subreports = $this->selected_report->getSubreports();
		$subreports = array_keys( $this->subreports );
		
		$this->selected_subreport = Http_Request::GET()->getString('sr', default_value: $subreports[0], valid_values: $subreports);
		
		
		$this->view->setVar( 'module', $this->module );
		
		$this->view->setVar( 'reports', $this->reports );
		$this->view->setVar( 'selected_report', $this->selected_report );
		
		$this->view->setVar( 'time_periods', $this->time_periods );
		$this->view->setVar( 'selected_time_period', $this->selected_time_period );
		
		$this->view->setVar( 'date_from', $this->date_from );
		$this->view->setVar( 'date_to', $this->date_to );
		
		$this->initReport();
		
		return 'default';
	}
	
	abstract protected function preinitReport() : void;
	
	protected function initReport() : void
	{
		$this->preinitReport();
		/**
		 * @var Main $module
		 */
		$module = $this->module;
		$this->selected_report->init( $this );
		
	}
	
	protected function output( string $view_script ): void
	{
		$output = $this->view->render( $view_script );
		
		$this->content->setOutput( $output );
	}
	
}