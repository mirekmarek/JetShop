<?php
/**
 * @copyright Copyright (c) Miroslav Marek <mirek.marek@web-jet.cz>
 * @license EUPL 1.2  https://eupl.eu/1.2/en/
 * @author Miroslav Marek <mirek.marek@web-jet.cz>
 */
namespace JetApplicationModule\Carrier\PPL;


use JetApplication\Carrier_DeliveryPoint;
use JetApplication\EShops;
use JetApplication\EShop;

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
		
		foreach( EShops::getList() as $eshop) {
			foreach( $this->_downloadUpToDateDeliveryPointsList( $eshop ) as $item ) {
				$res[$item->getKey()] = $item;
			}
		}
		
		return $res;
	}
	
	/**
	 * @return Carrier_DeliveryPoint[]
	 */
	public function _downloadUpToDateDeliveryPointsList( EShop $eshop ): array
	{
		/**
		 * @var Config_PerShop $config
		 */
		$config = $this->carrier->getEshopConfig( $eshop );
		
		if(
			!$config->getAPIURL() ||
			!$config->getClientId() ||
			!$config->getClientSecret()
		) {
			return [];
		}
		
		$curl_handle = curl_init();
		
		curl_setopt($curl_handle, CURLOPT_URL, $config->getAPIURL().'login/getAccessToken' );
		curl_setopt($curl_handle, CURLOPT_POST, true);
		curl_setopt($curl_handle, CURLOPT_POSTFIELDS, http_build_query([
			'grant_type' => 'client_credentials',
			'client_id' => $config->getClientId(),
			'client_secret' => $config->getClientSecret(),
		]));
		curl_setopt($curl_handle, CURLOPT_RETURNTRANSFER, true);
		
		$response_data = json_decode(trim(curl_exec($curl_handle)), true);
		//$response_status = curl_getinfo($curl_handle, CURLINFO_HTTP_CODE);
		curl_close( $curl_handle );
		
		if(
			!$response_data ||
			!isset($response_data['access_token'])
		) {
			die();
		}
		
		$token = $response_data['access_token'];
		
		
		$per_page_limit = 1000;
		$total_items_count = null;
		$pages = null;
		$page_no = 0;
		$country = $eshop->getLocale()->getRegion();
		
		$types = [
			'ParcelBox',
			'AlzaBox',
			'ParcelShop',
		];
		
		
		$items = [];
		do {
			$curl_handle = curl_init();
			$URL = $config->getAPIURL().'accessPoint'
				.'?Limit='.$per_page_limit
				.'&Offset='.($per_page_limit*$page_no
					//.'&Country='.$country
				);
			
			curl_setopt($curl_handle, CURLOPT_URL, $URL );
			curl_setopt($curl_handle, CURLOPT_POST, false);
			curl_setopt($curl_handle, CURLOPT_RETURNTRANSFER, true);
			curl_setopt($curl_handle, CURLOPT_HEADER, true);
			curl_setopt($curl_handle, CURLOPT_HTTPHEADER, [
				//'Accept: application/json',
				//'Content-Type: application/json',
				'Authorization: Bearer '.$token
			]);
			
			$response_data = curl_exec($curl_handle);
			//$response_status = curl_getinfo($curl_handle, CURLINFO_HTTP_CODE);
			$header_size = curl_getinfo($curl_handle, CURLINFO_HEADER_SIZE);
			
			curl_close( $curl_handle );
			
			
			$header = substr($response_data, 0, $header_size);
			$body = substr($response_data, $header_size);
			
			$_headers = explode("\n", $header);
			
			$headers = [];
			foreach($_headers as $h) {
				$h=trim($h);
				if(!strpos($h, ':')) {
					continue;
				}
				
				$h = explode(':', $h);
				
				$headers[trim($h[0])] = trim($h[1]);
			}
			
			if($total_items_count===null) {
				$total_items_count = (int)$headers['X-Paging-Total-Items-Count'];
				$pages = floor($total_items_count/$per_page_limit);
			}
			
			$_items = json_decode($body, true);
			
			foreach($_items as $item) {
				if(
					$item['country']==$country /* &&
					in_array($item['accessPointType'], $types) */
				) {
					$items[] = $item;
				}
				
			}

			$page_no++;
		} while( $page_no<=$pages );
		
		
		
		$list = [];
		
		$days = [
			2 => 'monday',
			3 => 'tuesday',
			4 => 'wednesday',
			5 => 'thursday',
			6 => 'friday',
			7 => 'saturday',
			1 => 'sunday',
		];
		
		
		
		
		foreach( $items as $item ) {
			
			$place = new Carrier_DeliveryPoint();
			
			$place->setCarrier( $this->carrier );
			$place->setPointType( $item['accessPointType'] );
			$place->setPointLocale( $eshop->getLocale() );
			
			$place->setPointCode( $item['accessPointCode'] );
			$place->setName( $item['name'] );
			$place->setStreet( $item['street'] );
			$place->setTown( $item['city'] );
			$place->setZip( $item['zipCode'] );
			
			
			//$place->addImage($item['_links']['picture']['href']);
			
			$place->setLatitude( $item['gps']['latitude'] );
			$place->setLongitude( $item['gps']['longitude'] );
			
			
			$open_hours = [
			];
			
			
			foreach($days as $day=>$day_label) {
				
				foreach( $item["workHours"] as $oh ) {
					if($oh['weekDay']!=$day) {
						continue;
					}
					
					if(!isset($open_hours[$day_label])) {
						$open_hours[$day_label] = [
							'open1' => '',
							'close1' => '',
							
							'open2' => '',
							'close2' => '',
							
							'open3' => '',
							'close3' => '',
						];
					}
					
					$day_part = $oh['dayPart'];
					$day_part++;
					
					$open_hours[$day_label]['open'.$day_part] = $oh['openFrom'];
					$open_hours[$day_label]['close'.$day_part] = $oh['openTo'];
				}
				
			}
			
			foreach($open_hours as $day=>$oh) {
				$place->addOpeningHours(
					$day,
					$oh['open1'],
					$oh['close1'],
					$oh['open2'],
					$oh['close2'],
					$oh['open3'],
					$oh['close3']
				);
				
			}
			
			$list[$place->getPointCode()] = $place;
			
		}
		
		
		
		return $list;
	}
	
}