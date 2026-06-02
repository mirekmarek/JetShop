<?php
/**
 * @copyright Copyright (c) Miroslav Marek <mirek.marek@web-jet.cz>
 * @license EUPL 1.2  https://eupl.eu/1.2/en/
 * @author Miroslav Marek <mirek.marek@web-jet.cz>
 */
namespace JetApplicationModule\EShop\Analytics\Service\JetAnalytics;


class Report_General_ConversionRate extends Report_General
{
	public const KEY = 'conversion_rate';
	protected ?string $title = 'Conversion Rate';
	protected bool $is_default = false;
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
		
		$sessions = Session::dataFetchAll(
			select: [
				'id',
				'date_time' => 'start_date_time',
				'shopping_cart_used',
				'purchased',
			],
			where: [
				$this->selected_eshop->getWhere(),
				'AND',
				'start_date_time >=' => $this->date_from,
				'AND',
				'start_date_time <=' => $this->date_to,
			]
		);
		
		$days = $this->prepareDayMap();
		
		$data = [
			'sessions_count' => $days,
			'purchased'      => $days,
			'cart_used'      => $days,
			'no_shopping'   => $days
		];
		
		
		foreach($sessions as $s) {
			
			$date = $s['date_time']->format('Y-m-d');
			
			
			if($s['purchased']) {
				$what = 'purchased';
			} else {
				if($s['shopping_cart_used']) {
					$what = 'cart_used';
				} else {
					$what = 'no_shopping';
				}
			}
			
			$data[$what][$date] = rand(10, 500);
		}
		
		foreach($days as $date=>$v) {
			$data['sessions_count'][$date] = $data['purchased'][$date]+$data['cart_used'][$date]+$data['no_shopping'][$date];
		}
		
		
		return $data;
	}
	
}