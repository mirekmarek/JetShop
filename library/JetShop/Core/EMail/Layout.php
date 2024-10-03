<?php
/**
 * 
 */

namespace JetShop;

use Jet\DataModel;
use Jet\DataModel_Definition;

use JetApplication\EMail_Layout_ShopData;
use JetApplication\Entity_WithShopData;
use JetApplication\Shops_Shop;



#[DataModel_Definition(
	name: 'email_layout',
	database_table_name: 'email_layout',
)]
abstract class Core_EMail_Layout extends Entity_WithShopData
{
	
	
	/**
	 * @var EMail_Layout_ShopData[]
	 */
	#[DataModel_Definition(
		type: DataModel::TYPE_DATA_MODEL,
		data_model_class: EMail_Layout_ShopData::class
	)]
	protected array $shop_data = [];
	
	
	
	public function getShopData( ?Shops_Shop $shop = null ): EMail_Layout_ShopData
	{
		/** @noinspection PhpIncompatibleReturnTypeInspection */
		return $this->_getShopData( $shop );
	}
}