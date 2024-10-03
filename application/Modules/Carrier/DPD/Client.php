<?php
/**
 *
 * @copyright
 * @license
 * @author
 */
namespace JetApplicationModule\Carrier\DPD;

use Jet\Exception;
use JetApplication\Carrier_DeliveryPoint;
use JetApplication\Shops;
use JetApplication\Shops_Shop;

class Client {
	protected Main $carrier;
	
	public function __construct( Main $carrier )
	{
		$this->carrier = $carrier;
	}
	
	
	/**
	 * @return Carrier_DeliveryPoint[]
	 */
	public function downloadUpToDateDeliveryPointsList(): array
	{
		$res = [];
		
		foreach(Shops::getList() as $shop) {
			foreach( $this->_downloadUpToDateDeliveryPointsList( $shop ) as $item ) {
				$res[$item->getKey()] = $item;
			}
		}
		
		return $res;
	}
	
	/**
	 * @return Carrier_DeliveryPoint[]
	 */
	public function _downloadUpToDateDeliveryPointsList( Shops_Shop $shop ): array
	{
		$list = [];
		
		$JSON_URL = $this->carrier->getConfig( $shop )->getURLJSONBranches();
		if(!$JSON_URL) {
			return [];
		}
		
		$data = json_decode( file_get_contents( $JSON_URL ), true );
		
		if(
			!isset($data['status']) ||
			$data['status']!='ok' &&
			!isset($data['data']['items'])
		) {
			throw new Exception('Unable to load JSON '.$JSON_URL);
			return [];
		}
		
		
		foreach( $data['data']['items'] as $item ) {
			$point = new Carrier_DeliveryPoint();
			
			$point->setCarrier( $this->carrier );
			$point->setPointType( Main::DP_TYPE_BRANCH );
			$point->setPointLocale( $shop->getLocale() );
			$point->setPointType( '' );
			
			
			$point->setPointCode( $item['id'] );
			
			$point->setName( $item['company'] );
			$point->setStreet( $item['street'].' '.$item['house_number'] );
			$point->setTown( $item['city'] );
			$point->setZip( $item['postcode'] );
			
			$point->setLatitude( $item['latitude'] );
			$point->setLongitude( $item['longitude'] );
			
			$point->addImage( $item['photo'] );
			
			foreach($item['hours'] as $d) {
				$point->addOpeningHours(
					$d['dayName'],
					$d['openMorning'],
					$d['closeMorning'],
					$d['openAfternoon'],
					$d['closeAfternoon']
				);
			}
			
			
			$list[$point->getKey()] = $point;
		}
		
		
		return $list;
	}
	
}