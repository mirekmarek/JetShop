<?php
/**
 * @copyright Copyright (c) Miroslav Marek <mirek.marek@web-jet.cz>
 * @license EUPL 1.2  https://eupl.eu/1.2/en/
 * @author Miroslav Marek <mirek.marek@web-jet.cz>
 */
namespace JetApplicationModule\Payment\ESSOX;


use Jet\Http_Headers;
use Jet\Logger;
use JetApplication\EShopConfig_ModuleConfig_PerShop;
use JetApplication\Order;

class Client {
	public const SERVICE_STD = 'std';
	public const SERVICE_SPLIT = 'split';
	
	
	public const METHOD_POST = 'POST';
	public const METHOD_GET = 'GET';
	public const METHOD_DELETE = 'DELETE';
	public const METHOD_PUT = 'PUT';
	
	public const HTTP_STATUS_OK = 200;
	public const HTTP_STATUS_CREATED = 201;
	public const HTTP_STATUS_ACCEPTED = 202;
	public const HTTP_STATUS_NO_CONTENT = 204;
	
	protected string $API_URL = '';
	protected string $key = '';
	protected string $secret = '';
	
	public string $last_request_URL = '';
	public string $last_request_data = '';
	public array $last_request_headers = [];
	
	public int $last_response_status = 0;
	public string $last_error_message = '';
	public null|false|string $last_response_data_raw = '';
	public null|false|array $last_response_data = null;
	public string $last_response_headers = '';
	
	protected ?string $access_token = null;
	protected ?string $access_token_type = null;
	
	
	public function __construct( EShopConfig_ModuleConfig_PerShop|Config_PerShop $config )
	{
		$this->API_URL = $config->getApiUrl();
		$this->key = $config->getClientKey();
		$this->secret = $config->getClientSecret();
		
	}
	
	public function _exec( string $method, string $object, array $data = [], array $params = [], array $headers = [], $use_json=false ) : bool
	{
		if( str_ends_with( $object, '/' ) ) {
			$object = substr($object, 0, -1);
		}
		
		$this->last_error_message = '';
		$this->last_request_data = '';
		$this->last_response_data = null;
		$this->last_response_data_raw = '';
		$this->last_response_status = 0;
		
		
		
		
		$URL = $this->API_URL.$object.'?' . http_build_query($params);
		$this->last_request_URL = $URL;
		
		$curl_handle = curl_init();
		
		curl_setopt($curl_handle, CURLOPT_URL, $URL );
		
		if($use_json) {
			$data = $this->_toJson($data);
		} else {
			$data = (is_array($data)) ? http_build_query($data) : $data;
		}
		$this->last_request_data = $data;
		
		switch ($method) {
			case self::METHOD_DELETE:
				curl_setopt($curl_handle, CURLOPT_CUSTOMREQUEST, self::METHOD_DELETE);
				break;
			case self::METHOD_POST:
				curl_setopt($curl_handle, CURLOPT_POST, true);
				curl_setopt($curl_handle, CURLOPT_POSTFIELDS, $data);
				break;
			case self::METHOD_PUT:
				$handle = fopen('php://temp', 'w+');
				fwrite($handle, $data );
				rewind($handle);
				$f_stat = fstat($handle);
				curl_setopt($curl_handle, CURLOPT_PUT, true);
				curl_setopt($curl_handle, CURLOPT_INFILE, $handle);
				curl_setopt($curl_handle, CURLOPT_INFILESIZE, $f_stat['size']);
				break;
			case self::METHOD_GET:
				curl_setopt($curl_handle, CURLOPT_HTTPGET, true);
				break;
		}
		
		$this->last_request_headers = $headers;
		
		curl_setopt($curl_handle, CURLOPT_RETURNTRANSFER, true);
		curl_setopt($curl_handle, CURLOPT_HTTPHEADER, $headers);
		curl_setopt($curl_handle, CURLOPT_SSL_VERIFYHOST, 2);
		curl_setopt($curl_handle, CURLOPT_SSL_VERIFYPEER, false);
		//curl_setopt($curl_handle, CURLOPT_CAINFO, $cainfo);
		
		$response_data = curl_exec($curl_handle);
		
		$response_status = curl_getinfo($curl_handle, CURLINFO_HTTP_CODE);
		
		$this->last_response_data_raw = $response_data;
		
		$this->last_response_status = $response_status;
		
		
		
		if($response_data===false) {
			$this->last_error_message = 'CURL_ERR:' . curl_errno($curl_handle) . ' - ' . curl_error($curl_handle);
			$this->last_response_data = null;
			
			curl_close($curl_handle);
			return false;
		} else {
			if(
				$response_status==self::HTTP_STATUS_NO_CONTENT ||
				$response_status==self::HTTP_STATUS_CREATED
			) {
				curl_close($curl_handle);
				return true;
			}
			
			$this->last_response_data = $this->_fromJson($response_data);
			
			if(!is_array($this->last_response_data)) {
				$this->last_error_message = "Neocekavana odpoved: HTTP status: {$response_status}, Data odpovedi: " . $response_data;
				
				curl_close($curl_handle);
				return false;
			} else {
				
				switch ($response_status) {
					case self::HTTP_STATUS_OK:
					case self::HTTP_STATUS_ACCEPTED:
						curl_close($curl_handle);
						return true;
					default:
						$this->last_error_message = "http error: {$response_status}";
						curl_close($curl_handle);
						return false;
				}
				
			}
			
		}
		
	}
	
	protected function _toJson( array $data ) : string
	{
		return json_encode( $data );
	}
	
	protected function _fromJson( string $str ) : array|false
	{
		
		$result = json_decode($str, true);
		
		if(!is_array($result)) {
			return false;
		}
		
		return $result;
	}
	
	
	public function getToken() : string|false
	{
		if($this->access_token===null) {
			$resp = $this->_exec(
				static::METHOD_POST,
				'token',
				[
					'grant_type' => 'client_credentials',
					'scope' => 'scopeFinit.consumerGoods.eshop',
				],
				[],
				[
					'Authorization: Basic '.base64_encode($this->key.':'.$this->secret)
				]
			);
			
			if(
				!$resp ||
				empty($this->last_response_data['access_token'])
			) {
				$this->access_token = false;
			} else {
				$this->access_token = $this->last_response_data['access_token'];
				$this->access_token_type = $this->last_response_data['token_type'];
			}
		}
		
		return $this->access_token;
	}
	
	public function sendProposal( Order $order, bool $spreaded_instalments, string $return_url ) : bool
	{
		
		
		$data = [
			'firstName' => $order->getBillingAddress()->getFirstName(),
			'surname' => $order->getBillingAddress()->getSurname(),
			'mobilePhonePrefix' => '+420',
			'mobilePhoneNumber' => $order->getPhone(),
			'email' => $order->getEmail(),
			'price' => $order->getTotalAmount_WithVAT(),
			'productId' => 0,
			'orderId' => $order->getId(),
			'customerId' => $order->getCustomerId(),
			'transactionId' => $order->getId(),
			/*
			'shippingAddress' => [
				'street' => $order->getDeliveryAddress()->getStreetAddress(),
				'houseNumber' =>  '',
				'city' =>  $order->getDeliveryAddress()->getCity(),
				'zip' =>  $order->getDeliveryAddress()->getPostcode()
			],
			*/
			'callbackUrl' => $return_url,
			'spreadedInstalments' => $spreaded_instalments
		];
		
		
		$token = $this->getToken();
		if(!$token) {
			Logger::danger(
				event: 'ESSOX-problem:unable_to_get_token',
				event_message: 'Nelze ziskat auth. token',
				context_object_data: $this->last_response_data_raw
			);

			return false;
		}
		
		$object = 'consumergoods/v1/api/consumergoods/proposal';
		
		$headers = [
			'accept: application/json',
			'Content-Type: application/json-patch+json',
			'Authorization: '.$this->access_token_type.' '.$token
		];
		
		
		$resp = $this->_exec(
			static::METHOD_POST,
			$object,
			$data,
			[],
			$headers,
			true
		);
		
		if(
			!$resp ||
			empty($this->last_response_data['contractId']) ||
			empty($this->last_response_data['redirectionUrl'])
		) {
			Logger::danger(
				event: 'ESSOX-problem:unable_to_send',
				event_message: 'Nepodarilo se odeslat navrh',
				context_object_data: $this->last_response_data_raw
			);
			
			return false;
		}
		
		
		
		Contract::newContract( $order, $this->last_response_data['contractId'] );
		
		Http_Headers::movedTemporary( $this->last_response_data['redirectionUrl'] );
		return true;
	}
	
	public function getStatus( string $contract_id ) : array|false
	{
		
		$token = $this->getToken();
		if(!$token) {
			return false;
		}
		
		$object = 'consumergoods/v1/api/consumergoods/status';
		$params = [
			'ContractId' => $contract_id
		];
		
		$headers = [
			'Authorization: '.$this->access_token_type.' '.$token
		];
		
		$resp = $this->_exec(
			static::METHOD_GET,
			$object,
			[],
			$params,
			$headers,
			false
		);
		
		if(
			!$resp ||
			empty($this->last_response_data['businessCases'])
		) {
			return false;
		}
		
		return $this->last_response_data['businessCases'][0];
	}
	
	public function getCalcURL( float $price ) : string
	{
		
		$data = [
			'price' => $price,
			'productId' => 0,
		];
		
		$token = $this->getToken();
		if(!$token) {
			return false;
		}
		
		$object = 'consumergoods/v1/api/consumergoods/calculator';
		
		$headers = [
			'accept: application/json',
			'Content-Type: application/json-patch+json',
			'Authorization: '.$this->access_token_type.' '.$token
		];
		
		$resp = $this->_exec(
			static::METHOD_POST,
			$object,
			$data,
			[],
			$headers,
			true
		);
		
		if(
			!$resp ||
			empty($this->last_response_data['redirectionUrl'])
		) {
			return false;
		}
		
		return $this->last_response_data['redirectionUrl'];
		
	}
	
}