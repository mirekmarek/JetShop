<?php
/**
 *
 * @copyright 
 * @license  
 * @author  
 */
namespace JetApplicationModule\Delivery\PersonalTakeover\InTime;

use JetApplication\Delivery_Method_Module_PersonalTakeover;
use JetApplication\Delivery_Method_ShopData;
use JetApplication\Delivery_PersonalTakeover_Place;

/**
 *
 */
class Main extends Delivery_Method_Module_PersonalTakeover
{
	public function getPlacesList( Delivery_Method_ShopData $method ): iterable
	{
		$shop = $method->getShop();
		
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


			$place->setShop( $shop );
			$place->setMethodId( $method->getId() );
			
			$place->setPlaceCode( $item['code'] );

			$place->setName( $item['name'] );
			$place->setStreet( $item['address']['street'].' '.$item['address']['number'] );
			$place->setTown( $item['address']['town'] );
			$place->setZip( $item['address']['postal_code'] );

			$place->setLatitude( $item['position']['lat'] );
			$place->setLongitude( $item['position']['lng'] );
			
			$place->setCountry( $method->getShop()->getLocale()->getRegion() );

			if(isset($item['photo'])) {
				$place->addImage( $item['photo'] );
			}

			$days = [
				'mon' => 'Pondělí',
				'tue' => 'Úterý',
				'wed' => 'Středa',
				'thu' => 'Čtvrtek',
				'fri' => 'Pátek',
				'sat' => 'Sobota',
				'sun' => 'Neděle',
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