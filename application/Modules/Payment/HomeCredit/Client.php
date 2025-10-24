<?php
/**
 * @copyright Copyright (c) Miroslav Marek <mirek.marek@web-jet.cz>
 * @license EUPL 1.2  https://eupl.eu/1.2/en/
 * @author Miroslav Marek <mirek.marek@web-jet.cz>
 */
namespace JetApplicationModule\Payment\HomeCredit;

use CurlHandle;

class Client {
	const REQUEST_TYPE_GET = 'GET';
	const REQUEST_TYPE_POST = 'POST';
	const REQUEST_TYPE_PUT = 'PUT';
	
	protected string $last_request_type = '';
	protected mixed $last_request_data = null;
	protected string $last_request_URL = '';
	protected ?int $last_response_code = null;
	protected ?string $last_response_raw = null;
	protected mixed $last_response = null;
	protected Config_PerShop $config;
	protected ?string $access_token = null;
	
	
	public function __construct( Config_PerShop $config )
	{
		$this->config = $config;
	}
	
	public function getLastRequestType() : string
	{
		return $this->last_request_type;
	}
	
	public function getLastRequestData() : mixed
	{
		return $this->last_request_data;
	}
	
	public function getLastRequestURL() : string
	{
		return $this->last_request_URL;
	}
	
	public function getLastResponseCode() : ?int
	{
		return $this->last_response_code;
	}
	
	public function getLastResponseRaw() : ?string
	{
		return $this->last_response_raw;
	}
	
	public function getLastResponse() : mixed
	{
		return $this->last_response;
	}
	
	
	protected function request( string $URL, string $type, mixed $data=null ) : mixed
	{
		if( !$this->access_token ) {
			$this->logim();
		}
		
		if( !$this->access_token ) {
			throw new Exception(
				'Request without login - login fist please',
				Exception::CODE_REQUEST_WITHOUT_LOGIN
			);
		}
		
		$this->last_request_type = $type;
		$this->last_request_data = $data;
		$this->last_request_URL = $this->config->getApiUrl() . $URL;
		
		$this->last_response_code = null;
		$this->last_response_raw = null;
		$this->last_response = null;
		
		
		$ch = curl_init( $this->config->getApiUrl() . $URL );
		
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
		curl_setopt($ch, CURLINFO_HEADER_OUT, true);
		curl_setopt($ch,CURLOPT_HTTPHEADER,array(
			'Content-Type: application/json',
			'Charset: utf-8',
			'Authorization: Bearer ' . $this->access_token
		));

		
		switch($type) {
			case static::REQUEST_TYPE_GET:
				curl_setopt($ch, CURLOPT_HTTPGET, true);
				break;
			case static::REQUEST_TYPE_POST:
				curl_setopt($ch, CURLOPT_POST, true);
				curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($data) );
				break;
			case static::REQUEST_TYPE_PUT:
				curl_setopt($ch, CURLOPT_CUSTOMREQUEST, 'PUT');
				
				if($data) {
					curl_setopt($ch, CURLOPT_POST, true);
					curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode( $data ) );
				}
				break;
		}
		
		return $this->request_exec($ch);
		
	}
	

	protected function request_exec( CurlHandle $ch ) : mixed
	{
		$this->last_response_raw = curl_exec($ch);
		
		if( curl_errno($ch) ) {
			throw new Exception(
				'CURL error: ['.curl_errno($ch).'] '.curl_error($ch),
				Exception::CODE_CURL_ERROR
			);
		}
		
		
		$this->last_response_code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
		
		curl_close($ch);
		
		$this->last_response = json_decode( $this->last_response_raw,true );
		
		if($this->last_response===false) {
			throw new Exception(
				'Bad response - response is not JSON',
				Exception::CODE_BAD_RESPONSE_NOT_JSON
			);
		}
		
		
		if($this->last_response_code<200 || $this->last_response_code>299) {
			throw new Exception(
				'Response error: ['.$this->last_response_code.'] '.$this->last_response_raw,
				Exception::CODE_RESPONSE_ERROR
			);
		}
		
		
		
		return $this->last_response;
		
	}
	
	
	protected function request_PUT( $URL, $data=null ) {
		return $this->request( $URL, static::REQUEST_TYPE_PUT, $data );
	}
	
	protected function request_POST( $URL, $data ) {
		return $this->request( $URL, static::REQUEST_TYPE_POST, $data );
	}
	
	protected function request_GET( $URL ) {
		return $this->request( $URL, static::REQUEST_TYPE_GET );
	}
	
	
	public function logim() : void
	{
		if($this->access_token) {
			return;
		}
		
		$this->last_request_type = static::REQUEST_TYPE_POST;
		$this->last_request_data = '**SECREAT**';
		$this->last_request_URL = $this->config->getApiUrl() . '/authentication/v1/partner/';
		$this->last_response_code = null;
		$this->last_response_raw = null;
		$this->last_response = null;
		
		$ch = curl_init( $this->last_request_URL );
		
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
		curl_setopt($ch, CURLINFO_HEADER_OUT, true);
		curl_setopt($ch, CURLOPT_HTTPHEADER, [
			'Content-Type: application/json',
			'Charset: utf-8'
		]);
		
		curl_setopt($ch, CURLOPT_POST, true);
		curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode([
			'username' => $this->config->getUserName(),
			'password' => $this->config->getPassword(),
		]));
		
		$response = $this->request_exec($ch);
		
		if(empty($response['accessToken'])) {
			throw new Exception(
				Exception::CODE_LOGIN_FAILED,
				'Login failed',
				$this
			);
		}
		
		$this->access_token = $response['accessToken'];
	}
	
	public function sendCreditApplication( CreditApplication $credit_ppplication, string &$id, string &$redirect_url ) : void
	{
		$response = $this->request_POST('/financing/v1/applications', $credit_ppplication);
		
		if(empty($response['id']) || empty($response['gatewayRedirectUrl'])) {
			throw new Exception(
				'Bad reponse',
				Exception::CODE_BAD_RESPONSE
			);
		}
		
		$id = $response['id'];
		$redirect_url = $response['gatewayRedirectUrl'];
		
		$contract = Contract::newContract(
			$this->config->getEshop(),
			$credit_ppplication->getOrderNumber(),
			$id
		);
	}
	
	public function getCreditApplicationId( int $order_id ) : ?string
	{
		$contract = Contract::get( $order_id );
		return $contract?->getApplicationId()??null;
	}
	
	public function getState( int $order_id, string &$state, string &$state_reason ) : bool
	{
		$contract = Contract::get( $order_id );
		if(!$contract) {
			return false;
		}
		

		$credit_application = $this->getCreditApplicationDetail( $contract->getApplicationId() );
		if(!$credit_application) {
			return false;
		}
		
		$state = $credit_application['state'];
		$state_reason = $credit_application['stateReason'];
		
		$contract = Contract::get( $order_id );
		$contract->setState( $state );
		$contract->setStateReason( $state_reason );
		$contract->save();
		
		return true;
	}
	
	
	public function cancelCreditApplication(
		string $credit_ppplication_id,
		string $reason,
		string $custom_reason
	) : void {
		$this->request_PUT('/financing/v1/applications/' . $credit_ppplication_id . '/cancel', [
			'reason' => $reason,
			'customReason' => $custom_reason
		]);
		
	}
	
	public function eventOrderSent( string $credit_ppplication_id ) : void
	{
		$this->request_PUT('/financing/v1/applications/' . $credit_ppplication_id . '/order/send');
		
	}
	
	public function eventOrderDelivered( string $credit_ppplication_id ) : void
	{
		$this->request_PUT('/financing/v1/applications/' . $credit_ppplication_id . '/order/deliver');
	}
	
	public function getCreditApplicationDetail( string $credit_ppplication_id ) : ?array
	{
		return $this->request_GET('/financing/v1/applications/' . $credit_ppplication_id);
	}
	
	public function changeCreditApplicationState( string $credit_ppplication_id, string $state ) : void
	{
		$this->request_POST('/fakeshop/rest/applications/' . $credit_ppplication_id . '/changeState', [
			'stateReason' => $state
		]);
		
	}
}