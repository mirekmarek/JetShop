<?php
namespace JetShop;

use JetApplication\ShopConfig_ModuleConfig_PerShop;
use JetApplication\Shops_Shop;

interface Core_ShopConfig_ModuleConfig_ModuleHasConfig_PerShop_Interface
{
	public function getShopConfig( Shops_Shop $shop ) : ShopConfig_ModuleConfig_PerShop;
}