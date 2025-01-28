<?php
/**
 * 
 */

namespace JetShop;

use Jet\DataModel;
use Jet\DataModel_Definition;

use JetApplication\Admin_Managers_ContentEMailLayouts;
use JetApplication\EMail_Layout_EShopData;
use JetApplication\Entity_Admin_WithEShopData_Interface;
use JetApplication\Entity_Admin_WithEShopData_Trait;
use JetApplication\Entity_WithEShopData;
use JetApplication\EShop;
use JetApplication\Entity_Definition;


#[DataModel_Definition(
	name: 'email_layout',
	database_table_name: 'email_layout',
)]
#[Entity_Definition(
	admin_manager_interface: Admin_Managers_ContentEMailLayouts::class
)]
abstract class Core_EMail_Layout extends Entity_WithEShopData implements Entity_Admin_WithEShopData_Interface
{
	use Entity_Admin_WithEShopData_Trait;
	
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