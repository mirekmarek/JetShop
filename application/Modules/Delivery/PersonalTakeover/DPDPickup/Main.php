<?php
/**
 *
 * @copyright 
 * @license  
 * @author  
 */
namespace JetApplicationModule\Delivery\PersonalTakeover\DPDPickup;

use JetApplication\Delivery_Method_Module_PersonalTakeover;
use JetApplication\Delivery_Method_ShopData;
use JetApplication\Delivery_PersonalTakeover_Place;

/**
 *
 */
class Main extends Delivery_Method_Module_PersonalTakeover
{
	protected static array $countries_map = [
		'cs_CZ' => 203
	];
	
	public function getPlacesList( Delivery_Method_ShopData $method ): iterable
	{
		$shop = $method->getShop();
		$locale = $shop->getLocale()->toString();

		if(!isset(static::$countries_map[$locale])) {
			return [];
		}

		$country_id = static::$countries_map[$locale];



		$data = json_decode( file_get_contents('https://pickup.dpd.cz/api/get-all?country='.$country_id), true );

		if(
			!isset($data['status']) ||
			$data['status']!='ok' &&
			!isset($data['data']['items'])
		) {
			return [];
		}

		$list = [];

		foreach( $data['data']['items'] as $item ) {
			$place = new Delivery_PersonalTakeover_Place();

			$place->setShop( $shop );
			$place->setMethodId( $method->getId() );
			
			$place->setPlaceCode( $item['id'] );

			$place->setName( $item['company'] );
			$place->setStreet( $item['street'].' '.$item['house_number'] );
			$place->setTown( $item['city'] );
			$place->setZip( $item['postcode'] );

			$place->setLatitude( $item['latitude'] );
			$place->setLongitude( $item['longitude'] );
			
			$place->setCountry( $method->getShop()->getLocale()->getRegion() );

			$place->addImage( $item['photo'] );

			foreach($item['hours'] as $d) {
				$place->addOpeningHours(
					$d['dayName'],
					$d['openMorning'],
					$d['closeMorning'],
					$d['openAfternoon'],
					$d['closeAfternoon']
				);
			}


			$list[] = $place;
		}


		return $list;
	}
}