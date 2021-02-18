<?php
/**
 *
 * @copyright 
 * @license  
 * @author  
 */
namespace JetShopModule\Order\Delivery\PersonalTakeover\InTime;

use JetShop\Delivery_PersonalTakeover_Module;
use JetShop\Delivery_PersonalTakeover_Place;
use JetShop\Shops_Shop;

/**
 *
 */
class Main extends Delivery_PersonalTakeover_Module
{
	protected static string $method_code = 'Order.PersonalTakeover.InTime';


	public function getCurrentPlaces( Shops_Shop $shop ): iterable
	{
		$locale = $shop->getLocale()->toString();

		if($locale!='cs_CZ') {
			return [];
		}


		$data = json_decode(file_get_contents('https://bridge.intime.cz/public/branches/branches.json'), true);

		if(
			!isset($data['intime']) ||
			!isset($data['intime']['branches'])
		) {
			return [];
		}

		$list = [];

		foreach( $data['intime']['branches'] as $item ) {
			$place = new Delivery_PersonalTakeover_Place();


			$place->setShopCode( $shop->getCode() );
			$place->setPlaceCode( $item['code'] );
			$place->setMethodCode( static::$method_code );

			$place->setName( $item['name'] );
			$place->setStreet( $item['address']['street'].' '.$item['address']['number'] );
			$place->setTown( $item['address']['town'] );
			$place->setZip( $item['address']['postal_code'] );

			$place->setLatitude( $item['position']['lat'] );
			$place->setLongitude( $item['position']['lng'] );

			if(isset($item['photo'])) {
				$place->addImage( $item['photo'] );
			}

			$days = [
				'mon' => 'Pondìlí',
				'tue' => 'Úterý',
				'wed' => 'Støeda',
				'thu' => 'Ètvrtek',
				'fri' => 'Pátek',
				'sat' => 'Sobota',
				'sun' => 'Nedìle',
			];

			foreach($days as $day=>$day_name) {
				if(!isset($item['opening_hours'][$day])) {
					continue;
				}

				$oh = $item['opening_hours'][$day];

				$place->addOpeningHours( $day_name, $oh['from'], $oh['to'] );
			}

			$list[] = $place;
		}


		return $list;
	}
}