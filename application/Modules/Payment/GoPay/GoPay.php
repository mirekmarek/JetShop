<?php
/**
 * @copyright Copyright (c) Miroslav Marek <mirek.marek@web-jet.cz>
 * @license EUPL 1.2  https://eupl.eu/1.2/en/
 * @author Miroslav Marek <mirek.marek@web-jet.cz>
 */
namespace JetApplicaTionModule\Payment\GoPay;


use Jet\Data_DateTime;
use JetApplication\EShops;
use JetApplication\Order;

class GoPay {
	public const ERROR_AUT_TOKEN_GET_FAILED = 'aut_token_get_failed';
	public const ERROR_PAYMENT_CREATE_FAILED = 'payment_create_failed';
	public const ERROR_PAYMENT_GET_FAILED = 'payment_get_failed';
	
	protected ?GoPay_Logger $logger = null;
	
	protected GoPay_Config $config;
	
	
	public function __construct( GoPay_Config $config ) {
		$this->config = $config;
	}
	
	public function getLogger(): ?GoPay_Logger
	{
		return $this->logger;
	}
	
	public function setLogger( ?GoPay_Logger $logger ): void
	{
		$this->logger = $logger;
	}
	
	
	protected function getAuthToken( string $scope ) : bool|string
	{
		
		$curl_handle = curl_init();
		
		curl_setopt($curl_handle, CURLOPT_RETURNTRANSFER, true);
		curl_setopt($curl_handle, CURLOPT_SSL_VERIFYHOST, 2);
		curl_setopt($curl_handle, CURLOPT_SSL_VERIFYPEER, false);
		
		$URL = $this->config->getAPIUrl().'oauth2/token';
		
		curl_setopt($curl_handle, CURLOPT_URL, $URL );
		curl_setopt($curl_handle, CURLOPT_CUSTOMREQUEST, 'POST');
		
		curl_setopt($curl_handle, CURLOPT_HTTPAUTH, CURLAUTH_BASIC);
		curl_setopt($curl_handle, CURLOPT_USERPWD, $this->config->getClientID().':'.$this->config->getClientSecret());
		
		curl_setopt($curl_handle, CURLOPT_HTTPHEADER, [
			'Accept: application/json',
			'Content-Type: application/x-www-form-urlencoded',
		]);
		
		curl_setopt($curl_handle, CURLOPT_POSTFIELDS, http_build_query([
			'scope' => $scope,
			'grant_type' => 'client_credentials'
		]));
		
		
		$this->logger?->step('Getting auth token');
		
		$response = curl_exec($curl_handle);
		
		$response_data = json_decode(trim( $response ), true);
		
		if(empty($response_data['access_token'])) {
			$this->logger?->step('Token gain error. Response: '.$response);
			$this->logger?->doneError( static::ERROR_AUT_TOKEN_GET_FAILED, $response );
			
			return false;
		}
		
		curl_close($curl_handle);
		
		$token = $response_data['access_token'];
		
		return $token;
	}
	
	public function createPayment(
		GoPay_Order $order,
		string      $return_url,
		string      $notification_url,
		string      $payment_method,
		?string     $selected_bank = ''
	): ?GoPay_CreatedPayment
	{
		$this->logger?->start('create payment start: '.$payment_method.':'.$order->getOderNumber() );
		
		
		$token = $this->getAuthToken('payment-create');
		
		if(!$token) {
			return null;
		}
		
		
		$this->logger?->step('Creating payment ...');
		
		//--------------------------------------------------------------
		$curl_handle = curl_init();
		curl_setopt($curl_handle, CURLOPT_RETURNTRANSFER, true);
		curl_setopt($curl_handle, CURLOPT_SSL_VERIFYHOST, 2);
		curl_setopt($curl_handle, CURLOPT_SSL_VERIFYPEER, false);
		
		
		$URL = $this->config->getAPIUrl().'payments/payment';
		
		curl_setopt($curl_handle, CURLOPT_URL, $URL );
		curl_setopt($curl_handle, CURLOPT_CUSTOMREQUEST, 'POST');
		curl_setopt($curl_handle, CURLOPT_HTTPHEADER, [
			'Accept: application/json',
			'Content-Type: application/json',
			'Authorization: Bearer '.$token
		]);
		
		$payment_data = [
			'target' => [
				'type' => 'ACCOUNT',
				'goid' => $this->config->getGoID(),
			],
			'payer' => [
				//"allowed_payment_instruments" => array_keys(static::`$allowed_payment_methods`),
				"allowed_payment_instruments" => [$payment_method],
				"default_payment_instrument" => $payment_method,
				'contact' => [
					"first_name" => $order->getFirstname(),
					"last_name" => $order->getLastname(),
					"email" => $order->getEmail(),
					"phone_number" => $order->getPhoneNumber(),
					"city" => $order->getCity(),
					"street" => $order->getStreet(),
					"postal_code" => $order->getPostalCode(),
					"country_code" => $order->getCountryCode()
				
				]
			],
			'amount' => $order->getAmount()*100,
			'currency' => 'CZK',
			'order_number' => $order->getOderNumber(),
			'order_description' => $order->getDescription(),
			'callback' => [
				'return_url' => $return_url,
				'notification_url' => $notification_url
			],
		];
		
		if($selected_bank) {
			unset($payment_data['payer']['default_payment_instrument']);
			$payment_data['payer']['allowed_payment_instruments'] = [GoPay_PaymentMethod::BANK_ACCOUNT];
			$payment_data['payer']['allowed_swifts'] = [$selected_bank];
			$payment_data['payer']['default_swift'] = $selected_bank;
		}
		
		$payment_data['items'] = [];
		
		foreach( $order->getItems() as $item ) {
			$payment_data['items'][] = [
				'name' => $item->getName(),
				'count' => $item->getCount(),
				'amount' => $item->getAmount()*100
			];
		}
		
		
		$this->logger?->step('Payment data: '.json_encode($payment_data));
		
		curl_setopt($curl_handle, CURLOPT_POSTFIELDS, json_encode($payment_data));
		
		$response = curl_exec($curl_handle);
		
		$response_data = json_decode(trim( $response ), true);

		curl_close($curl_handle);
		
		if( empty($response_data['gw_url'])) {
			
			$this->logger?->step('Error creating payment. Response: '.$response);
			
			$this->logger?->doneError( static::ERROR_PAYMENT_CREATE_FAILED, $response, $payment_data  );
			
			return null;
		}
		
		$result = new GoPay_CreatedPayment();
		$result->setPaymentId( $response_data['id'] );
		$result->setURL( $response_data['gw_url'] );
		
		
		$this->logger?->step('Payment create. ID:'.$response_data['id'].' URL:'.$URL);
		$this->logger?->doneSuccess();
		
		return $result;
	}
	
	
	public function verifyPayment( PaymentPair $payment ) : bool|null
	{
		$payment_id = $payment->getPaymentId();
		
		$this->logger?->start('verify payment start: '.$payment_id );
		
		$token = $this->getAuthToken('payment-all');
		if(!$token) {
			return null;
		}
		
		$this->logger?->step('Getting payment information...');
		
		$curl_handle = curl_init();
		curl_setopt($curl_handle, CURLOPT_RETURNTRANSFER, true);
		curl_setopt($curl_handle, CURLOPT_SSL_VERIFYHOST, 2);
		curl_setopt($curl_handle, CURLOPT_SSL_VERIFYPEER, false);
		
		
		$URL = $this->config->getAPIUrl().'payments/payment/'.$payment_id;
		
		curl_setopt($curl_handle, CURLOPT_URL, $URL );
		curl_setopt($curl_handle, CURLOPT_CUSTOMREQUEST, 'GET');
		curl_setopt($curl_handle, CURLOPT_HTTPHEADER, [
			'Accept: application/json',
			'Content-Type: application/x-www-form-urlencoded',
			'Authorization: Bearer '.$token
		]);
		
		$response = curl_exec($curl_handle);
		
		$response_data = json_decode(trim( $response ), true);
		curl_close($curl_handle);
		
		
		if(empty($response_data['state'])) {
			
			$this->logger->step('Error getting payment information. Response:'.$response);
			$this->logger->doneError( static::ERROR_PAYMENT_GET_FAILED, $response  );
			
			return null;
			
		}
		
		$this->logger->step('OK - Payment status:: '.$response_data['state']);
		$this->logger->doneSuccess();
		
		$payment->setPaymentStatus( $response_data['state'] );
		
		if($response_data['state']!='PAID') {
			return false;
		}
		
		return true;
	}
	
	public function checkPayments() : void
	{
		$order_ids = Order::dataFetchCol(select: ['id'], where: [
			EShops::getCurrent()->getWhere(),
			'AND',
			'paid' => false,
			'AND',
			'date_purchased <=' => new Data_DateTime('-2 day')
		]);
		
		if(!$order_ids) {
			return;
		}
		
		$payments = PaymentPair::fetch([''=>[
			'payment_status' => [
				'',
				'CREATED',
				'PAYMENT_METHOD_CHOSEN',
			],
			'AND',
			'order_id' => $order_ids
		]]);
		
		foreach($payments as $payment) {
			echo $payment->getOrderId()." : ".$payment->getPaymentId()."\n";
			
			if($this->verifyPayment( $payment )) {
				$order = Order::get( $payment->getOrderId() );
				$order?->paid();
			}
			
			echo "\t".$payment->getPaymentStatus()."\n";
		}
	}
	
}
