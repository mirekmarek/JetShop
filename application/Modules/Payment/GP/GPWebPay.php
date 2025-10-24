<?php
/**
 * @copyright Copyright (c) Miroslav Marek <mirek.marek@web-jet.cz>
 * @license EUPL 1.2  https://eupl.eu/1.2/en/
 * @author Miroslav Marek <mirek.marek@web-jet.cz>
 */
namespace JetApplicationModule\Payment\GP;


use Jet\Exception;
use Jet\Http_Headers;
use Jet\Logger;
use JetApplication\Order;
use SoapClient;
use SoapFault;

class GPWebPay {
	
	protected Config_PerShop $config;
	
	public function __construct( Config_PerShop $config )
	{
		$this->config = $config;
	}
	
	
	protected function getGpUrlData( Order $order, string $return_url ) : array
	{

		$orders_id = $order->getId();
		$return_path = $return_url;
		
		$amount = round( $order->getTotalAmount_WithVAT() * 100 );
		$deposit_flag = 1;
		$operation = 'CREATE_ORDER';
		
		$currency = GPWebPay_Currencies::getList()[$order->getCurrency()->getCode()];
		
		$gp_url_data = [
			'MERCHANTNUMBER' => $this->config->getMerchantNumber(),
			'OPERATION'      => $operation,
			'ORDERNUMBER'    => $orders_id,
			'AMOUNT'         => $amount,
			'CURRENCY'       => $currency,
			'DEPOSITFLAG'    => $deposit_flag,
			'URL'            => $return_path,
			'PAYMETHOD'      => $order->getPaymentMethod()->getBackendModulePaymentMethodSpecification(),
		];
		
		$digest = $this->signData( $gp_url_data );
		
		
		$gp_url_data["DIGEST"] = $digest;
			

		
		return $gp_url_data;
	}
	
	public function getGPURL( Order $order, string $return_url ) : string
	{
		return $this->config->getAPIURL() . http_build_query( $this->getGpUrlData( $order, $return_url ) );
		
	}
	
	protected function signData( array $data, bool $encode = true ) : string
	{
		$key = $this->config->getPrivateKey();
		$pass = $this->config->getKeyPassword();
		
		
		$pkeyid = openssl_get_privatekey( $key, $pass );
		if(!$pkeyid) {
			throw new Exception('GP Web Pay: Incorrect key and/or key password');
		}
		
		$signature = '';
		openssl_sign( implode( '|', $data), $signature, $pkeyid );
		//openssl_free_key($pkeyid);
		
		if($encode) {
			$signature = base64_encode( $signature );
		}
		
		return $signature;
	}
	
	
	public function process( Order $order, string $return_url ) : void
	{
		$URL = $this->getGPURL( $order, $return_url );
		
		Http_Headers::movedTemporary( $URL );
	}
	
	public function handleReturn( Order $order ) : bool
	{
		
		return $this->verifyPayment( $order );
	}
	
	protected function getSoap() : SoapClient
	{
		$soap = new SoapClient(
			__DIR__ . '/GPWebPay/WSDL/' . $this->config->getWSDLFileName(),
			[
				'location' => $this->config->getSOAPEndpoint(),
				'trace' => 1,
			]
		);
		
		return $soap;
	}
	
	
	public function verifyPayment( Order $order ) : ?bool
	{
		if( $order->getPaid() ) {
			return true;
		}
		
		$soap = $this->getSoap();
		
		$params = [
			'messageId' => md5(uniqid('', true)),
			'provider' => $this->config->getPaymentProvider(),
			'merchantNumber' => $this->config->getMerchantNumber(),
			'paymentNumber' => $order->getId(),
		];
		
		
		$params['signature'] = static::signData( $params, false );
		
		try {
			/** @noinspection PhpUndefinedMethodInspection */
			$result = $soap->getPaymentStatus(
				[
					'paymentStatusRequest' => $params
				]
			);
		} catch(SoapFault $e) {
			$result = null;
		}
		
		
		if(
			!is_object( $result ) ||
			!isset($result->paymentStatusResponse)
		) {
			
			Logger::danger(
				event: 'payment_GP_error',
				event_message: 'GP payment error',
				context_object_data: [
					'request'  => $soap->__getLastRequest(),
					'response' => $soap->__getLastResponse(),
				]
			);
			
			return null;
		}
		
		$result = $result->paymentStatusResponse;
		
		if( $result->state != 7 ) {
			return false;
		}
		
		return true;
	}
	
}