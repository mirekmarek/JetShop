<?php
/**
 * 
 */

namespace JetShop;

use Jet\DataModel;
use Jet\DataModel_Definition;


use JetApplication\Entity_WithShopRelation;

#[DataModel_Definition(
	name: 'warehouse_shop',
	database_table_name: 'whm_warehouses_shops'
)]
class Core_WarehouseManagement_Warehouse_Shop extends Entity_WithShopRelation
{
	
	#[DataModel_Definition(
		type: DataModel::TYPE_INT,
		is_key: true,
	)]
	protected int $warehouse_id = 0;

	
	public function setWarehouseId( int $value ) : void
	{
		$this->warehouse_id = $value;
	}
	
	public function getWarehouseId() : string
	{
		return $this->warehouse_id;
	}
}
