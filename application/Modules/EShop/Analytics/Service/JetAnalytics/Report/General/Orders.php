<?php
/**
 * @copyright Copyright (c) Miroslav Marek <mirek.marek@web-jet.cz>
 * @license EUPL 1.2  https://eupl.eu/1.2/en/
 * @author Miroslav Marek <mirek.marek@web-jet.cz>
 */
namespace JetApplicationModule\EShop\Analytics\Service\JetAnalytics;

use JetApplication\Order;

class Report_General_Orders extends Report_General
{
	public const KEY = 'orders';
	protected ?string $title = 'Orders';
	protected bool $is_default = true;
	protected array $sub_reports = [
		'summary' => 'Summary',
		'chart' => 'Chart',
		'details_per_day' => 'Numbers per Day',
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
	
	public function prepare_details_per_day() : void
	{
		$data = $this->getRawData();
		$this->view->setVar('data', $data);
	}
	
	protected function getRawData() : array
	{
		
		$data = Order::dataFetchAll(
			select: [
				'id',
				'eshop_code',
				'locale',
				'date_purchased',
				/*
				'customer_id',
				'purchased'
				*/
			],
			where: [
				[
					'date_purchased >=' => $this->date_from,
					'AND',
					'date_purchased <=' => $this->date_to,
				]
			],
			raw_mode: true
		);
		
		
		return $this->prepareDataPerShopPerDay( $data, $this->selected_eshop_keys, 'date_purchased' );
	}
	
}