<?php
/**
 * 
 */

namespace JetShop;

use Jet\DataModel_Definition;
use Jet\DataModel_IDController_Passive;
use Jet\DataModel_Related_1toN;
use Jet\Form_Definition;
use Jet\Form_Field;

use JetApplication\WarehouseManagement_Warehouse;
use JetApplication\CommonEntity_ShopRelationTrait_ShopIsId;

/**
 *
 */
#[DataModel_Definition(
	name: 'warehouse_shop',
	database_table_name: 'whm_warehouses_shops',
	parent_model_class: WarehouseManagement_Warehouse::class,
	id_controller_class: DataModel_IDController_Passive::class
)]
class Core_WarehouseManagement_Warehouse_Shop extends DataModel_Related_1toN
{
	use CommonEntity_ShopRelationTrait_ShopIsId;

	/**
	 * @var string
	 */ 
	#[DataModel_Definition(
		related_to: 'main.code',
		is_key: true,
	)]
	#[Form_Definition(
		type: Form_Field::TYPE_HIDDEN
	)]
	protected string $warehouse_code = '';

	public function getArrayKeyValue(): string
	{
		return $this->getShop()->getKey();
	}


	/**
	 * @param string $value
	 */
	public function setWarehouseCode( string $value ) : void
	{
		$this->warehouse_code = $value;
		
		if( $this->getIsSaved() ) {
			$this->setIsNew();
		}
		
	}

	/**
	 * @return string
	 */
	public function getWarehouseCode() : string
	{
		return $this->warehouse_code;
	}
}
