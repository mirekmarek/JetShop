<?php
/**
 * @copyright Copyright (c) Miroslav Marek <mirek.marek@web-jet.cz>
 * @license EUPL 1.2  https://eupl.eu/1.2/en/
 * @author Miroslav Marek <mirek.marek@web-jet.cz>
 */
namespace JetApplicationModule\EShop\Analytics\Service\JetAnalytics;

class Report_Product_Views extends Report_Product
{
	public const KEY = 'views';
	protected ?string $title = 'Views';
	protected bool $is_default = true;
	protected array $sub_reports = [
		'summary' => 'Summary',
		'chart' => 'Chart',
		'details_per_day' => 'Details Per Day',
	];
	
	public function prepare_summary() : void
	{
		$data = $this->getRawData();
		//var_dump($data);
		//TODO:
	}
	
	public function prepare_chart() : void
	{
		$this->handleSelectedEShopKeys();
		
		$data = $this->getRawData();
		
		$this->view->setVar('data', $data);
	}
	
	public function prepare_details_per_day() : void
	{
		$data = $this->getRawData();
		//TODO:
		
	}
	
	
	protected function getRawData() : array
	{
		
		$data = Event_ProductDetailView::dataFetchAll(
			select: [
				'eshop_code',
				'locale',
				'date_time'
			],
			where: [
				'product_id' => $this->product->getId(),
				'AND',
				[
					'date_time >=' => $this->date_from,
					'AND',
					'date_time <=' => $this->date_to,
				]
			],
			raw_mode: true
		);
		
		
		return $this->prepareDataPerShopPerDay( $data, $this->selected_eshop_keys );
	}
	
}