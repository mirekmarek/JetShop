<?php
namespace JetApplication;

require __DIR__.'/../application/bootstrap_cli_service.php';

class client
{
	public const METHOD_POST = 'POST';
	public const METHOD_GET = 'GET';
	public const METHOD_DELETE = 'DELETE';
	public const METHOD_PUT = 'PUT';
	
	public const HTTP_STATUS_OK = 200;
	public const HTTP_STATUS_CREATED = 201;
	public const HTTP_STATUS_ACCEPTED = 202;
	public const HTTP_STATUS_NO_CONTENT = 204;
	
	protected string $last_request_URL = '';
	protected string $last_request_method = '';
	protected string $last_error_message = '';
	protected int $response_status = 0;
	protected mixed $response_data = null;
	
	public function get( string $URL, array $get_data=[] ) : bool
	{
		return $this->commonRequest( $URL, static::METHOD_GET, get_data: $get_data );
	}
	
	public function post( string $URL, array $get_data=[], array $post_data=[] ) : bool
	{
		return $this->commonRequest( $URL, static::METHOD_POST, post_data: $post_data, get_data: $get_data );
	}
	
	public function put( string $URL, array $get_data=[], array $post_data=[] ) : bool
	{
		return $this->commonRequest( $URL, static::METHOD_PUT, get_data: $get_data );
	}
	
	public function commonRequest( string $URL, string $method, array $post_data = [], array $get_data = [] ) : bool
	{
		
		
		$this->last_request_method = $method;
		$this->last_error_message = '';
		
		$headers = [];
		
		if($get_data) {
			$URL .= '?'.http_build_query($get_data);
		}
		
		$this->last_request_URL = $URL;
		
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
		
		if($this->response_data===false) {
			$this->last_error_message = 'CURL_ERR:' . curl_errno($curl_handle) . ' - ' . curl_error($curl_handle);
			$this->response_data = null;
			
			curl_close($curl_handle);
			return false;
		}
		
		curl_close($curl_handle);
		
		if($this->response_status==static::HTTP_STATUS_NO_CONTENT) {
			return true;
		}
		
		$response_data = static::fromJson($this->response_data);
		
		if(!is_array($response_data)) {
			$this->last_error_message = "Error: HTTP status: ".$this->response_status.", Response data: " . $response_data;
			return false;
		}
		
		$this->response_data = $response_data;
		
		if(!in_array($this->response_status, [
			static::HTTP_STATUS_OK,
			static::HTTP_STATUS_CREATED,
			static::HTTP_STATUS_ACCEPTED,
		])) {
			$this->last_error_message = "http error: ".$this->response_status.", message: " . $message;
			
			return false;
		}
		
		return true;
	}
	
	public static function fromJson( $str ) : array|false
	{
		
		$result = json_decode($str, true);
		
		if(!is_array($result)) {
			return false;
		}
		
		return $result;
	}
	
	public function getLastErrorMessage(): string
	{
		return $this->last_error_message;
	}
	
	public function getResponseStatus(): int
	{
		return $this->response_status;
	}
	
	public function getResponseData(): mixed
	{
		return $this->response_data;
	}
	
	
	public function debug() : void
	{
		echo $this->last_request_method.': '.$this->last_request_URL.PHP_EOL.PHP_EOL;
		
		var_dump(
			$this->getResponseStatus(),
			$this->getResponseData()
		);
	}
	
}


$base_URL = 'http://jet-shop.lc/services/7bc9d613f0a18c098ff166b6e02441ea6d0c8e77/MarketplaceIntegration.Heureka:mp_server_cz_cs_CZ/';

$products =  [
	[
		'id' => '59956', //not avl
		'count' => 10
	],
	[
		'id' => '59958', //avl
		'count' => 1
	],
	[
		'id' => '59966', //avl - limited
		'count' => 10
	]
];


$client = new client();
$availability = $client->get( $base_URL.'products/availability', ['products'=>$products] );

if(!$availability) {
	$client->debug();
	die();
}

$products = [];

foreach($client->getResponseData()['products'] as $sri) {
	if(!$sri['available']) {
		continue;
	}
	
	$products[] = [
		'id' => $sri['id'],
		'count' => $sri['count'],
		'price' => $sri['price'],
		'totalPrice' => $sri['price']*$sri['count'],
	];
}

$productsTotalPrice = 0;
foreach($products as $pr) {
	$productsTotalPrice += $pr['totalPrice'];
}



$delivery_payment = $client->get( $base_URL.'payment/delivery', ['products'=>$products] );

if(!$delivery_payment) {
	$client->debug();
	die();
}

$delivery_data = $client->getResponseData();

$payment_transport_binding = $delivery_data['binding'][0];

$transports = $delivery_data['transport'];
$payments = $delivery_data['payment'];

$transportId = $payment_transport_binding['transportId'];
$paymentId = $payment_transport_binding['paymentId'];

$transport = null;
foreach($transports as $tr) {
	if($tr['id']==$transportId) {
		$transport = $tr;
		break;
	}
}

$payment = null;
foreach($payments as $pm) {
	if($pm['id']==$paymentId) {
		$payment = $pm;
		break;
	}
}



$order_data = [
	'products' => $products,
	'productsTotalPrice' => $productsTotalPrice,
	'heureka_id' => time(),
	'deliveryId' => $transport['id'],
	'deliveryPrice' => $transport['price'],
	'paymentId' => $payment['id'],
	'paymentPrice' => $payment['price'],
	
	'note' => 'Poznámka k heureka objednávce '.date('Y-m-d H:i:s'),
	'customer' => [
		'firstname' => 'TestJméno',
		'lastname' => 'TestPříjmení',
		'email' => 'mirek.marek.2m@gmail.com',
		'phone' => '+420123456789',
		'street' => 'TestUlice1',
		'city' => 'TestMěsto',
		'postCode' => '12345',
		'state' => 'TestStát',
		'company' => 'TestFirma',
		'ic' => '987654321',
		'dic' => 'CZ987654321'
	],
	'deliveryAddress' => [
		'firstname' => 'DeliveryTestJméno',
		'lastname' => 'DeliveryTestPříjmení',
		'street' => 'DeliveryTestUlice1',
		'city' => 'DeliveryTestMěsto',
		'postCode' => '12345',
		'state' => 'DeliveryTestStát',
		'company' => 'DeliveryTestFirma',
	
	]
];


$client->post( $base_URL.'order/send', post_data: $order_data );
$client->debug();

