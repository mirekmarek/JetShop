<?php
/**
 * @copyright Copyright (c) Miroslav Marek <mirek.marek@web-jet.cz>
 * @license EUPL 1.2  https://eupl.eu/1.2/en/
 * @author Miroslav Marek <mirek.marek@web-jet.cz>
 */
namespace JetApplicationModule\EShop\Analytics\Service\JetAnalytics;

use JetApplication\Product;

class Report_KindOfProduct_Views extends Report_KindOfProduct
{
	public const KEY = 'views';
	protected ?string $title = 'Views';
	protected bool $is_default = true;
	protected array $sub_reports = [
		'summary' => 'Summary',
		'chart' => 'Chart',
		'details_per_day' => 'Details Per Day',
	];
	
	protected array $selected_eshop_keys = [];
	
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
		$product_ids = Product::dataFetchCol(
			select: ['id'],
			where: ['kind_id' => $this->kind_of_product->getId()],
			raw_mode: true
		);
		
		if(!$product_ids) {
			$product_ids = [0];
		}
		
		$data = Event_ProductDetailView::dataFetchAll(
			select: [
				'eshop_code',
				'locale',
				'date_time',
				'product_id'
			],
			where: [
				'product_id' => $product_ids,
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