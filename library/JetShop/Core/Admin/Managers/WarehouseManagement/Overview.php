<?php
namespace JetShop;


use JetApplication\Admin_EntityManager_Interface;
use JetApplication\Product;

interface Core_Admin_Managers_WarehouseManagement_Overview extends Admin_EntityManager_Interface
{
	public function renderProductStockStatusInfo( Product $product ) : string;
}