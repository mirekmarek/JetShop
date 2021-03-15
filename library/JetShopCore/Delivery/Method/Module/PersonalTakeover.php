<?php
/**
 *
 */

namespace JetShop;

abstract class Core_Delivery_Method_Module_PersonalTakeover extends Delivery_Method_Module
{
	/**
	 * @param Shops_Shop $shop
	 *
	 * @return Delivery_PersonalTakeover_Place[]
	 */
	abstract public function getPlacesList( Shops_Shop $shop ) : iterable;
}