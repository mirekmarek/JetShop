<?php
/**
 * @copyright Copyright (c) Miroslav Marek <mirek.marek@web-jet.cz>
 * @license EUPL 1.2  https://eupl.eu/1.2/en/
 * @author Miroslav Marek <mirek.marek@web-jet.cz>
 */
namespace JetApplicaTionModule\MarketplaceIntegration\Heureka;


use Jet\Data_DateTime;

class Client {

	protected Config_PerShop $config;
	
	protected const METHOD_POST = 'POST';
	protected const METHOD_GET = 'GET';
	protected const METHOD_DELETE = 'DELETE';
	protected const METHOD_PUT = 'PUT';
	
	protected const HTTP_STATUS_OK = 200;
	protected const HTTP_STATUS_CREATED = 201;
	protected const HTTP_STATUS_ACCEPTED = 202;
	protected const HTTP_STATUS_NO_CONTENT = 204;
	
	protected string $last_error_message = '';
	protected int $response_status = 0;
	protected mixed $response_data = null;
	
	
	public function __construct( Config_PerShop $config ) {
		$this->config = $config;
	}
	
	
	public function commonRestRequest($URL, $method, $post_data = [], $get_data = []) : bool
	{
		$headers = [];
		
		if($get_data) {
			$URL .= '?'.http_build_query($get_data);
		}
		
		
		$this->last_error_message = '';
		
		
		$curl_handle = curl_init();
		
		curl_setopt($curl_handle, CURLOPT_URL, $URL );
		
		$post_data = http_build_query($post_data);
		
		switch ($method) {
			case static::METHOD_DELETE:
				curl_setopt($curl_handle, CURLOPT_CUSTOMREQUEST, static::METHOD_DELETE);
				break;
			case static::METHOD_POST:
				curl_setopt($curl_handle, CURLOPT_POST, true);
				curl_setopt($curl_handle, CURLOPT_POSTFIELDS, $post_data);
				break;
			case static::METHOD_PUT:
				$handle = fopen('php://temp', 'w+');
				fwrite($handle, $post_data );
				rewind($handle);
				$f_stat = fstat($handle);
				curl_setopt($curl_handle, CURLOPT_PUT, true);
				curl_setopt($curl_handle, CURLOPT_INFILE, $handle);
				curl_setopt($curl_handle, CURLOPT_INFILESIZE, $f_stat['size']);
				break;
			case static::METHOD_GET:
				curl_setopt($curl_handle, CURLOPT_HTTPGET, true);
				break;
		}
		
		curl_setopt($curl_handle, CURLOPT_RETURNTRANSFER, true);
		curl_setopt($curl_handle, CURLOPT_HTTPHEADER, $headers);
		curl_setopt($curl_handle, CURLOPT_SSL_VERIFYHOST, false);
		curl_setopt($curl_handle, CURLOPT_SSL_VERIFYPEER, false);
		
		$this->response_data = curl_exec($curl_handle);
		
		$this->response_status = curl_getinfo($curl_handle, CURLINFO_HTTP_CODE);
		
		
		$message = '';
		
		$curl_error_no = curl_errno($curl_handle);
		$curl_error = curl_error($curl_handle);
		curl_close($curl_handle);

		
		if($this->response_data===false) {
			$this->last_error_message = 'CURL_ERR:' . $curl_error_no . ' - ' . $curl_error;
			$this->response_data = null;
			
			return false;
		}
		
		
		if($this->response_status==static::HTTP_STATUS_NO_CONTENT) {
			return true;
		}
		
		$response_data = json_decode($this->response_data, true);
		
		if(!is_array($response_data)) {
			$this->last_error_message = "Unknown response: HTTP status: ".$this->response_status.", Response: " . $response_data;
			
			return false;
		}
		
		$this->response_data = $response_data;
		
		if(!in_array($this->response_status, [
			static::HTTP_STATUS_OK,
			static::HTTP_STATUS_CREATED,
			static::HTTP_STATUS_ACCEPTED
		])) {
			$this->last_error_message = "Http status error: ".$this->response_status.", message: " . $message;
			return false;
		}
		
		
		return true;
		
	}
	
	public function heurekaRestRequest( string $method, string $action, array $post_data = [], array$get_data = [] ) : bool
	{
		$api_url = rtrim( $this->config->getApiUrl(), '/' );
		
		return $this->commonRestRequest(
			$api_url.'/'.$action,
			$method,
			$post_data,
			$get_data
		);
	}
	
	public function getHeurekaOrderPaymentStatus( string $order_id ) : bool|int
	{
		if( !$this->heurekaRestRequest(static::METHOD_GET, 'payment/status', [], ['order_id'=>$order_id]) ) {
			return false;
		}
		
		return $this->response_data['status'];
	}
	
	
	public function getHeurekaOrderStatus( string$order_id ) : array|bool
	{
		if( !$this->heurekaRestRequest( static::METHOD_GET, 'order/status', [], ['order_id'=>$order_id]) ) {
			return false;
		}
		
		return $this->response_data;
	}
	
	
	public function putHeurekaPaymentStatus( string $order_id, int $status, string|Data_DateTime $date ) : bool
	{
		if(is_object($date)) {
			$date = $date->toString();
		}
		
		$data = [
			'order_id' => $order_id,
			'status' => $status,
			'date' => $date
		];
		
		return $this->heurekaRestRequest(static::METHOD_PUT, 'payment/status', $data, []);
	}
	
	public function putHeurekaOrderStatus( string $order_id, int $heureka_status_id, string $note='', string $tracking_url='', string $expect_delivery='' ) : bool 
	{
		
		$data = [
			'order_id' => $order_id,
			'status' => $heureka_status_id
		];
		
		$transport = [];
		if($tracking_url) {
			$transport['tracking_url'] = $tracking_url;
		}
		if($note) {
			$transport['note'] = $note;
		}
		
		if($expect_delivery) {
			$transport['expectDelivery'] = $expect_delivery;
		}
		
		if($transport) {
			$data['transport'] = $transport;
		}
		
		return $this->heurekaRestRequest(static::METHOD_PUT, 'order/status', $data, []);
	}
	
	
	public function postHeurekaOrderNote( string $order_id, string $note ) : bool 
	{
		
		$data = [
			'order_id' => $order_id,
			'note' => $note
		];
		
		return $this->heurekaRestRequest(static::METHOD_POST, 'order/note', $data, []);
	}
	
	
	public function getLastErrorMessage() : string
	{
		return $this->last_error_message;
	}
	
	public function getResponseStatus() : int
	{
		return $this->response_status;
	}
	
	public function getResponseData() : array|null
	{
		return $this->response_data;
	}
	
	
}