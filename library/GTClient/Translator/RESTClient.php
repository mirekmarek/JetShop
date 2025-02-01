<?php
/**
 *
 * @copyright Copyright (c) Miroslav Marek <mirek.marek@web-jet.cz>
 * @license
 * @author Miroslav Marek <mirek.marek@web-jet.cz>
 */

namespace GTClient;

class Translator_RESTClient
{
	public const METHOD_POST = 'POST';
	public const METHOD_GET = 'GET';
	
	public const HTTP_STATUS_OK = 200;
	
	protected string $API_URL = '';
	protected string $API_key = '';
	protected string $error_message = '';
	protected string $request = '';
	protected ?array $request_data = null;
	protected string|array $request_body = '';
	protected int $response_status = 0;
	protected string $response_header = '';
	protected string $response_body = '';
	protected array|null $response_data = null;
	
	public function __construct( string $API_URL, string $API_key )
	{
		$this->API_URL = $API_URL;
		$this->API_key = $API_key;
	}
	
	public function exec( string $method,
	                      string $object,
	                      array  $post_data = [],
	                      array  $get_params = []
	): bool
	{
		$headers = [];
		
		if( str_ends_with( $object, '/' ) ) {
			$object = substr( $object, 0, -1 );
		}
		
		$get_params['key'] = $this->API_key;
		
		$URL = $this->API_URL.$object.'?'.http_build_query( $get_params );
		$curl_handle = curl_init();
		
		
		
		curl_setopt( $curl_handle, CURLOPT_URL, $URL );
		
		
		switch( $method ) {
			case self::METHOD_GET:
				curl_setopt( $curl_handle, CURLOPT_HTTPGET, true );
				break;
			case self::METHOD_POST:
				curl_setopt( $curl_handle, CURLOPT_POST, true );
				
				$headers[] = 'Content-Type: application/json';
				
				$this->request_data = $post_data;
				$this->request_body = json_encode( $post_data );
					
				
				curl_setopt( $curl_handle, CURLOPT_POSTFIELDS, $this->request_body );
				$headers[] = 'Expect:';
				break;
		}
		
		curl_setopt( $curl_handle, CURLOPT_RETURNTRANSFER, true );
		curl_setopt( $curl_handle, CURLOPT_VERBOSE, true );
		curl_setopt( $curl_handle, CURLOPT_HEADER, true );
		
		curl_setopt( $curl_handle, CURLOPT_RETURNTRANSFER, true );
		curl_setopt( $curl_handle, CURLINFO_HEADER_OUT, true );
		curl_setopt( $curl_handle, CURLOPT_HTTPHEADER, $headers );
		
		
		$this->response_body = curl_exec( $curl_handle );
		
		$this->request = curl_getinfo( $curl_handle, CURLINFO_HEADER_OUT );
		$this->response_status = curl_getinfo( $curl_handle, CURLINFO_HTTP_CODE );
		
		
		$header_size = curl_getinfo( $curl_handle, CURLINFO_HEADER_SIZE );
		$this->response_header = substr( $this->response_body, 0, $header_size );
		$this->response_body = substr( $this->response_body, $header_size );
		
		
		$result = false;
		
		if( $this->response_data === false ) {
			$this->error_message = 'CURL_ERR:' . curl_errno( $curl_handle ) . ' - ' . curl_error( $curl_handle );
			
		} else {
			
			$this->response_data = json_decode( $this->response_body, true );
			
			if( !is_array( $this->response_data ) ) {
				$this->error_message = 'JSON parse error';
			} else {
				
				switch( $this->response_status ) {
					case self::HTTP_STATUS_OK:
						$result = true;
						break;
					default:
						break;
				}
				
			}
			
		}
		
		curl_close( $curl_handle );
		
		return $result;
	}
	
	public function get( string $object, array $get_params = [] ): bool
	{
		return $this->exec( method: static::METHOD_GET, object: $object, get_params: $get_params );
	}
	
	
	public function post( string $object, array $data, array $get_params = [] ): bool
	{
		return $this->exec(
			method: static::METHOD_POST,
			object: $object,
			post_data: $data,
			get_params: $get_params
		);
	}
	
	public function request(): string
	{
		return $this->request;
	}

	public function requestBody(): string|array
	{
		return $this->request_body;
	}

	public function requestData(): array|null
	{
		return $this->request_data;
	}

	public function errorMessage(): string
	{
		return $this->error_message;
	}

	public function responseStatus(): int
	{
		return $this->response_status;
	}

	public function responseHeader(): string
	{
		return $this->response_header;
	}

	public function responseBody(): string
	{
		return $this->response_body;
	}

	public function responseData(): array|null
	{
		return $this->response_data;
	}
	
}