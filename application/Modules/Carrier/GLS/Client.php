<?php
/**
 * @copyright Copyright (c) Miroslav Marek <mirek.marek@web-jet.cz>
 * @license EUPL 1.2  https://eupl.eu/1.2/en/
 * @author Miroslav Marek <mirek.marek@web-jet.cz>
 */
namespace JetApplicationModule\Carrier\GLS;


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

	
	
	protected function download( Locale $locale, string $URL ): array
	{
		
		$data = file_get_contents($URL);
		
		$data = gzdecode( $data );
		
		$xml = simplexml_load_string( $data );
		
		if(!$xml) {
			return [];
		}
		
		$list = [];
		
		foreach ($xml->Data->DropoffPoint AS $node) {
			$place = new Carrier_DeliveryPoint();
			$place->setCarrier($this->carrier);
			$place->setPointLocale( $locale );
			
			
			$attr = $node->attributes();
			
			$place->setPointCode((string)$attr['ID']);
			$place->setName( (string)$attr['Name'] );
			$place->setStreet( (string)$attr['Address'] );
			$place->setTown( (string)$attr['CityName'] );
			$place->setZip( (string)$attr['ZipCode'] );
			$place->setLatitude( (string)$attr['GeoLat'] );
			$place->setLongitude( (string)$attr['GeoLng'] );
			
			
			
			foreach($node->Openings->Openings as $op) {
				$attr = $op->attributes();
				
				$day = strtolower( (string)$attr['Day'] );
				$op = trim((string)$attr['OpenHours']);
				$br = trim((string)$attr['MidBreak']);
				
				if( !str_contains( $br, '-' ) ) {
					$br = '';
				}
				
				$op = explode('-', $op);
				
				if(!isset($op[1])) {
					continue;
				}
				
				list($open, $close) = $op;
				
				if(!$br) {
					$place->addOpeningHours($day, $open, $close);
				} else {
					$br = explode('-', $br);
					
					list($br_start, $br_end) = $br;
					
					$place->addOpeningHours(
						$day,
						
						$open, $br_start,
						
						$br_end, $close
					);
				}
			}
			
			$list[$place->getPointCode()] = $place;
		}
		
		return $list;
	}
	
	
}