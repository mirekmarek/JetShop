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
	protected bool $is_default = false;
	protected array $sub_reports = [
		'summary' => 'Summary',
		'chart' => 'Chart',
		'details_per_day' => 'Numbers per Day',
		'customer_club_vs_nonclub_pie_chart' => 'Customer club / non-club - pie chart',
		'customer_club_vs_nonclub_graph' => 'Customer club / non-club - graph',
		'customer_club_vs_nonclub_table' => 'Customer club / non-club - table',
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
	
	protected function _prepare_customer_club_vs_nonclub( ?array &$club, ?array &$non_club ) : void
	{
		$club = Order::dataFetchAll(
			select: [
				'id',
				'eshop_code',
				'locale',
				'date_purchased',
			],
			where: [
				[
					'date_purchased >=' => $this->date_from,
					'AND',
					'date_purchased <=' => $this->date_to,
				],
				'AND',
				[
					'customer_id >' => 0,
					'AND',
					'newsletter_accepted' => true
				]
			],
			raw_mode: true
		);
		$club = $this->prepareData_PerShop_PerDay_Count( $club, 'date_purchased' );
		
		$non_club = Order::dataFetchAll(
			select: [
				'id',
				'eshop_code',
				'locale',
				'date_purchased',
			],
			where: [
				[
					'date_purchased >=' => $this->date_from,
					'AND',
					'date_purchased <=' => $this->date_to,
				],
				'AND',
				[
					'customer_id' => 0,
					'OR',
					'newsletter_accepted' => false
				]
			],
			raw_mode: true
		);
		
		$non_club = $this->prepareData_PerShop_PerDay_Count( $non_club, 'date_purchased' );
		
		
	}
	
	public function prepare_customer_club_vs_nonclub_pie_chart() : void
	{
		$this->_prepare_customer_club_vs_nonclub( $club, $non_club );
		$this->view->setVar('data_club', $club);
		$this->view->setVar('data_non_club', $non_club);
	}
	
	public function prepare_customer_club_vs_nonclub_table() : void
	{
		$this->_prepare_customer_club_vs_nonclub( $club, $non_club );
		$this->view->setVar('data_club', $club);
		$this->view->setVar('data_non_club', $non_club);
	}
	
	
	public function prepare_customer_club_vs_nonclub_graph() : void
	{
		$this->_prepare_customer_club_vs_nonclub( $club, $non_club );
		
		$data = [];
		
		$data['total'] = $club['total'];
		
		foreach($non_club['total'] as $day=>$v) {
			$data['total'][$day] += $v;
		}
		
		foreach($club as $k=>$v) {
			$data[$k.'|Club'] = $v;
		}
		foreach($non_club as $k=>$v) {
			$data[$k.'|Non-club'] = $v;
		}
		
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
		
		
		return $this->prepareData_PerShop_PerDay_Count( $data, 'date_purchased' );
	}
	
}