<?php
namespace JetShop;
use Jet\DataModel;
use Jet\DataModel_Definition;

use JetApplication\Entity_WithIDAndShopData;
use JetApplication\PropertyGroup_ShopData;
use JetApplication\Shops_Shop;
use JetApplication\Shops;

#[DataModel_Definition(
	name: 'property_group',
	database_table_name: 'property_groups',
)]
abstract class Core_PropertyGroup extends Entity_WithIDAndShopData {
	
	/**
	 * @var PropertyGroup_ShopData[]
	 */
	#[DataModel_Definition(
		type: DataModel::TYPE_DATA_MODEL,
		data_model_class: PropertyGroup_ShopData::class
	)]
	protected array $shop_data = [];
	

	public function getShopData( ?Shops_Shop $shop=null ) : PropertyGroup_ShopData
	{
		return $this->shop_data[$shop ? $shop->getKey() : Shops::getCurrent()->getKey()];
	}
}