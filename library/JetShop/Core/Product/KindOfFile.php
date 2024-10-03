<?php
/**
 *
 */

namespace JetShop;

use Jet\DataModel;
use Jet\DataModel_Definition;

use JetApplication\Product_KindOfFile_ShopData;
use JetApplication\Entity_WithShopData;
use JetApplication\Shops_Shop;

#[DataModel_Definition(
	name: 'products_kind_of_file',
	database_table_name: 'products_kind_of_file',
)]
abstract class Core_Product_KindOfFile extends Entity_WithShopData
{
	
	
	/**
	 * @var Product_KindOfFile_ShopData[]
	 */
	#[DataModel_Definition(
		type: DataModel::TYPE_DATA_MODEL,
		data_model_class: Product_KindOfFile_ShopData::class
	)]
	protected array $shop_data = [];
	
	

	
	public function getShopData( ?Shops_Shop $shop=null ) : Product_KindOfFile_ShopData
	{
		/** @noinspection PhpIncompatibleReturnTypeInspection */
		return $this->_getShopData( $shop );
	}
}
