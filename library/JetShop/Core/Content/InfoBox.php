<?php
/**
 *
 */

namespace JetShop;

use Jet\DataModel;
use Jet\DataModel_Definition;

use JetApplication\Admin_Managers_ContentInfoBoxes;
use JetApplication\Content_InfoBox_EShopData;
use JetApplication\Entity_Admin_WithEShopData_Interface;
use JetApplication\Entity_Admin_WithEShopData_Trait;
use JetApplication\Entity_WithEShopData;
use JetApplication\EShop;
use JetApplication\Entity_Definition;


#[DataModel_Definition(
	name: 'content_info_box',
	database_table_name: 'content_info_box',
)]
#[Entity_Definition(
	admin_manager_interface: Admin_Managers_ContentInfoBoxes::class,
	separate_tab_form_shop_data: true
)]
abstract class Core_Content_InfoBox extends Entity_WithEShopData implements Entity_Admin_WithEShopData_Interface
{
	use Entity_Admin_WithEShopData_Trait;
	
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