<?php
/**
 *
 */

namespace JetShop;

use Jet\Application_Module;

abstract class Core_Delivery_PersonalTakeover_Module extends Application_Module
{
	/**
	 * @param Shops_Shop $shop
	 *
	 * @return Delivery_PersonalTakeover_Place[]
	 */
	abstract public function getCurrentPlaces( Shops_Shop $shop ) : iterable;
}