<?php
/**
 *
 * @copyright 
 * @license  
 * @author  
 */
namespace JetShopModule\Order\Delivery\Methods\PersonalTakeover\InTime;

use JetShop\Delivery_Method_Module_PersonalTakeover;
use JetShop\Delivery_PersonalTakeover_Place;
use JetShop\Shops_Shop;

/**
 *
 */
class Main extends Delivery_Method_Module_PersonalTakeover
{
	protected static string $method_code = 'PersonalTakeover.InTime';


	public function getPlacesList( Shops_Shop $shop ): iterable
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
				'mon' => 'Pond�l�',
				'tue' => '�ter�',
				'wed' => 'St�eda',
				'thu' => '�tvrtek',
				'fri' => 'P�tek',
				'sat' => 'Sobota',
				'sun' => 'Ned�le',
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