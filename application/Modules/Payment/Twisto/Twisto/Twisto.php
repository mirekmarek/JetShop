<?php
namespace Twisto;


class Twisto
{
    private string $public_key;
    private string $secret_key;
    private string $api_url = 'https://api.twisto.cz/v2/';

    public function __construct()
    {
    }

    public function setSecretKey( string $key) : void
    {
        $this->secret_key = $key;
    }

    public function setPublicKey( string $key) : void
    {
        $this->public_key = $key;
    }

    public function setApiUrl( string $api_url) : void
    {
        $this->api_url = $api_url;
    }

    protected function encrypt( string $data ) : string
    {
        $bin_key = pack("H*", substr($this->secret_key, 8));
        $aes_key = substr($bin_key, 0, 16);
        $salt = substr($bin_key, 16, 16);
        $iv = openssl_random_pseudo_bytes(16);
        $encrypted = openssl_encrypt($data, 'aes-128-cbc', $aes_key, true, $iv);
        $digest = hash_hmac('sha256', $data . $iv, $salt, true);
        return base64_encode($iv . $digest . $encrypted);
    }

	protected function compress( string $data) : string
    {
        $gz_data = gzcompress($data, 9);
        return pack("N", strlen($gz_data)) . $gz_data;
    }

    public function requestJson(string $method, string $url, ?array $data = null) : mixed
    {
        $response = $this->request($method, $url, $data);

        $json = json_decode($response, true);
        if ($json === null) {
            throw new Error('API responded with invalid JSON');
        }

        return $json;
    }


    public function request(string $method, string $url, ?array $data = null) : mixed
    {
        $curl = curl_init();
        curl_setopt($curl, CURLOPT_HTTPHEADER, array(
            "Content-Type: application/json",
            "Authorization: {$this->public_key},{$this->secret_key}"
        ));
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($curl, CURLOPT_URL, $this->api_url . $url);
        curl_setopt($curl, CURLOPT_CUSTOMREQUEST, $method);
        curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, false);
        curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, false);

        if ($data !== null) {
            curl_setopt($curl, CURLOPT_POSTFIELDS, json_encode($data));
        }

        $response = curl_exec($curl);

        if (curl_errno($curl)) {
            throw new Error('Curl error: ' . curl_error($curl));
        }

        $info = curl_getinfo($curl);
        if ($info['http_code'] != 200) {
            throw new Error('API responded with wrong status code (' . $info['http_code'] . ') '.$response, json_decode($response));
        }

        return $response;
    }

    /**
     *
     * @param Customer $customer
     * @param Order $order
     * @param array<Order> $previous_orders
     * @return string
     */
    public function getCheckPayload(Customer $customer, Order $order, array $previous_orders ) : string
    {
	    $data = array(
		    'random_nonce' => uniqid('', true),
		    'customer' => $customer->serialize(),
		    'order' => $order->serialize(),
		    'previous_orders' => array_map(function (Order $item) {
			    return $item->serialize();
		    }, $previous_orders)
	    );

        $payload = json_encode( $data );

        return $this->encrypt($this->compress($payload));
    }
}
