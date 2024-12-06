<?php
namespace JetShop;

use JetApplication\EShopConfig_ModuleConfig_General;

interface Core_EShopConfig_ModuleConfig_ModuleHasConfig_General_Interface
{
	public function getGeneralConfig() : EShopConfig_ModuleConfig_General;
}