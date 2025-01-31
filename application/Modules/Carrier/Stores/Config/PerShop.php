<?php
/**
 * @copyright Copyright (c) Miroslav Marek <mirek.marek@web-jet.cz>
 * @license EUPL 1.2  https://eupl.eu/1.2/en/
 * @author Miroslav Marek <mirek.marek@web-jet.cz>
 */
namespace JetApplicaTionModule\Carrier\Stores;



use Jet\Config_Definition;
use Jet\Form_Definition_Interface;
use Jet\Form_Definition_Trait;
use JetApplication\EShopConfig_ModuleConfig_PerShop;

#[Config_Definition(
	name: 'Stores'
)]
class Config_PerShop extends EShopConfig_ModuleConfig_PerShop implements Form_Definition_Interface {
	use Form_Definition_Trait;
	
	
	
	
}