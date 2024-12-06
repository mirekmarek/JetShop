<?php
/**
 * 
 */

namespace JetShop;

use Jet\DataModel;
use Jet\DataModel_Definition;

use JetApplication\EMail_Layout_EShopData;
use JetApplication\Entity_WithEShopData;
use JetApplication\EShop;



#[DataModel_Definition(
	name: 'email_layout',
	database_table_name: 'email_layout',
)]
abstract class Core_EMail_Layout extends Entity_WithEShopData
{
	
	
	/**
	 * @var EMail_Layout_EShopData[]
	 */
	#[DataModel_Definition(
		type: DataModel::TYPE_DATA_MODEL,
		data_model_class: EMail_Layout_EShopData::class
	)]
	protected array $eshop_data = [];
	
	
	
	public function getEshopData( ?EShop $eshop = null ): EMail_Layout_EShopData
	{
		/** @noinspection PhpIncompatibleReturnTypeInspection */
		return $this->_getEshopData( $eshop );
	}
}