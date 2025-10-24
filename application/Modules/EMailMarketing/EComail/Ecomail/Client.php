<?php
/**
 * @copyright Copyright (c) Miroslav Marek <mirek.marek@web-jet.cz>
 * @license EUPL 1.2  https://eupl.eu/1.2/en/
 * @author Miroslav Marek <mirek.marek@web-jet.cz>
 */
namespace JetApplicationModule\EMailMarketing\EComail;

class Ecomail_Client {
	const METHOD_GET = 'GET';
	const METHOD_POST = 'POST';
	const METHOD_PUT = 'PUT';
	const METHOD_DELETE = 'DELETE';
	
	protected string $API_URL = 'https://api2.ecomailapp.cz';
	protected string $API_key;
	protected int $list_id;
	
	public function __construct(
		string $API_URL,
		string $API_key,
		int $list_id
	) {
		$this->list_id = $list_id;
		$this->API_key = $API_key;
		$this->API_URL = $API_URL;
	}
	
	public function getListsCollection() : array
	{
		return $this->GET('lists');
	}
	
	public function addListCollection(array $data) : array
	{
		return $this->POST('lists', $data);
	}
	
	public function showList() : array
	{
		return $this->GET( 'lists/'. $this->list_id );
	}
	
	public function updateList( array $data) : array
	{
		return $this->PUT('lists/'.$this->list_id, $data);
	}
	
	public function getSubscribers() : array
	{
		return $this->GET('lists/'.$this->list_id.'/subscribers');
	}
	
	public function getSubscriber( string $email ) : array
	{
		return $this->GET('lists/'.$this->list_id.'/subscriber/'.$email);
	}
	
	public function getSubscriberList( string $email ) : array
	{
		return $this->GET( 'subscribers/'.$email );
	}
	
	public function addSubscriber( array $data) : array
	{
		return $this->POST( 'lists/'.$this->list_id.'/subscribe', $data);
	}
	
	public function addEvent(array $data) : array
	{
		return $this->POST('tracker/events', $data);
	}
	
	
	public function updateSubscriber(string $email, array $data) : array
	{
		$data['email'] = $email;
		
		return $this->PUT('lists/'.$this->list_id.'/update-subscriber', $data);
	}
	
	public function removeSubscriber( array $data ) : array
	{
		return $this->DELETE('lists/'.$this->list_id.'/unsubscribe', $data);
	}
	
	public function addSubscriberBulk( array $data) : array
	{
		return $this->POST('lists/'.$this->list_id.'/subscribe-bulk', $data);
	}
	
	
	public function deleteSubscriber( string $email ) : array
	{
		return $this->DELETE( 'subscribers/'.$email.'/delete' );
	}
	
	public function getSubscriberByEmail( string $email ) : array
	{
		return $this->GET( 'subscribers/'. $email );
	}
	
	public function listCampaigns( ?string $filters = null) : array
	{
		$query = [];
		if($filters!==null) {
			$query = ['filters' => $filters];
		}
		
		return $this->GET('campaigns', $query);
	}
	
	
	protected function GET( string $request, array $url_params = []) : array
	{
		return $this->request(static::METHOD_GET, $request, url_params: $url_params);
	}
	
	protected function POST( string $request, array $data ) : array
	{
		return $this->request(static::METHOD_POST, $request, data: $data );
	}
	
	protected function PUT( string $request, array $data = [] ) : array
	{
		return $this->request( static::METHOD_PUT, $request, data: $data );
	}
	
	protected function DELETE( string $request, array $data = [] ) : array
	{
		return $this->request( static::METHOD_DELETE, $request, data: $data );
	}
	
	protected function request( string $method, string $request, ?array $data=null, array $url_params=[]) : array
	{
		$request_URL = $this->API_URL . '/' . $request . '?' . http_build_query($url_params);
		$headers = [
			'key: ' . $this->API_key
		];
		
		$ch = curl_init();
		curl_setopt($ch, CURLOPT_URL, $request_URL);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
		curl_setopt($ch, CURLOPT_CUSTOMREQUEST, $method );
		
		if (is_array($data)) {
			$headers[] = 'Content-Type: application/json';
			curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($data));
		}

		
		curl_setopt( $ch, CURLOPT_HTTPHEADER, $headers );
		
		$raw_output = $output = curl_exec($ch);
		if($raw_output === false) {
			return [];
		}
		
		
		// Check HTTP status code
		if (!curl_errno($ch)) {
			$http_code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
			$content_type = curl_getinfo($ch, CURLINFO_CONTENT_TYPE);
			$error_message_is_json = $content_type === 'application/json';
			if ($error_message_is_json) {
				$output = json_decode($raw_output, null, 512 );
			}
			if ($http_code < 200 || $http_code > 299) {
				return array(
					'error' => $http_code,
					'message' => $output,
				);
			}
		}
		
		curl_close($ch);
		
		$raw_output = json_decode($raw_output, true);
		
		return $raw_output;
	}
	
}