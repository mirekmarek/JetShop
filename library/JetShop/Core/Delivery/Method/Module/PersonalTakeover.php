<?php
/**
 *
 */

namespace JetShop;

use JetApplication\Delivery_Method_ShopData;
use JetApplication\Delivery_Method_Module;
use JetApplication\Delivery_PersonalTakeover_Place;

abstract class Core_Delivery_Method_Module_PersonalTakeover extends Delivery_Method_Module
{
	/**
	 * @param Delivery_Method_ShopData $method
	 *
	 * @return Delivery_PersonalTakeover_Place[]
	 */
	abstract public function getPlacesList( Delivery_Method_ShopData $method ) : iterable;
	
	public function actualizePlaces( Delivery_Method_ShopData $method, bool $verbose=false ) : void
	{
		if($verbose) {
			$log = function( string $message ) {
				echo $message;
			};
		} else {
			$log = function( string $message ) {};
		}
		
		$log("\t".$method->getTitle().': '.$this->getModuleManifest()->getName()."\n");
		$log("\tgetting current list from API ...\n");
		
		
		$future_list = [];
		foreach( $this->getPlacesList( $method ) as $place) {
			$future_list[$place->generateHash()] = $place;
		}
		
		$log("\t\tDONE\n");

		
		if(!$future_list) {
			$log("\tlist is empty - skipping\n");
			return;
		}
		

		$log("\t\treading current map ...\n");
		
		$current_map = $method->getPersonalTakeoverPlaceHashMap();
		
		$log( "\t\tDONE\n" );
		
		$log("\t\tdeleting ...\n");
		foreach($current_map as $hash=>$id) {
			if(!isset($future_list[$hash])) {
				$place = Delivery_PersonalTakeover_Place::load( (int)$id );
				$place?->delete();
			}
		}
		$log( "\t\tDONE\n" );
		
		$log("\t\tadding ...\n");
		foreach($future_list as $hash=>$place) {
			if(!isset($current_map[$hash])) {
				$log( "\t\t {$place->getKey()} - adding\n" );
				$place->save();
			}
		}
		$log( "\t\tDONE\n" );
		
		
	}
}