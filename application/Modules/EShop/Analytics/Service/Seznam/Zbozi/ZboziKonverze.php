<?php
namespace JetApplicationModule\EShop\Analytics\Service\Seznam\Zbozi;

use Error;
use Exception;

/**
 * Provides access to ZboziKonverze service.
 *
 * Example of usage:
 *
 * \code
 * try {
 *     // Initialize
 *     $zbozi = new ZboziKonverze(ID PROVOZOVNY, "TAJNY KLIC");
 *
 *     // Set order details
 *     $zbozi->setOrder(array(
 *         "orderId" => "CISLO OBJEDNAVKY",
 *         "email" => "email@example.com",
 *         "deliveryType" => "CESKA_POSTA",
 *         "deliveryPrice" => 80,
 *         "otherCosts" => 20,
 *         "paymentType" => "dobírka",
 *     ));
 *
 *     // Add bought items
 *     $zbozi->addCartItem(array(
 *         "itemId" => "1357902468",
 *         "productName" => "Samsung Galaxy S3 (i9300)",
 *         "quantity" => 1,
 *         "unitPrice" => 5000.50,
 *     ));
 *
 *     $zbozi->addCartItem(array(
 *         "itemId" => "2468013579",
 *         "productName" => "BARUM QUARTARIS 165/70 R14 81 T",
 *         "quantity" => 4,
 *         "unitPrice" => 600,
 *     ));
 *
 *     // Finally send request
 *     $zbozi->send();
 *
 * } catch (ZboziKonverzeException $e) {
 *     // Error should be handled according to your preference
 *     error_log("Chyba konverze: " . $e->getMessage());
 * }
 * \endcode
 *
 * @author Zbozi.cz <zbozi@firma.seznam.cz>
 */

class ZboziKonverze {

	/**
	 * Endpoint URL
	 */
	const BASE_URL = 'https://%%DOMAIN%%/action/%%SHOP_ID%%/conversion/backend';

	/**
	 * Private identifier of request creator
	 */
	public string $PRIVATE_KEY;

	/**
	 * Public identifier of request creator
	 */
	public string $SHOP_ID;

	/**
	 * Identifier of this order
	 */
	public string $orderId;

	/**
	 * Customer email
	 * Should not be set unless customer allows to do so.
	 */
	public string $email;

	/**
	 * How the order will be transfered to the customer
	 */
	public string $deliveryType;

	/**
	 * Cost of delivery (in CZK)
	 */
	public float $deliveryPrice;

	/**
	 * How the order was paid
	 */
	public string $paymentType;

	/**
	 * Other fees (in CZK)
	 */
	public string $otherCosts;

	/**
	 * Array of CartItem
	 */
	public array $cart = [];

	/**
	 * Determine URL where the request will be send to
	 */
	private bool $sandbox;

	/**
	 * Set if sandbox URL will be used.
	 */
	public function useSandbox( bool $val) : void
	{
		$this->sandbox = $val;
	}

	/**
	 * Check if string is not empty
	 */
	private static function isNullOrEmptyString( ?string $question): bool
	{
		return (!isset($question) || trim($question)==='');
	}

	/**
	 * Initialize ZboziKonverze service
	 *
	 * @param string $shopId Shop identifier
	 * @param string $privateKey Shop private key
	 * @throws ZboziKonverze_Exception can be thrown if \p $privateKey and/or \p $shopId
	 * is missing or invalid.
	 */
	public function __construct( string $shopId, string $privateKey )
	{
		if ($this::isNullOrEmptyString($shopId)) {
			throw new ZboziKonverze_Exception('shopId si mandatory');
		} else {
			$this->SHOP_ID = $shopId;
		}

		if ($this::isNullOrEmptyString($privateKey)) {
			throw new ZboziKonverze_Exception('privateKey si mandatory');
		} else {
			$this->PRIVATE_KEY = $privateKey;
		}

		$this->sandbox = false;
	}

	/**
	 * Sets customer email
	 */
	public function setEmail( string $email ) : void
	{
		$this->email = $email;
	}

	/**
	 * Adds order ID
	 */
	public function addOrderId( string $orderId ) : void
	{
		$this->orderId = $orderId;
	}

	/**
	 * Adds ordered product using name
	 */
	public function addProduct( string $productName ) : void
	{
		$item = new ZboziKonverze_CartItem();
		$item->productName = $productName;
		$this->cart[] = $item;
	}

	/**
	 * Adds ordered product using item ID
	 */
	public function addProductItemId( string $itemId ) : void
	{
		$item = new ZboziKonverze_CartItem();
		$item->itemId = $itemId;
		$this->cart[] = $item;
	}

	/**
	 * Adds ordered product using array which can contains
	 * \p productName ,
	 * \p itemId ,
	 * \p unitPrice ,
	 * \p quantity
	 *
	 * @param array $cartItem Array of various CartItem attributes
	 */
	public function addCartItem( array $cartItem) : void
	{
		$item = new ZboziKonverze_CartItem();
		if (array_key_exists("productName", $cartItem)) {
			$item->productName = $cartItem["productName"];
		}
		if (array_key_exists("itemId", $cartItem)) {
			$item->itemId = $cartItem["itemId"];
		}
		if (array_key_exists("unitPrice", $cartItem)) {
			$item->unitPrice = $cartItem["unitPrice"];
		}
		if (array_key_exists("quantity", $cartItem)) {
			$item->quantity = $cartItem["quantity"];
		}

		$this->cart[] = $item;
	}

	/**
	 * Sets order attributes within
	 * \p email ,
	 * \p deliveryType ,
	 * \p deliveryPrice ,
	 * \p orderId ,
	 * \p otherCosts ,
	 * \p paymentType ,
	 *
	 * @param array $orderAttributes Array of various order attributes
	 */
	public function setOrder( array $orderAttributes ) : void
	{
		if (array_key_exists("email", $orderAttributes) && $orderAttributes["email"]) {
			$this->email = $orderAttributes["email"];
		}
		$this->deliveryType = $orderAttributes["deliveryType"];
		$this->deliveryPrice = $orderAttributes["deliveryPrice"];
		$this->orderId = $orderAttributes["orderId"];
		$this->otherCosts = $orderAttributes["otherCosts"];
		$this->paymentType = $orderAttributes["paymentType"];
	}


	/**
	 * Creates HTTP request and returns response body
	 *
	 * @param string $url URL
	 * @return boolean true if everything is perfect else throws exception
	 * @throws ZboziKonverze_Exception can be thrown if connection to Zbozi.cz
	 * server cannot be established.
	 */
	protected function sendRequest( string $url ) : bool
	{
		$data = get_object_vars($this);

		$convert = null;
		$convert = function( &$data ) use (&$convert) {
			foreach( $data as $k=>$v ) {
				if(is_object($v)) {
					$v = get_object_vars($v);

					$data[$k] = $v;
				}

				if(is_array($v)) {
					$convert( $data[$k] );
					continue;
				}

				if(is_string($v)) {
					$data[$k] = $v;
				}
			}
		};

		$convert($data);

		$encoded_json = json_encode($data);

		if (extension_loaded('curl'))
		{
			$ch = curl_init();
			curl_setopt($ch, CURLOPT_URL, $url);
			curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
			curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 3 /* seconds */);
			curl_setopt($ch, CURLOPT_POST, 1);
			curl_setopt($ch, CURLOPT_POSTFIELDS, $encoded_json);
			curl_setopt($ch, CURLOPT_HTTPHEADER, array('Content-Type: application/json'));
			$response = curl_exec($ch);

			if ($response === false) {
				throw new ZboziKonverze_Exception('Unable to establish connection to ZboziKonverze service: ' . curl_error($ch));
			}
		}
		else
		{
			// use key 'http' even if you send the request to https://...
			$options = array(
				'http' => array(
					'header'  => "Content-type: application/json",
					'method'  => 'POST',
					'content' => $encoded_json,
				),
			);
			$context  = stream_context_create($options);
			$response = file_get_contents($url, false, $context);

			if ($response === false) {
				throw new ZboziKonverze_Exception('Unable to establish connection to ZboziKonverze service');
			}
		}

		$decoded_response = json_decode($response, true);
		if ((int)($decoded_response["status"] / 100) === 2) {
			return true;
		} else {
			throw new ZboziKonverze_Exception('Request was not accepted: ' . $decoded_response['statusMessage']);
		}
	}

	/**
	 * Returns endpoint URL
	 *
	 * @return string URL where the request will be called
	 */
	private function getUrl() : string
	{
		$url = $this::BASE_URL;
		$url = str_replace("%%SHOP_ID%%", $this->SHOP_ID, $url);

		if ($this->sandbox) {
			$url = str_replace("%%DOMAIN%%", "sandbox.zbozi.cz", $url);
		} else {
			$url = str_replace("%%DOMAIN%%", "www.zbozi.cz", $url);
		}

		return $url;
	}

	/**
	 * Sends request to ZboziKonverze service and checks for valid response
	 *
	 * @return boolean true if everything is perfect else throws exception
	 * @throws ZboziKonverze_Exception can be thrown if connection to Zbozi.cz
	 * server cannot be established or mandatory values are missing.
	 */
	public function send() : bool
	{
		$url = $this->getUrl();

		// send request and check for valid response
		try {
			$status = $this->sendRequest($url);
			return $status;
		} catch ( Exception|Error $e) {
			throw new ZboziKonverze_Exception($e->getMessage());
		}
	}
}

