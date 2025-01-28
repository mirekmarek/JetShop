<?php
/**
 *
 * @copyright 
 * @license  
 * @author  
 */
namespace JetApplicationModule\Admin\WarehouseManagement\Warehouses;

use JetApplication\Admin_EntityManager_Controller;

class Controller_Main extends Admin_EntityManager_Controller
{
	public function getEntityNameReadable() : string
	{
		return 'Warehouse Management - Warehouse';
	}
	
}