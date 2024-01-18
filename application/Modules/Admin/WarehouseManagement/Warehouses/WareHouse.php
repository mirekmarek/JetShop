<?php
/**
 *
 * @copyright
 * @license
 * @author
 */
namespace JetApplicationModule\Admin\WarehouseManagement\Warehouses;

use JetApplication\Admin_Entity_Common_Interface;
use JetApplication\Admin_Entity_Common_Trait;
use JetApplication\WarehouseManagement_Warehouse;
use JetApplicationModule\Admin\Suppliers\Main;

class WareHouse extends WarehouseManagement_Warehouse implements Admin_Entity_Common_Interface{
	use Admin_Entity_Common_Trait;
	
	public function getEditURL() : string
	{
		return Main::getEditURL( $this->id );
	}

}