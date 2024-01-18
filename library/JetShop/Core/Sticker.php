<?php
namespace JetShop;

use Jet\DataModel;
use Jet\DataModel_Definition;
use Jet\Form_Definition;

use JetApplication\Entity_WithShopData;
use JetApplication\Sticker_ShopData;
use JetApplication\Shops;
use JetApplication\Shops_Shop;

#[DataModel_Definition(
	name: 'stickers',
	database_table_name: 'stickers',
)]
abstract class Core_Sticker extends Entity_WithShopData {


	#[DataModel_Definition(
		type: DataModel::TYPE_DATA_MODEL,
		data_model_class: Sticker_ShopData::class
	)]
	#[Form_Definition(is_sub_forms: true)]
	protected array $shop_data = [];

	
	public function getShopData( ?Shops_Shop $shop=null ) : Sticker_ShopData
	{
		return $this->shop_data[$shop ? $shop->getKey() : Shops::getCurrent()->getKey()];
	}
}