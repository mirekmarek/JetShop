<?php
/**
 * @copyright Copyright (c) Miroslav Marek <mirek.marek@web-jet.cz>
 * @license EUPL 1.2  https://eupl.eu/1.2/en/
 * @author Miroslav Marek <mirek.marek@web-jet.cz>
 */
namespace JetApplicationModule\Admin\Stats\Views;


use Jet\Http_Request;
use Jet\MVC_Controller_Default;
use JetApplication\EShops;


class Controller_Main extends MVC_Controller_Default
{
	/**
	 * @var Statistics[]
	 */
	protected array $stats = [];
	
	protected string $default_stat_key = '';
	
	protected function registerStat( Statistics $stat ) : void
	{
		if(!$this->default_stat_key) {
			$this->default_stat_key = $stat->getKey();
		}
		
		$this->stats[ $stat->getKey() ] = $stat;
	}
	
	public function setupStat() : Statistics
	{
		$GET = Http_Request::GET();
		
		$stat_key = $GET->getString(
			'stat',
			default_value: $this->default_stat_key,
			valid_values: array_keys( $this->stats )
		);
		
		$stat = $this->stats[ $stat_key ];
		$stat->setIsSelected( true );
		$stat->setEshop( EShops::get( $GET->getString('eshop', EShops::getCurrentKey() ) ) );
		$stat->setView( $this->view );
		
		
		/*
		$this_year = date('Y');
		
		$start_year = $GET->getInt('start_year', date('Y')-1 );
		if($start_year<2000) {
			$start_year = 2000;
		}
		if($start_year>=$this_year) {
			$start_year = $this_year-1;
		}
		
		$end_year = $GET->getInt('end_year', $this_year );
		if($end_year>$this_year) {
			$end_year = $this_year;
		}
		
		if( $start_year >= $end_year ) {
			$end_year = $start_year + 1;
		}
		
		
		
		
		
		if(!$GET->exists('display_days')) {
			$display_days = $stat->getDisplayDaysByDefault();
		} else {
			$display_days = $GET->getBool('display_days');
		}
		
	
		$stat->setCurrentMonth( date('m') );
		$stat->setStartYear( $start_year );
		$stat->setEndYear( $end_year );
		$stat->setDisplayDays( $display_days );
		*/
		
		return $stat;
	}
	
	public function default_Action() : void
	{
		

		$this->registerStat( new Stats_ProductsByTimePeriod() );
		
		$stat = $this->setupStat();
		
		$this->view->setVar( 'stats', $this->stats );
		$this->view->setVar( 'stat', $stat );
		
		$stat->init();
		
		$this->output( 'default' );
	}
}