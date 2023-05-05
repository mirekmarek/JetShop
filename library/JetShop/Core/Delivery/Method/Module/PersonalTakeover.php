<?php
/**
 *
 */

namespace JetShop;

use JetApplication\Shops_Shop;
use JetApplication\Delivery_Method_Module;
use JetApplication\Delivery_PersonalTakeover_Place;

abstract class Core_Delivery_Method_Module_PersonalTakeover extends Delivery_Method_Module
{
	/**
	 * @param Shops_Shop $shop
	 *
	 * @return Delivery_PersonalTakeover_Place[]
	 */
	abstract public function getPlacesList( Shops_Shop $shop ) : iterable;
}