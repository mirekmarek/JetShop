<?php
/**
 *
 * @copyright
 * @license
 * @author
 */
namespace JetApplicationModule\Carrier\CustomDeliveryPoints;


use Jet\Config_Definition;
use Jet\Form_Definition_Interface;
use Jet\Form_Definition_Trait;
use JetApplication\ShopConfig_ModuleConfig_PerShop;

#[Config_Definition(
	name: 'CustomDeliveryPoints'
)]
class Config_PerShop extends ShopConfig_ModuleConfig_PerShop implements Form_Definition_Interface {
	use Form_Definition_Trait;
	
	
	
	
}