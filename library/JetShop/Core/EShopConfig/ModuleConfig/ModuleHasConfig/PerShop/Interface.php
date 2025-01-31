<?php
/**
 * @copyright Copyright (c) Miroslav Marek <mirek.marek@web-jet.cz>
 * @license EUPL 1.2  https://eupl.eu/1.2/en/
 * @author Miroslav Marek <mirek.marek@web-jet.cz>
 */
namespace JetShop;


use JetApplication\EShopConfig_ModuleConfig_PerShop;
use JetApplication\EShop;

interface Core_EShopConfig_ModuleConfig_ModuleHasConfig_PerShop_Interface
{
	public function getEshopConfig( EShop $eshop ) : EShopConfig_ModuleConfig_PerShop;
}