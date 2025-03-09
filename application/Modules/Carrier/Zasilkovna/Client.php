<?php
/**
 * @copyright Copyright (c) Miroslav Marek <mirek.marek@web-jet.cz>
 * @license EUPL 1.2  https://eupl.eu/1.2/en/
 * @author Miroslav Marek <mirek.marek@web-jet.cz>
 */
namespace JetApplicationModule\Carrier\Zasilkovna;


use Jet\Exception;
use Jet\Factory_MVC;
use Jet\Locale;
use JetApplication\Carrier_DeliveryPoint;
use JetApplication\Carrier_Document;
use JetApplication\OrderDispatch;
use JetApplication\EShops;
use JetApplication\EShop;
use JetApplication\OrderDispatch_Status_Sent;
use SoapClient;
use SoapFault;

class Client
{
	protected Main $carrier;
	
	public function __construct( Main $carrier )
	{
		$this->carrier = $carrier;
	}
	
	public function getCarriers( ?EShop $eshop = null ): array
	{
		$eshop = $eshop ? : EShops::getDefault();
		
		$API_key = $this->carrier->getConfig( $eshop )->getAPIKey();
		if( !$API_key ) {
			return [];
		}
		
		$JSON_URL = "https://pickup-point.api.packeta.com/v5/$API_key/carrier/json?lang=" . Locale::getCurrentLocale()->getLanguage();
		
		$data = json_decode( file_get_contents( $JSON_URL ), true );
		
		if(
			!is_array( $data )
		) {
			return [];
		}
		
		$res = [];
		foreach( $data as $d ) {
			$res[$d['id']] = $d;
		}
		
		return $res;
	}
	
	
	/**
	 * @return Carrier_DeliveryPoint[]
	 * @throws Exception
	 */
	public function downloadUpToDateDeliveryPointsList(): array
	{
		$res = [];
		
		foreach( EShops::getList() as $eshop ) {
			foreach( $this->_downloadUpToDateDeliveryPointsList_branches( $eshop ) as $item ) {
				$res[$item->getKey()] = $item;
			}
			foreach( $this->_downloadUpToDateDeliveryPointsList_boxes( $eshop ) as $item ) {
				$res[$item->getKey()] = $item;
			}
		}
		
		return $res;
	}
	
	/**
	 * @return Carrier_DeliveryPoint[]
	 * @throws Exception
	 */
	public function _downloadUpToDateDeliveryPointsList_branches( EShop $eshop ): array
	{
		
		$API_key = $this->carrier->getConfig( $eshop )->getAPIKey();
		if( !$API_key ) {
			return [];
		}
		
		$JSON_URL = 'https://pickup-point.api.packeta.com/v5/' . $API_key . '/branch/json?language=' . $eshop->getLocale()->getLanguage();
		
		return $this->download( $eshop->getLocale(), Main::DP_TYPE_BRANCH, $JSON_URL );
	}
	
	/**
	 * @return Carrier_DeliveryPoint[]
	 * @throws Exception
	 */
	public function _downloadUpToDateDeliveryPointsList_boxes( EShop $eshop ): array
	{
		$API_key = $this->carrier->getConfig( $eshop )->getAPIKey();
		if( !$API_key ) {
			return [];
		}
		
		$JSON_URL = 'https://pickup-point.api.packeta.com/v5/' . $API_key . '/box/json?language=' . $eshop->getLocale()->getLanguage();
		
		return $this->download( $eshop->getLocale(), Main::DP_TYPE_BOX, $JSON_URL );
	}
	
	protected function download( Locale $locale, string $type, string $JSON_URL ): array
	{
		$data = json_decode( file_get_contents( $JSON_URL ), true );
		
		if(
			!is_array( $data )
		) {
			throw new Exception( 'Unable to load JSON ' . $JSON_URL );
			
			return [];
		}
		
		
		$list = [];
		
		$relevant_country = strtolower( $locale->getRegion() );
		
		foreach( $data as $item ) {
			
			if( $item['country'] != $relevant_country ) {
				continue;
			}
			
			$point = $this->createDP( $item, $type, $locale );
			
			$list[$point->getKey()] = $point;
			
		}
		
		return $list;
		
	}
	
	protected function createDP( array $item, string $type, Locale $locale ): Carrier_DeliveryPoint
	{
		$days = [
			'monday'    => 'monday',
			'tuesday'   => 'tuesday',
			'wednesday' => 'wednesday',
			'thursday'  => 'wednesday',
			'friday'    => 'friday',
			'saturday'  => 'saturday',
			'sunday'    => 'sunday',
		];
		
		
		$point = new Carrier_DeliveryPoint();
		
		
		$point->setCarrier( $this->carrier );
		$point->setPointType( $type );
		$point->setPointLocale( $locale );
		
		$point->setIsActive( $item['status']['statusId'] == 1 );
		
		$point->setPointCode( $item['id'] );
		$point->setName( $item['name'] );
		
		$point->setStreet( $item['street'] );
		$point->setTown( $item['city'] );
		$point->setZip( $item['zip'] );
		
		$point->setLatitude( $item['latitude'] );
		$point->setLongitude( $item['longitude'] );
		
		foreach( $item['photos'] as $photo ) {
			$point->addImage( $photo['thumbnail'] );
		}
		
		foreach( $days as $day => $day_name ) {
			if( empty( $item['openingHours']['regular'][$day] ) ) {
				continue;
			}
			
			$oh = $item['openingHours']['regular'][$day];
			
			$oh = explode( ',', $oh );
			
			$oh[0] = explode( '-', $oh[0] );
			if( isset( $oh[1] ) ) {
				$oh[1] = explode( '-', $oh[1] );
			} else {
				$oh[1] = [
					'',
					''
				];
			}
			
			if( !isset( $oh[0][0] ) ) {
				$oh[0][0] = '';
			}
			if( !isset( $oh[0][1] ) ) {
				$oh[0][1] = '';
			}
			if( !isset( $oh[1][0] ) ) {
				$oh[1][0] = '';
			}
			if( !isset( $oh[1][1] ) ) {
				$oh[1][1] = '';
			}
			
			$point->addOpeningHours( $day_name, $oh[0][0], $oh[0][1], $oh[1][0], $oh[1][1] );
		}
		
		return $point;
	}
	
	public function createConsignment( OrderDispatch $dispatch ): bool
	{
		
		$cfg = $this->carrier->getConfig( $dispatch->getEshop() );
		
		$gw = new SoapClient( $cfg->getSoapApiURL() );
		$api_password = $cfg->getAPIPassword();
		
		$packet = null;
		try {
			$service = $dispatch->getCarrierService();
			
			$address_id = $service->getCarrierServiceIdentificationCode();
			if( $dispatch->getDeliveryPointCode() ) {
				$address_id = $dispatch->getDeliveryPointCode();
			}
			
			$data = [
				'number'      => $dispatch->getOrderId(),
				'name'        => $dispatch->getRecipientFirstName(),
				'surname'     => $dispatch->getRecipientSurname(),
				'company'     => $dispatch->getRecipientCompany(),
				'email'       => $dispatch->getRecipientEmail(),
				'phone'       => $dispatch->getRecipientPhone(),
				'addressId'   => $address_id,
				'value'       => $dispatch->getFinancialValue(),
				'weight'      => $dispatch->getTotalWeight(),
				'eshop'       => $cfg->getEshopId(),
				'adultContent'=> $dispatch->hasAdditionalConsignmentParameter( 'adultContent' ),
				'note'        => $dispatch->getRecipientNote(),
				'street'      => $dispatch->getRecipientStreet(),
				'houseNumber' => '',
				'city'        => $dispatch->getRecipientTown(),
				'zip'         => $dispatch->getRecipientZip(),
			];
			
			if($dispatch->getCod()) {
				$data['currency']    = $dispatch->getCodCurrency()->getCode();
				$data['cod']         = $dispatch->getCod();
			}
			
			
			if( $service->getsPackagingHasDimensions() ) {
				foreach( $dispatch->getPackets() as $p ) {
					$data['size'] = [
						'length' => round( $p->getSizeL() * 10 ),
						'width'  => round( $p->getSizew() * 10 ),
						'height' => round( $p->getSizeH() * 10 ),
					];
					break;
				}
			}
			
			/** @noinspection PhpUndefinedMethodInspection */
			$packet = $gw->createPacket( $api_password, $data );
		} catch( SoapFault $e ) {
			$error = $e->getMessage().'<br>';
			
			
			
			if(
				isset($e->detail->PacketAttributesFault->attributes->fault) &&
				is_array($e->detail->PacketAttributesFault->attributes->fault) ||
				is_object($e->detail->PacketAttributesFault->attributes->fault)
			) {
				$fault = $e->detail->PacketAttributesFault->attributes->fault;
				
				if(is_array($fault)) {
					foreach($fault as $f) {
						$error .= $f?->fault.'<br>';
					}
				} else {
					$error .= $fault?->fault.'<br>';
				}
				

			}
			$dispatch->setConsignmentCreateError( $error );
			return false;
			
		}
		
		if(
			is_object( $packet ) &&
			isset( $packet->id ) &&
			isset( $packet->barcode )
		) {
			
			$dispatch->setConsignmentCreated( $packet->id, $packet->barcode );
			
			return true;
		}
		
		$dispatch->setConsignmentCreateError( 'unknown error' );
		return false;
	}
	
	public function cancelConsignment( OrderDispatch $dispatch, string &$error_message = '' ): bool
	{
		$cfg = $this->carrier->getConfig( $dispatch->getEshop() );
		
		$gw = new SoapClient( $cfg->getSoapApiURL() );
		$api_password = $cfg->getAPIPassword();
		
		try {
			
			/** @noinspection PhpUndefinedMethodInspection */
			$gw->cancelPacket( $api_password, $dispatch->getConsignmentId() );
		} catch( SoapFault $e ) {
			$error_message = $e->getMessage();
			return false;
			
		}
		
		return true;
	}
	
	public function getPacketLabel( OrderDispatch $dispatch, string &$error_message = '' ): ?Carrier_Document
	{
		
		$cfg = $this->carrier->getConfig( $dispatch->getEshop() );
		
		$gw = new SoapClient( $cfg->getSoapApiURL() );
		$api_password = $cfg->getAPIPassword();
		$format = $cfg->getLabelsFormat();
		
		try {
			/** @noinspection PhpUndefinedMethodInspection */
			$label = $gw->packetLabelPdf( $api_password, $dispatch->getConsignmentId(), $format, 0 );
			
			return new Carrier_Document(
				mime_type: 'application/pdf',
				data: $label
			);
		} catch( SoapFault $e ) {
			$error_message = $e->getMessage();
			
		}
		
		return null;
	}
	
	/**
	 * @param OrderDispatch[] $dispatches
	 * @param string $error_message
	 * @return Carrier_Document|null
	 */
	public function getPacketLabels( array $dispatches, string &$error_message = '' ): ?Carrier_Document
	{
		$gw = null;
		$api_password = null;
		$format = '';
		$ids = [];
		
		foreach( $dispatches as $dispatch ) {
			if( !$gw ) {
				$cfg = $this->carrier->getConfig( $dispatch->getEshop() );
				
				$gw = new SoapClient( $cfg->getSoapApiURL() );
				$api_password = $cfg->getAPIPassword();
				$format = $cfg->getLabelsFormat();
			}
			
			$ids[] = $dispatch->getConsignmentId();
		}
		
		if( !$gw ) {
			return null;
		}
		
		
		try {
			/** @noinspection PhpUndefinedMethodInspection */
			$label = $gw->packetsLabelsPdf( $api_password, $ids, $format, 0 );
			
			return new Carrier_Document(
				mime_type: 'application/pdf',
				data: $label
			);
		} catch( SoapFault $e ) {
			$error_message = $e->getMessage();
		}
		
		return null;
		
	}
	
	
	/**
	 * @param OrderDispatch[] $dispatches
	 * @param string $error_message
	 * @return Carrier_Document|null
	 */
	public function getDeliveryNote( array $dispatches, string &$error_message = '' ): ?Carrier_Document
	{
		$gw = null;
		$api_password = null;
		$format = '';
		$ids = [];
		
		foreach( $dispatches as $dispatch ) {
			if( !$gw ) {
				$cfg = $this->carrier->getConfig( $dispatch->getEshop() );
				
				$gw = new SoapClient( $cfg->getSoapApiURL() );
				$api_password = $cfg->getAPIPassword();
				$format = $cfg->getLabelsFormat();
			}
			
			$ids[] = $dispatch->getConsignmentId();
		}
		
		if( !$gw ) {
			return null;
		}
		
		
		try {
			/** @noinspection PhpUndefinedMethodInspection */
			$res = $gw->createShipment( $api_password, $ids );
			
			
			$view = Factory_MVC::getViewInstance( $this->carrier->getViewsDir() );
			$view->setVar('dispatches', $dispatches);
			$view->setVar( 'id', $res->id );
			$view->setVar( 'barcode', $res->barcode );
			
			return new Carrier_Document(
				mime_type: 'text/html',
				data: $view->render('delivery-note')
			);
		} catch( SoapFault $e ) {
			$error_message = $e->getMessage();
		}
		
		return null;
		
	}
	
	
	
	public function actualizeTracking( OrderDispatch $dispatch, string &$error_message = '' ): bool
	{
		$cfg = $this->carrier->getConfig( $dispatch->getEshop() );
		
		$gw = new SoapClient( $cfg->getSoapApiURL() );
		$api_password = $cfg->getAPIPassword();
		
		$c_id = $dispatch->getConsignmentId();
		
		try {
			/** @noinspection PhpUndefinedMethodInspection */
			$status_data = $gw->packetStatus( $api_password, $c_id );
			/** @noinspection PhpUndefinedMethodInspection */
			$_tracking_data = $gw->packetTracking( $api_password, $c_id );
		} catch( SoapFault $e ) {
			$error_message = $e->getMessage();
			return false;
		}
		

		$new_status = OrderDispatch_Status_Sent::get();
		
		$tracking_data = [];
		
		//TODO:
		var_dump( $status_data, $_tracking_data );
		die('???');
		
		$dispatch->setTrackingData(
			new_tracking_history: $tracking_data,
			new_status: $new_status
		);
		
		return true;
	}
}