<?php
namespace JetShop;

use Jet\DataModel;
use Jet\DataModel_Definition;
use JetApplication\Brand_ShopData;
use JetApplication\Entity_WithShopData;
use JetApplication\Shops;
use JetApplication\Shops_Shop;

#[DataModel_Definition(
	name: 'brands',
	database_table_name: 'brands',
)]
abstract class Core_Brand extends Entity_WithShopData {
	
	/**
	 * @var Brand_ShopData[]
	 */
	#[DataModel_Definition(
		type: DataModel::TYPE_DATA_MODEL,
		data_model_class: Brand_ShopData::class
	)]
	protected array $shop_data = [];
	
	public function getShopData( ?Shops_Shop $shop=null ) : Brand_ShopData
	{
		return $this->shop_data[$shop ? $shop->getKey() : Shops::getCurrent()->getKey()];
	}
	
}