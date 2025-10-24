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
		$data = json_decode(file_get_contents($JSON_URL), true);
		
		if(!is_array($data['intime']['branches'])) {
			return [];
		}
		
		
		$list = [];
		foreach( $data['intime']['branches'] as $item ) {
			if($item['address']['country']!=$locale->getRegion()) {
				continue;
			}
			
			$place = new Carrier_DeliveryPoint();
			$place->setPointLocale( $locale );
			$place->setCarrier( $this->carrier );
			
			$place->setPointType( $item['branch_type'] );
			
			$place->setPointCode( $item['code'] );
			$place->setName( $item['name'] );
			$place->setStreet( $item['address']['street'].' '.$item['address']['number'] );
			$place->setTown( $item['address']['town'] );
			$place->setCountry( $item['address']['country'] );
			$place->setZip( str_replace(' ', '', $item['address']['postal_code']) );
			
			
			$place->addImage($item['photo']);
			
			$place->setLatitude( $item['position']['lat'] );
			$place->setLongitude( $item['position']['lng'] );
			
			$days = [
				'mon' => 'Monday',
				'tue' => 'Tuesday',
				'wed' => 'Wednesday',
				'thu' => 'Thursday',
				'fri' => 'Friday',
				'sat' => 'Saturday',
				'sun' => 'Sunday',
			];
			
			foreach($days as $day=>$label) {
				$oh = $item['opening_hours'][$day];
				
				$place->addOpeningHours( $label, $oh['from'], $oh['to'] );
			}
			
			
			$list[$place->getPointCode()] = $place;
			
		}
		
		return $list;
	}
	
	
}