<?php
/**
 * @copyright Copyright (c) Miroslav Marek <mirek.marek@web-jet.cz>
 * @license EUPL 1.2  https://eupl.eu/1.2/en/
 * @author Miroslav Marek <mirek.marek@web-jet.cz>
 */
namespace JetApplicationModule\EShop\Analytics\Service\JetAnalytics;

class Report_General_Sessions extends Report_General
{
	public const KEY = 'sessions';
	protected ?string $title = 'Sessions';
	protected bool $is_default = true;
	protected array $sub_reports = [
		'summary' => 'Summary',
		'chart' => 'Chart',
		'details_per_day' => 'Numbers per Day',
		'customer_logged_in' => 'Customer logged in / not logged in',
		'purchased' => 'Purchased / not purchased',
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

	public function prepare_customer_logged_in() : void
	{

		$logged_in = Session::dataFetchAll(
			select: [
				'id',
				'eshop_code',
				'locale',
				'start_date_time',
			],
			where: [
				[
					'start_date_time >=' => $this->date_from,
					'AND',
					'start_date_time <=' => $this->date_to,
				],
				'AND',
				[
					'start_date_time < ' => Session::getDataModelDefinition()->getProperty('last_activity_date_time'),
				],
				'AND',
				'customer_id >' => 0
			],
			raw_mode: true
		);
		$logged_in = $this->prepareDataPerShopPerDay( $logged_in, $this->selected_eshop_keys, 'start_date_time' );

		
		$not_logged_in = Session::dataFetchAll(
			select: [
				'id',
				'eshop_code',
				'locale',
				'start_date_time',
			],
			where: [
				[
					'start_date_time >=' => $this->date_from,
					'AND',
					'start_date_time <=' => $this->date_to,
				],
				'AND',
				'customer_id' => 0
			],
			raw_mode: true
		);
		
		
		$not_logged_in = $this->prepareDataPerShopPerDay( $not_logged_in, $this->selected_eshop_keys, 'start_date_time' );
		
		
		
		
		$this->view->setVar('data_logged_in', $logged_in);
		$this->view->setVar('data_not_logged_in', $not_logged_in);
		
	}
	
	public function prepare_purchased() : void
	{
		$purchased = Session::dataFetchAll(
			select: [
				'id',
				'eshop_code',
				'locale',
				'start_date_time',
			],
			where: [
				[
					'start_date_time >=' => $this->date_from,
					'AND',
					'start_date_time <=' => $this->date_to,
				],
				'AND',
				'purchased' => true
			],
			raw_mode: true
		);
		$purchased = $this->prepareDataPerShopPerDay( $purchased, $this->selected_eshop_keys, 'start_date_time' );
		
		
		$not_purchased = Session::dataFetchAll(
			select: [
				'id',
				'eshop_code',
				'locale',
				'start_date_time',
			],
			where: [
				[
					'start_date_time >=' => $this->date_from,
					'AND',
					'start_date_time <=' => $this->date_to,
				],
				'AND',
				'purchased' => false
			],
			raw_mode: true
		);
		
		
		$not_purchased = $this->prepareDataPerShopPerDay( $not_purchased, $this->selected_eshop_keys, 'start_date_time' );
		
		
		$this->view->setVar('data_purchased', $purchased);
		$this->view->setVar('data_not_purchased', $not_purchased);
	
	}
	
	
	protected function getRawData() : array
	{
		
		$data = Session::dataFetchAll(
			select: [
				'id',
				'eshop_code',
				'locale',
				'start_date_time',
				/*
				'customer_id',
				'purchased'
				*/
			],
			where: [
				[
					'start_date_time >=' => $this->date_from,
					'AND',
					'start_date_time <=' => $this->date_to,
				]
			],
			raw_mode: true
		);
		
		
		return $this->prepareDataPerShopPerDay( $data, $this->selected_eshop_keys, 'start_date_time' );
	}
	
}