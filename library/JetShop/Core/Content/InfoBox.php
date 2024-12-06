<?php
/**
 *
 */

namespace JetShop;

use Jet\DataModel;
use Jet\DataModel_Definition;

use JetApplication\Content_InfoBox_EShopData;
use JetApplication\Entity_WithEShopData;
use JetApplication\EShop;



#[DataModel_Definition(
	name: 'content_info_box',
	database_table_name: 'content_info_box',
)]
abstract class Core_Content_InfoBox extends Entity_WithEShopData
{
	/**
	 * @var Content_InfoBox_EShopData[]
	 */
	#[DataModel_Definition(
		type: DataModel::TYPE_DATA_MODEL,
		data_model_class: Content_InfoBox_EShopData::class
	)]
	protected array $eshop_data = [];
	
	
	
	public function getEshopData( ?EShop $eshop = null ): Content_InfoBox_EShopData
	{
		/** @noinspection PhpIncompatibleReturnTypeInspection */
		return $this->_getEshopData( $eshop );
	}
	
}