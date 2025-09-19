<?php
/**
 * @copyright Copyright (c) Miroslav Marek <mirek.marek@web-jet.cz>
 * @license EUPL 1.2  https://eupl.eu/1.2/en/
 * @author Miroslav Marek <mirek.marek@web-jet.cz>
 */
namespace JetApplicationModule\Carrier\Ulozenka;


use Jet\Exception;
use Jet\Locale;
use JetApplication\Carrier_DeliveryPoint;
use JetApplication\EShops;

class Client
{
	protected Main $carrier;
	
	public function __construct( Main $carrier )
	{
		$this->carrier = $carrier;
	}
	
	
	
	/**
	 * @return Carrier_DeliveryPoint[]
	 * @throws Exception
	 */
	public function downloadUpToDateDeliveryPointsList(): array
	{
		$res = [];
		
		foreach( EShops::getList() as $eshop ) {
			$URL = $this->carrier->getConfig( $eshop )->getPlaceslistAPIURL();
			if( $URL ) {
				foreach( $this->download( $eshop->getLocale(), $URL ) as $item ) {
					$res[$item->getKey()] = $item;
				}
			}
			
		}
		
		return $res;
	}

	
	
	protected function download( Locale $locale, string $JSON_URL ): array
	{
		$list = [];
		$data = json_decode(file_get_contents($JSON_URL), true);
		
		foreach($data['data'] as $item) {
			$id = $item['id'];
			$name = $item['name'];
			
			$detail = json_decode( file_get_contents('https://api.ulozenka.cz/v3/branches/'.$id), true );
			$item = $detail['data'][0]??null;

			if(!$item) {
				continue;
			}
			
			if(
				$locale->getRegion()=='CZ' &&
				$item['country']!='CZE'
			) {
				continue;
			}
			
			if(
				$locale->getRegion()=='SK' &&
				$item['country']!='SVK'
			) {
				continue;
			}

			
			var_dump($id, $item);
			
			die();
		}
		
		die();
		/*
		var_dump($JSON_URL, $data);
		die();
		
		$import = function( $data ) use ( &$list, $locale ) {
			foreach( $data as $item ) {
				
				
				$place = new Carrier_DeliveryPoint();
				$place->setPointLocale( $locale );
				$place->setCarrier( $this->carrier );
				
				$place->setPointCode( $item['id'] );
				$place->setName( $item['name'] );
				$place->setStreet( $item['street'].' '.$item['house_number'] );
				$place->setTown( $item['town'] );
				$place->setZip( $item['zip'] );
				
				
				if(!empty($item['_links']['picture']['href'])) {
					$place->addImage($item['_links']['picture']['href']);
				}
				
				$place->setLatitude( $item['gps']['latitude'] );
				$place->setLongitude( $item['gps']['longitude'] );
				
				
				$days = [
					'monday',
					'tuesday',
					'wednesday',
					'thursday',
					'friday',
					'saturday',
					'sunday',
				];
				
				
				foreach($days as $day ) {
					if(!isset($item['opening_hours']['regular'][$day])) {
						continue;
					}
					
					$oh = $item['opening_hours']['regular'][$day];
					if(
						!$oh ||
						!isset($oh['hours']) ||
						!isset($oh['hours'][0])
					) {
						continue;
					}
					
					$oh = $oh['hours'][0];
					
					$place->addOpeningHours( $day, $oh['open'], $oh['close']);
				}
				
				$list[$place->getPointCode()] = $place;
			}
			
			
		};
		
		
		$import( $data['data']['destination'] );
		$import( $data['data']['register'] );
		*/
		
		return $list;
		
	}
	
	
}