<?php
namespace JetShop;


use JetApplication\Product;

interface Core_Admin_Managers_WarehouseManagementOverview
{
	public function renderProductStockStatusInfo( Product $product ) : string;
}