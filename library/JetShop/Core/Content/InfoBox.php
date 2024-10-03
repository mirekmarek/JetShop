<?php
/**
 *
 */

namespace JetShop;

use Jet\DataModel;
use Jet\DataModel_Definition;

use JetApplication\Content_InfoBox_ShopData;
use JetApplication\Entity_WithShopData;
use JetApplication\Shops_Shop;



#[DataModel_Definition(
	name: 'content_info_box',
	database_table_name: 'content_info_box',
)]
abstract class Core_Content_InfoBox extends Entity_WithShopData
{
	/**
	 * @var Content_InfoBox_ShopData[]
	 */
	#[DataModel_Definition(
		type: DataModel::TYPE_DATA_MODEL,
		data_model_class: Content_InfoBox_ShopData::class
	)]
	protected array $shop_data = [];
	
	
	
	public function getShopData( ?Shops_Shop $shop = null ): Content_InfoBox_ShopData
	{
		/** @noinspection PhpIncompatibleReturnTypeInspection */
		return $this->_getShopData( $shop );
	}
	
}