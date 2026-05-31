<?php
/**
 * @copyright Copyright (c) Miroslav Marek <mirek.marek@web-jet.cz>
 * @license EUPL 1.2  https://eupl.eu/1.2/en/
 * @author Miroslav Marek <mirek.marek@web-jet.cz>
 */
namespace JetApplicationModule\EShop\Analytics\Service\JetAnalytics;


class Report_General_OrderSources extends Report_General
{
	public const KEY = 'order_sources';
	protected ?string $title = 'Order sources';
	protected bool $is_default = true;
	protected bool $one_eshop_mode = true;
	protected array $sub_reports = [
		//'summary' => 'Summary',
		'pie_chart' => 'Pie Chart',
		'chart' => 'Chart',
		//'details_per_day' => 'Numbers per Day',
	];
	
	public function prepare_summary() : void
	{
		$data = $this->getRawData();
		$this->view->setVar('data', $data);
	}
	
	public function prepare_chart() : void
	{
		$data = $this->getRawData();
		$this->view->setVar('data', $data);
	}
	
	public function prepare_pie_chart() : void
	{
		$data = $this->getRawData();
		$this->view->setVar('data', $data);
	}
	
	public function prepare_details_per_day() : void
	{
		$data = $this->getRawData();
		$this->view->setVar('data', $data);
	}
	
	
	
	protected function getRawData() : array
	{
		
		$orders = Event_Purchase::dataFetchAll(
			select: [
				'session_id',
				'date_time',
				'total' => 'total_amount_with_VAT'
			],
			where: [
				$this->selected_eshop->getWhere(),
				'AND',
				'date_time >=' => $this->date_from,
				'AND',
				'date_time <=' => $this->date_to,
			]
		);
		
		$session_ids = [0];
		
		foreach($orders as $o) {
			$session_ids[] = $o['session_id'];
		}
		
		$sessions = Session::dataFetchPairs(
			select: [
				'id',
				'source'
			],
			where: [
				'id' => $session_ids
			],
			raw_mode: true
		);
		
		
		$data = [];
		
		foreach(Session_SourceDetector::getSources() as $source) {
			$data[$source] = [];
		}
		
		foreach($orders as $o) {
			$source = $sessions[$o['session_id']]??'';
			if(!$source) {
				continue;
			}
			
			$r = [
				'date_time' => $o['date_time']->format('Y-m-d'),
				'source' => $source,
				'amount' => $o['total'],
			];
			
			$data[$source][] = $r;
		}
		
		$res = [
			'count' => [],
			'total' => [],
		];
		foreach($data as $source=>$d) {
			$res['count'][$source] = $this->prepareData_PerDay_Count( $d, 'date_time' );
			$res['total'][$source] = $this->prepareData_PerDay_Total( $d, 'amount', 'date_time' );
		}
		
		//var_dump($res);
		//die();
		
		return $res;
	}
	
}