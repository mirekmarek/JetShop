<?php
/**
 * @copyright Copyright (c) Miroslav Marek <mirek.marek@web-jet.cz>
 * @license EUPL 1.2  https://eupl.eu/1.2/en/
 * @author Miroslav Marek <mirek.marek@web-jet.cz>
 */
namespace JetApplicationModule\EShop\Analytics\Service\JetAnalytics;


use Jet\Autoloader;
use Jet\Data_DateTime;
use Jet\Http_Request;
use Jet\IO_Dir;
use Jet\MVC_View;
use Jet\Tr;
use JetApplication\EShop;
use JetApplication\EShops;

abstract class Report
{
	public const KEY = null;
	protected ?string $title = null;
	protected bool $is_default = false;
	protected array $sub_reports;
	protected int $priority = 0;
	protected bool $one_eshop_mode = false;
	
	protected Data_DateTime $date_from ;
	protected Data_DateTime $date_to;
	protected Report_TimePeriod $time_period;
	protected Main $module;
	protected string $selected_subreport;
	protected array $selected_eshop_keys = [];
	
	
	protected ?MVC_View $view = null;
	
	
	/**
	 * @var Report_TimePeriod[]
	 */
	protected ?array $time_periods = null;
	
	public static function getKey() : string
	{
		return static::KEY;
	}
	
	public function getPriority(): int
	{
		return $this->priority;
	}
	
	
	
	public function getTitle() : string
	{
		return Tr::_($this->title);
	}
	
	public function isDefault(): bool
	{
		return $this->is_default;
	}
	
	public function getSubReports() : array
	{
		return $this->sub_reports;
	}
	
	public function getSelectedEshopKeys(): array
	{
		return $this->selected_eshop_keys;
	}
	
	public function isIsDefault(): bool
	{
		return $this->is_default;
	}
	
	public function getDateFrom(): Data_DateTime
	{
		return $this->date_from;
	}
	
	public function getDateTo(): Data_DateTime
	{
		return $this->date_to;
	}
	
	public function getTimePeriod(): Report_TimePeriod
	{
		return $this->time_period;
	}
	
	public function getModule(): Main
	{
		return $this->module;
	}
	
	public function getSelectedSubreport(): string
	{
		return $this->selected_subreport;
	}
	
	
	
	protected function handleSelectedEShopKeys() : void
	{
		$all_eshop_keys = ['total'];
		
		foreach(EShops::getListSorted() as $eshop) {
			if(!$eshop->getIsVirtual()) {
				$all_eshop_keys[] = $eshop->getKey();
			}
		}
		$this->selected_eshop_keys = [];
		
		$GET = Http_Request::GET();
		if($this->one_eshop_mode) {
			
			$keys = $GET->getString('eshop', default_value: EShops::getDefault()->getKey(), valid_values: $all_eshop_keys);
			$this->selected_eshop_keys = [ $keys ];
			
		} else {
			if($GET->exists('eshop')) {
				$keys = $GET->getRaw('eshop');
				$this->selected_eshop_keys = array_intersect( $all_eshop_keys, $keys );
			}
			
			if(!$this->selected_eshop_keys) {
				$this->selected_eshop_keys = $all_eshop_keys;
			}
			
		}
		
		
	}
	
	
	public function init( Report_Controller $controller ) : void
	{
		$this->date_from = $controller->getDateFrom();
		$this->date_to = $controller->getDateTo();
		$this->time_period = $controller->getSelectedTimePeriod();
		/** @noinspection PhpFieldAssignmentTypeMismatchInspection */
		$this->module = $controller->getModule();
		$this->selected_subreport = $controller->getSelectedSubreport();
		
		$this->handleSelectedEShopKeys();
		
		$subreports = array_keys( $this->getSubReports() );
		
		$this->getView();
		$this->view->setVar( 'module', $this->module );
		$this->view->setVar( 'report', $this );
		$this->view->setVar( 'date_from', $this->date_from );
		$this->view->setVar( 'date_to', $this->date_to );
		$this->view->setVar( 'time_period', $this->time_period );
		$this->view->setVar( 'subreports', $subreports );
		
		$this->{"prepare_{$this->selected_subreport}"}();
	}
	
	/**
	 * @return static[]
	 */
	public static function getList() : array
	{
		$dir = str_replace('.php', '', Autoloader::getScriptPath( static::class )).'/';
		
		$list = [];
		foreach( IO_Dir::getFilesList( $dir ) as $file_name ) {
			$file_name = pathinfo( $file_name, PATHINFO_FILENAME );
			
			$class_name = static::class.'_'.$file_name;
			
			$report = new $class_name();
			
			$list[$report::getKey()] = $report;
		}
		
		uasort( $list, function( Report $a, Report $b ) {
			return $a->getPriority() <=> $b->getPriority();
			
		} );
		
		return $list;
	}
	
	public function getViewDir() : string
	{
		$dir = str_replace('.php', '', Autoloader::getScriptPath( static::class )).'/views/';
		
		return $dir;
	}
	
	public function getView() : MVC_View
	{
		if(!$this->view) {
			$this->view = new MVC_View( $this->getViewDir() );
			$this->view->setVar( 'report', $this );
		}
		
		return $this->view;
	}
	
	protected function initTimePeriods() : void
	{
		$this->time_periods = [];
		
		$this->addTimePeriod( new Report_TimePeriod_Last7Days() );
		$this->addTimePeriod( new Report_TimePeriod_Last30Days() );
		$this->addTimePeriod( new Report_TimePeriod_Last365Days() );
		$this->addTimePeriod( new Report_TimePeriod_PreviousMonth() );
		$this->addTimePeriod( new Report_TimePeriod_PreviousQuarter() );
		$this->addTimePeriod( new Report_TimePeriod_PreviousYear() );
	}
	
	protected function addTimePeriod( Report_TimePeriod $period ) : void
	{
		$this->time_periods[$period::getKey()] = $period;
	}
	
	/**
	 * @return Report_TimePeriod[]
	 */
	public function getTimePeriods() : array
	{
		if($this->time_periods===null) {
			$this->initTimePeriods();
		}
		
		return $this->time_periods;
	}
	
	public function getDefaultTimePeriod() : ?Report_TimePeriod
	{
		foreach($this->getTimePeriods() as $period) {
			if($period->isDefault()) {
				return $period;
			}
		}
		
		return null;
	}
	
	public function show() : string
	{
		return $this->view->render( $this->selected_subreport );
	}
	
	public function prepareDayMap() : array
	{
		$day = strtotime($this->date_from);
		$end = strtotime($this->date_to);
		
		$map = [];
		
		do {
			$map[date('Y-m-d', $day)] = 0;
			$day += 86400;
		} while($day < $end);
		
		return $map;
	}
	
	public function prepareDataPerShopPerDay( array $data, array $eshop_keys, $date_column='date_time' ) : array
	{
		
		$res = [];
		foreach($eshop_keys as $key) {
			$res[$key] = $this->prepareDayMap();
		}
		
		foreach($data as $row) {
			$date = explode(' ', $row[$date_column])[0];
			
			$eshop_code = $row['eshop_code'];
			$locale = $row['locale'];
			
			$eshop_key = EShop::generateKey( $eshop_code, $locale );
			
			if(isset($res['total'])) {
				$res['total'][$date]++;
			}
			
			if(isset($res[$eshop_key])) {
				$res[$eshop_key][$date]++;
			}
		}

		return $res;
	}
	
}