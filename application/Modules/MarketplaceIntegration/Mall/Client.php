<?php
/**
 * @copyright Copyright (c) Miroslav Marek <mirek.marek@web-jet.cz>
 * @license EUPL 1.2  https://eupl.eu/1.2/en/
 * @author Miroslav Marek <mirek.marek@web-jet.cz>
 */
namespace JetApplicationModule\MarketplaceIntegration\Mall;


class Client {
	public const METHOD_POST = 'POST';
	public const METHOD_GET = 'GET';
	public const METHOD_DELETE = 'DELETE';
	public const METHOD_PUT = 'PUT';
	
	public const HTTP_STATUS_OK = 200;
	public const HTTP_STATUS_CREATED = 201;
	public const HTTP_STATUS_ACCEPTED = 202;
	public const HTTP_STATUS_NO_CONTENT = 204;
	
	protected Config_PerShop $config;
	
	protected int $last_response_status = 0;
	protected string $last_error_message = '';
	protected mixed $last_response_data = null;
	protected array $errors = [];
	
	protected bool $error_during_transaction = false;
	protected string $transaction_error_message = '';
	
	public function __construct( Config_PerShop $config ) {
		$this->config = $config;
	}
	
	public function getObject( string $object_type, string $object_ID, array $params=[] ) : mixed
	{
		if(!$this->_exec(self::METHOD_GET, $object_type.'/'.$object_ID, array(), $params)) {
			return false;
		}
		
		if(!isset($this->last_response_data['data'])) {
			$this->last_response_data['data'] = array();
		}
		
		if(!isset($this->last_response_data['paging'])) {
			return $this->last_response_data['data'];
		}
		
		$paging = $this->last_response_data['paging'];
		$result = $this->last_response_data['data'];
		
		for( $p=2; $p<=$paging['pages']; $p++ ) {
			
			$params['page'] = $p;
			
			if(!$this->_exec(self::METHOD_GET, $object_type.'/'.$object_ID, array(), $params)) {
				return false;
			}
			foreach( $this->last_response_data['data'] as $k=>$value ) {
				if(is_int($k)) {
					$result[] = $value;
				} else {
					if(is_array($result[$k])) {
						$result[$k] = array_merge( $result[$k], $value );
					}
					
				}
			}
			
		}
		
		return $result;
	}
	
	public function deleteObject( string $object_type, string $object_ID, array $params=[] ) : bool
	{
		return $this->_exec(self::METHOD_DELETE, $object_type.'/'.$object_ID, array(), $params);
	}
	
	public function addObject( string $object_type, array $object_data, array $params=[] ) : bool
	{
		return $this->_exec(self::METHOD_POST, $object_type, $object_data, $params);
	}
	
	public function updateObject( string $object_type, string|int $object_ID, array $object_data, array $params=[] ) : bool
	{
		return $this->_exec(self::METHOD_PUT, $object_type.'/'.$object_ID, $object_data, $params);
	}
	
	public function getLastErrorMessage() : string
	{
		return $this->last_error_message;
	}
	
	public function getLastResponseData() : mixed
	{
		return $this->last_response_data;
	}
	
	public function getLastResponseStatus() : int
	{
		return $this->last_response_status;
	}
	
	
	
	
	protected  function _exec( string $method, string $object, array $data = [], array $params = []) : bool
	{
		if($this->config->getTestMode()) {
			$params['test'] = 1;
		}
		
		$headers = [];
		
		if( str_ends_with( $object, '/' ) ) {
			$object = substr($object, 0, -1);
		}
		
		$this->last_error_message = '';
		$this->last_response_status = 0;
		
		$params['client_id'] = $this->config->getClientId();
		
		$URL = $this->config->getApiUrl().$object.'?' . http_build_query($params);
		
		$curl_handle = curl_init();
		
		curl_setopt($curl_handle, CURLOPT_URL, $URL );
		
		$data = $this->_toJson($data);
		
		switch ($method) {
			case self::METHOD_DELETE:
				curl_setopt($curl_handle, CURLOPT_CUSTOMREQUEST, self::METHOD_DELETE);
				break;
			case self::METHOD_POST:
				curl_setopt($curl_handle, CURLOPT_POST, true);
				curl_setopt($curl_handle, CURLOPT_POSTFIELDS, $data);
				$headers[] = 'Content-Type: application/json';
				$headers[] = 'Expect:';
				break;
			case self::METHOD_PUT:
				$handle = fopen('php://temp', 'w+');
				fwrite($handle, $data );
				rewind($handle);
				$f_stat = fstat($handle);
				curl_setopt($curl_handle, CURLOPT_PUT, true);
				curl_setopt($curl_handle, CURLOPT_INFILE, $handle);
				curl_setopt($curl_handle, CURLOPT_INFILESIZE, $f_stat['size']);
				$headers[] = 'Content-Type: application/json';
				$headers[] = 'Expect:';
				break;
			case self::METHOD_GET:
				curl_setopt($curl_handle, CURLOPT_HTTPGET, true);
				break;
		}
		
		curl_setopt($curl_handle, CURLOPT_RETURNTRANSFER, true);
		curl_setopt($curl_handle, CURLOPT_HTTPHEADER, $headers);
		curl_setopt($curl_handle, CURLOPT_SSL_VERIFYHOST, 2);
		curl_setopt($curl_handle, CURLOPT_SSL_VERIFYPEER, false);
		
		$response_data = curl_exec($curl_handle);
		$response_status = curl_getinfo($curl_handle, CURLINFO_HTTP_CODE);
		
		$this->last_response_status = $response_status;
		
		$backtrace = '';
		$message = '';
		
		
		if($response_data===false) {
			$this->last_error_message = 'CURL_ERR:' . curl_errno($curl_handle) . ' - ' . curl_error($curl_handle);
			$this->last_response_data = null;
			
			curl_close($curl_handle);
			return false;
		} else {
			if($response_status==self::HTTP_STATUS_NO_CONTENT) {
				curl_close($curl_handle);
				return true;
			}
			
			$this->last_response_data = $this->_fromJson($response_data);
			
			if(!is_array($this->last_response_data)) {
				$this->last_error_message = "Unknown response: HTTP status: {$response_status}, Response data: " . $response_data;
				
				curl_close($curl_handle);
				return false;
			} else {
				if(isset($this->last_response_data['result']['message'])) {
					$message = $this->last_response_data['result']['message'];
					
					$this->errors[] =
						[
							'URL' => $method.':'.$URL,
							'message' => $message
						];
				}
				
				switch ($response_status) {
					case self::HTTP_STATUS_OK:
					case self::HTTP_STATUS_CREATED:
					case self::HTTP_STATUS_ACCEPTED:
						curl_close($curl_handle);
						return true;
					default:
						$this->last_error_message = "http error: {$response_status}, message: " . $message;
						curl_close($curl_handle);
						return false;
				}
				
			}
			
		}
		
	}
	
	protected function _toJson( array $data ) : string
	{
		return json_encode($data);
	}
	
	protected function _fromJson( string $str ) : array|bool
	{
		$result = json_decode($str, true);
		
		if(!is_array($result)) {
			return false;
		}
		
		return $result;
	}
	
	public function transactionError( string $error_message ) : void
	{
		$this->error_during_transaction = true;
		$this->transaction_error_message .= $error_message.PHP_EOL;
	}
	
	public function getErrorDuringTransaction() : bool
	{
		return $this->error_during_transaction;
	}
	
	public function getTransactionErrorMessage() : string
	{
		return $this->transaction_error_message;
	}
	
	public function getErrors() : array
	{
		return $this->errors;
	}
	
}