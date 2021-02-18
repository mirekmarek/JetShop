<?php
/**
 *
 */

namespace JetShop;

use Jet\Application_Modules;

abstract class Core_Delivery_PersonalTakeover {

	protected static string $module_name_prefix = 'Order.Delivery.PersonalTakeover.';

	public static function getModuleNamePrefix(): string
	{
		return self::$module_name_prefix;
	}

	public static function setModuleNamePrefix( string $module_name_prefix ): void
	{
		self::$module_name_prefix = $module_name_prefix;
	}

	public static function actualizePlaces( Shops_Shop $shop, bool $verbose=false ) : bool
	{
		$past_list = Delivery_PersonalTakeover_Place::getListForShop(
			shop_code: $shop->getCode(),
			only_active: false
		);

		$updated = false;
		$future_list = [];
		foreach( static::getActiveModules() as $module ) {
			if($verbose) {
				echo "\t".$module->getModuleManifest()->getName()."\n";
			}

			foreach($module->getCurrentPlaces( $shop ) as $place) {
				$future_list[$place->getKey()] = $place;
			}
		}

		if(!$future_list) {
			return false;
		}

		/**
		 * @var Delivery_PersonalTakeover_Place $past_place
		 * @var Delivery_PersonalTakeover_Place $future_place
		 */

		foreach( $future_list as $k=>$future_place ) {

			if(!isset($past_list[$k])) {
				if($verbose) {
					echo "\t\t {$k} - adding\n";
				}
				$updated = true;
				$future_place->save();
				continue;
			}


			$past_place = $past_list[$k];
			$future_place = $future_list[$k];

			if($future_place->getHash()!=$past_place->getHash()) {
				echo "\t\t {$k} - updating\n";

				$updated = true;
				$past_place->delete();
				$future_place->save();
			}
		}

		foreach($past_list as $k=>$past_place) {
			if(
				$past_place->isActive() &&
				!isset($future_list[$k])
			) {
				echo "\t\t {$k} - deactivating\n";

				$updated = true;
				$past_place->setIsActive( false );
				$past_place->save();
			}

		}

		return $updated;
	}

	/**
	 * @return Delivery_PersonalTakeover_Module[]
	 */
	public static function getActiveModules() : iterable
	{
		$modules = [];

		$name_prefix = static::getModuleNamePrefix();

		foreach(Application_Modules::activatedModulesList() as $manifest) {
			if( str_starts_with( $manifest->getName(), $name_prefix ) ) {
				$modules[$manifest->getName()] = Application_Modules::moduleInstance( $manifest->getName() );
			}
		}

		return $modules;
	}
}