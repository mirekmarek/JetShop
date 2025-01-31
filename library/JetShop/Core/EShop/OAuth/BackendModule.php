<?php
/**
 * @copyright Copyright (c) Miroslav Marek <mirek.marek@web-jet.cz>
 * @license EUPL 1.2  https://eupl.eu/1.2/en/
 * @author Miroslav Marek <mirek.marek@web-jet.cz>
 */
namespace JetShop;


use Jet\Application_Module;
use Jet\Factory_MVC;
use Jet\Http_Request;
use JetApplication\EShop_OAuth_UserHandler;
use JetApplication\EShop;

/**
 *
 */
abstract class Core_EShop_OAuth_BackendModule extends Application_Module
{
	public const METHOD_POST = 'POST';
	public const METHOD_GET = 'GET';
	
	public const HTTP_STATUS_OK = 200;
	public const HTTP_STATUS_CREATED = 201;
	public const HTTP_STATUS_ACCEPTED = 202;
	public const HTTP_STATUS_NO_CONTENT = 204;
	
	protected int $last_response_status = 0;
	
	protected string $last_error_message = '';
	
	protected mixed $last_response_data=null;

	
	protected string $handler_url;
	
	
	
	public function getOAuthServiceID(): string
	{
		return static::OAUTH_SERVICE_ID;
	}
	
	abstract protected function getClientId( ?EShop $eshop=null ) : string;
	
	abstract protected function getClientSecret( ?EShop $eshop=null ) : string;
	
	abstract protected function getOAuthURL( ?EShop $eshop=null ) : string;
	
	abstract protected function getTokenURL( ?EShop $eshop=null ) : string;
	
	public function isConfigured( ?EShop $eshop=null ) : bool
	{
		return (
			$this->getClientId( $eshop ) &&
			$this->getClientSecret( $eshop ) &&
			$this->getOAuthURL( $eshop ) &&
			$this->getTokenURL( $eshop )
		);
	}
	
	public function getOAuthServiceAuthorizationLink() : string
	{
		$data = [
			'client_id'     => $this->getClientId(),
			'redirect_uri'  => $this->getHandlerUrl(),
			'scope'         => 'email',
			'response_type' => 'code'
		];
		
		return  $this->getOAuthURL().'?'.http_build_query($data);
	}
	
	
	public function handleOAuthServiceReturn( EShop_OAuth_UserHandler $user_handler ) : bool
	{
		
		$GET = Http_Request::GET();
		if( !$GET->exists('code') ) {
			return false;
		}
		
		$post_data = [
			'code'          => $GET->getString('code'),
			'client_id'     => $this->getClientId(),
			'client_secret' => $this->getClientSecret(),
			'redirect_uri'  => $this->getHandlerUrl(),
			'grant_type'    => 'authorization_code'
		];
		
		
		if($this->_request(
			static::METHOD_POST,
			$this->getTokenURL(),
			$post_data
		)) {
			return $this->handleTokenResponse( $user_handler );
		}
		
		return false;
	}
	
	public function getHandlerUrl(): string
	{
		return $this->handler_url;
	}
	
	public function setHandlerUrl( string $url ): void
	{
		$this->handler_url = $url;
	}
	
	public function _renderLoginButton() : string
	{
		if(!$this->isConfigured()) {
			return '';
		}
		
		$view = Factory_MVC::getViewInstance( $this->getViewsDir() );
		
		$view->setVar('module', $this);
		
		return $view->render('button');
	}
	
	protected function _request( string $method, string $URL, array $post_data = [], string $auth_token='') : bool
	{
		
		$this->last_error_message = '';
		$this->last_response_status = 0;
		
		$curl_handle = curl_init();
		
		$headers = [
			'Accept: application/json',
			'User-Agent: CURL',
		];
		if($auth_token) {
			$headers[] = 'Authorization: Bearer ' . $auth_token;
		}
		
		curl_setopt($curl_handle, CURLOPT_URL, $URL );
		curl_setopt($curl_handle, CURLOPT_HTTPHEADER, $headers);
		
		switch ($method) {
			case self::METHOD_POST:
				curl_setopt($curl_handle, CURLOPT_POST, true);
				curl_setopt($curl_handle, CURLOPT_POSTFIELDS, $post_data);
				break;
			case self::METHOD_GET:
				curl_setopt($curl_handle, CURLOPT_HTTPGET, true);
				break;
		}
		
		curl_setopt($curl_handle, CURLOPT_RETURNTRANSFER, true);
		
		$response_data = curl_exec($curl_handle);
		$response_status = curl_getinfo($curl_handle, CURLINFO_HTTP_CODE);
		
		$this->last_response_status = $response_status;
		
		$message = '';
		
		
		if($response_data===false) {
			$this->last_error_message = 'CURL_ERR:' . curl_errno($curl_handle) . ' - ' . curl_error($curl_handle);
			$this->last_response_data = null;
			
			curl_close($curl_handle);
			return false;
		} else {
			
			$this->last_response_data = json_decode($response_data, true);
			
			
			if(!is_array($this->last_response_data)) {
				$this->last_error_message = "Unexpected response: HTTP status: $response_status, Response: $response_data";
				
				curl_close($curl_handle);
				return false;
			} else {
				if(isset($this->last_response_data['result']['message'])) {
					$message = $this->last_response_data['result']['message'];
				}
				
				switch ($response_status) {
					case self::HTTP_STATUS_OK:
					case self::HTTP_STATUS_CREATED:
					case self::HTTP_STATUS_ACCEPTED:
						curl_close($curl_handle);
						return true;
						break;
					default:
						$this->last_error_message = "http error: $response_status, message: " . $message;
						curl_close($curl_handle);
						return false;
				}
				
			}
			
		}
	}
	
}