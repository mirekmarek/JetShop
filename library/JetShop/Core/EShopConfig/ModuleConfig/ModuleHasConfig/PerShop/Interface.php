<?php
namespace JetShop;

use JetApplication\EShopConfig_ModuleConfig_PerShop;
use JetApplication\EShop;

interface Core_EShopConfig_ModuleConfig_ModuleHasConfig_PerShop_Interface
{
	public function getEshopConfig( EShop $eshop ) : EShopConfig_ModuleConfig_PerShop;
}