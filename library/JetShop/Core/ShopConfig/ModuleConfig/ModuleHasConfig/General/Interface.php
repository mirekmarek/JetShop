<?php
namespace JetShop;

use JetApplication\ShopConfig_ModuleConfig_General;

interface Core_ShopConfig_ModuleConfig_ModuleHasConfig_General_Interface
{
	public function getGeneralConfig() : ShopConfig_ModuleConfig_General;
}