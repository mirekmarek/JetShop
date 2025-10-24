<?php
/**
 * @copyright Copyright (c) Miroslav Marek <mirek.marek@web-jet.cz>
 * @license EUPL 1.2  https://eupl.eu/1.2/en/
 * @author Miroslav Marek <mirek.marek@web-jet.cz>
 */
namespace JetApplicationModule\Admin\EShopAnalyticsTester;

use Jet\Tr;
use JetApplication\Application_Service_EShop_AnalyticsService;
use JetApplication\EShop;
use JetApplication\Product_EShopData;

abstract class Test {
	protected Application_Service_EShop_AnalyticsService $service;
	protected EShop $eshop;
	protected string $title;
	
	public function __construct( Application_Service_EShop_AnalyticsService $service, EShop $eshop )
	{
		$this->service = $service;
		$this->eshop = $eshop;
	}
	
	public function getTitle(): string
	{
		return Tr::_( $this->title );
	}
	
	abstract public function performTest() : string;
	
	protected function getRandomProduct() : Product_EShopData
	{
		$product_ids = Product_EShopData::dataFetchCol(
			select: ['entity_id'],
			where: [
				Product_EShopData::getActiveQueryWhere($this->eshop),
			]
		);
		shuffle( $product_ids );
		
		return Product_EShopData::get( $product_ids[0], $this->eshop );
	}
	
}