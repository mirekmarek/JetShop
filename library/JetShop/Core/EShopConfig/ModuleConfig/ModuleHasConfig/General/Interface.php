<?php
/**
 * @copyright Copyright (c) Miroslav Marek <mirek.marek@web-jet.cz>
 * @license EUPL 1.2  https://eupl.eu/1.2/en/
 * @author Miroslav Marek <mirek.marek@web-jet.cz>
 */
namespace JetShop;


use JetApplication\EShopConfig_ModuleConfig_General;

interface Core_EShopConfig_ModuleConfig_ModuleHasConfig_General_Interface
{
	public function getGeneralConfig() : EShopConfig_ModuleConfig_General;
}