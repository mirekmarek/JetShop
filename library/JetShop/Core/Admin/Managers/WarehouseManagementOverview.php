<?php
namespace JetShop;


use JetApplication\Admin_EntityManager_Interface;
use JetApplication\Product;

interface Core_Admin_Managers_WarehouseManagementOverview extends Admin_EntityManager_Interface
{
	public function renderProductStockStatusInfo( Product $product ) : string;
}