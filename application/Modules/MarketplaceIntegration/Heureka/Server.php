<?php
/**
 * @copyright Copyright (c) Miroslav Marek <mirek.marek@web-jet.cz>
 * @license EUPL 1.2  https://eupl.eu/1.2/en/
 * @author Miroslav Marek <mirek.marek@web-jet.cz>
 */
namespace JetApplicationModule\MarketplaceIntegration\Heureka;


use Jet\AJAX;
use Jet\Application;
use Jet\Data_DateTime;
use Jet\Http_Headers;
use Jet\Http_Request;
use Jet\Locale;
use Jet\Logger;
use JetApplication\Availability;
use JetApplication\Delivery_Method;
use JetApplication\Order;
use JetApplication\Order_Item;
use JetApplication\Order_Status_Cancelled;
use JetApplication\Order_Status_Delivered;
use JetApplication\Order_Status_Dispatched;
use JetApplication\Order_Status_DispatchStarted;
use JetApplication\Order_Status_ReadyForDispatch;
use JetApplication\Order_Status_Returned;
use JetApplication\Payment_Method;
use JetApplication\Pricelist;
use JetApplication\Product_EShopData;
use JetApplication\EShops;
use JetApplication\EShop;

class Server
{
	protected EShop $eshop;
	protected Config_PerShop $config;
	
	protected Availability $availability;
	protected Pricelist $pricelist;
	
	protected array $request_data = [];
	protected string $raw_request_data = '';
	protected string $request_object = '';
	protected string $handler_method = '';
	
	public function __construct( Config_PerShop $config )
	{
		$this->config = $config;
		
		$eshop = $config->getEshop();
		
		$this->eshop = $eshop;
		
		$this->availability = $eshop->getDefaultAvailability();
		$this->pricelist = $eshop->getDefaultPricelist();
		
		EShops::setCurrent( $eshop );
		Locale::setCurrentLocale( $eshop->getLocale() );
		
		$this->eshop = $eshop;
	}
	
	public function handle() : void
	{
		$this->init();
		
		if($this->handler_method) {
			$this->{'handle_'.$this->handler_method}();
		}
		Application::end();
	}
	
	protected function init() : void
	{
		$GET = Http_Request::GET();
		
		$request_method = Http_Request::requestMethod();
		
		
		$log_rec = [
			'request_date_time' => date('Y-m-d H:i:s'),
			'request_method' => $request_method,
			'request_uri' => $_SERVER['REQUEST_URI'],
			'request_get' => $_SERVER['QUERY_STRING'],
			'request_post' => '',
			'IP' => Http_Request::clientIP(),
		];
		
		if($request_method=='GET') {
			$this->request_data = $GET->getRawData();
		} else {
			$this->raw_request_data = Http_Request::rawPostData();
			
			$log_rec['request_post'] = $this->raw_request_data;
			
			parse_str(urldecode( $this->raw_request_data), $this->request_data );
		}
		
		
		$request = $request_method.':'.trim($_SERVER['REQUEST_URI'], '/');
		
		$this->handler_method = match ( $request ) {
			'GET:products/availability' => 'productsAvailability',
			'GET:payment/delivery' => 'paymentDelivery',
			'POST:order/send' => 'orderSend',
			'GET:order/status' => 'orderStatus',
			'PUT:order/cancel' => 'orderCancel',
			'PUT:payment/status' => 'paymentStatus',
			default => '',
		};
		
		$log_rec['handler'] = $this->handler_method;
		
		Logger::info(
			event: 'HeurekaServer',
			event_message: 'Heureka integration server request',
			context_object_data: $log_rec
		);
	}
	
	protected function response( array $data, $http_code = 200, array $http_headers=[] ) : void
	{
		
		if(empty($http_headers['Content-Type'])) {
			$http_headers['Content-Type'] = 'application/json';
		}
		
		AJAX::commonResponse( $data, $http_headers, $http_code );
	}
	
	public function responseUnknownRequest( array $error_data=[] ) : void
	{
		if(empty($error_data['error'])) {
			$error_data['error'] = 'Unknown request';
		}
		$this->response($error_data, Http_Headers::CODE_400_BAD_REQUEST);
	}
	
	public function responseUnknownItem( array $error_data=[] ) : void
	{
		if(empty($error_data['error'])) {
			$error_data['error'] = 'Unknown item';
		}
		$this->response($error_data, Http_Headers::CODE_404_NOT_FOUND );
	}
	
	protected function getProducts() : array
	{
		$request_data = $this->request_data;
		if(
			empty($request_data['products']) ||
			!is_array($request_data['products'])
		) {
			$this->responseUnknownRequest( [ 'info' => 'products data missing' ] );
		}
		
		$products = [];
		foreach( $request_data['products'] as $p_d ) {
			if(empty($p_d['id'])) {
				$this->responseUnknownRequest(['info'=>'product id is missing']);
			}
			if(empty($p_d['count'])) {
				$this->responseUnknownRequest(['info'=>'product count is missing']);
			}
			
			$product = Product_EShopData::get( $p_d['id'], $this->eshop );
			
			if(!$product) {
				$this->responseUnknownItem(['info'=>'unknown product '.$p_d['id']]);
			}
			
			$products[] = [
				'product' => $product,
				'count' => (int)$p_d['count'],
				'price' => (float)($p_d['price']??0.0)
			];
		}
		
		return $products;
	}
	
	
	protected function handle_productsAvailability() : void
	{
		$response = [];
		
		foreach($this->getProducts() as $item) {
			/**
			 * @var Product_EShopData $product
			 */
			$product = $item['product'];
			$count = $item['count'];
			
			$response[] = $this->handle_productsAvailability_generateResponseItem( $product, $count );
		}
		
		$price_sum = 0;
		
		foreach( $response as $p ) {
			$price_sum = $price_sum + $p['priceTotal'];
		}
		
		
		$this->response([
			'products' => $response,
			'priceSum' => $price_sum
		]);
	}
	
	protected function handle_productsAvailability_generateResponseItem( Product_EShopData $product, $requested_count ) : array
	{
		$avl_count = $requested_count;
		$available = true;
		$delivery = 0;
		
		
		
		if(!$product->isActive()) {
			$avl_count = 0;
			$delivery = -1;
			$available = false;
		} else {
			if($requested_count>$product->getNumberOfAvailable( $this->availability )) {
				$delivery = $product->getDeliveryInfo( $this->availability )->getLengthOfDelivery();
				
				if(!$product->getAllowToOrderWhenSoldOut()) {
					$available = false;
				}
			}
			
		}
		
		$price_per_item = $product->getPrice( $this->pricelist );
		
		$d = [
			'id' => $product->getId(),
			'available' => $available,
			'name' => $product->getName(),
			'price' => $price_per_item,
			'count' => (int)$avl_count,
			'delivery' => $delivery,
			'priceTotal' => $price_per_item*$avl_count
		];
		
		return $d;

	}
	
	
	protected function handle_paymentDelivery() : void
	{
		$products = [];
		foreach($this->getProducts() as $item) {
			/**
			 * @var Product_EShopData $product
			 */
			$product = $item['product'];
			$count = $item['count'];
			
			$products[] = $product;
		}
		
		
		$transport = [];
		$payment = [];
		$binding = [];
		
		$allowed_delivery_methods = [];
		$allowed_payment_methods_ids = null;
		$allowed_payment_methods = [];
		
		$this->available_delivery_methods = Delivery_Method::getAvailableByProducts(
			EShops::getCurrent(),
			$cart->getProducts()
		);
		
		foreach( $delivery_methods as $delivery_method ) {
			
			$delivery_map_item = $this->config->getDeliveryMapItem( $delivery_method->getId() );
			if( !$delivery_map_item ) {
				continue;
			}
			
			
			$allowed_delivery_methods[] = $delivery_method;
			
			$tr = [
				'id' => $delivery_method->getId(),
				'type' => $delivery_map_item->getType(),
				'name' => $delivery_method->getTitle(),
				'description' => $delivery_method->getDescription(),
				'price' => $delivery_method->getPrice( $this->pricelist )
			];
			if($delivery_map_item->getStoreId()) {
				$tr['store'] = [
					'id' => $delivery_map_item->getStoreId(),
					'type' => $delivery_map_item->getType(),
				];
			}
			
			$transport[] = $tr;
			
			$_allowed_payment_methods = [];
			foreach($delivery_method->getPaymentMethods() as $p_m) {
				$_allowed_payment_methods[] = $p_m->getId();
			}
			
			if($allowed_payment_methods_ids===null) {
				$allowed_payment_methods_ids = $_allowed_payment_methods;
			} else {
				$allowed_payment_methods_ids = array_intersect($allowed_payment_methods_ids, $_allowed_payment_methods);
			}
		}
		
		
		foreach( Payment_Method::getAllActive($this->eshop) as $payment_method ) {
			if(
				!in_array($payment_method->getId(), $allowed_payment_methods_ids??[]) ||
				!($map_item = $this->config->getPaymentMapItem($payment_method->getId()))
			) {
				continue;
			}
			
			$allowed_payment_methods[$payment_method->getId()] = $payment_method;
			
			$payment[] = [
				'id' => $payment_method->getId(),
				'type' => $map_item->getType(),
				'name' => $payment_method->getTitle(),
				'price' => $payment_method->getPrice( $this->pricelist )
			];
		}
		
		
		foreach( $allowed_delivery_methods as $delivery_method ) {

			foreach( $delivery_method->getPaymentMethods() as $payment_method ) {
				if(!isset($allowed_payment_methods[$payment_method->getId()])) {
					continue;
				}
				
				$binding[] = [
					'id' => 1000000*$delivery_method->getId()+$payment_method->getId(),
					'transportId' => $delivery_method->getId(),
					'paymentId' => $payment_method->getId()
				];
			}
		}
		
		$this->response([
			'transport' => $transport,
			'payment' => $payment,
			'binding' => $binding
		]);
	}
	
	
	
	protected function handle_orderSend() : void
	{
		$data = $this->request_data;
		
		$heureka_id = $data['heureka_id'];
		if(!$heureka_id) {
			$this->responseUnknownRequest([]);
		}
		
		$import_source = Main::IMPORT_SOURCE;
		
		$exists = Order::getByImportSource( $import_source, $heureka_id, $this->eshop );
		
		if($exists) {
			$this->response([
				'order_id' => $exists->getId(),
				'internal_id' => $exists->getId(),
				'variableSymbol' => $exists->getId()
			]);
			return;
			
		}
		
		$products = $this->getProducts();
		
		
		$order = new Order();
		
		$order->setImportSource( $import_source );
		$order->setImportRemoteId(  $heureka_id);
		
		$order->setDatePurchased( Data_DateTime::now() );
		
		$order->setEshop( $this->eshop );
		$order->setCurrencyCode( $this->pricelist->getCurrency()->getCode() );
		$order->setAvailabilityCode( $this->availability->getCode() );
		$order->setPricelistCode( $this->pricelist->getCode() );
		
		if(!empty($data['note'])) {
			$order->setSpecialRequirements( $data['note'] );
		}
		
		$order->setEmail( $data['customer']['email'] );
		$order->setPhone( $data['customer']['phone'] );
		
		$order->setBillingFirstName( $data['customer']['firstname'] );
		$order->setBillingSurname( $data['customer']['lastname'] );
		
		
		$order->setBillingCompanyName( $data['customer']['company']??'' );
		$order->setBillingCompanyId( $data['customer']['ic']??'' );
		$order->setBillingCompanyVatId( $data['customer']['dic']??'' );
		$order->setBillingAddressStreetNo( $data['customer']['street'] );
		$order->setBillingAddressTown( $data['customer']['city'] );
		$order->setBillingAddressZip( $data['customer']['postCode'] );
		$order->setBillingAddressCountry( $data['customer']['state'] );
		
		
		$order->setDeliveryCompanyName($data['deliveryAddress']['company']??'');
		$order->setDeliveryFirstName( $data['deliveryAddress']['firstname'] );
		$order->setDeliverySurname( $data['deliveryAddress']['lastname'] );
		$order->setDeliveryAddressStreetNo( $data['deliveryAddress']['street'] );
		$order->setDeliveryAddressTown( $data['deliveryAddress']['city'] );
		$order->setDeliveryAddressZip( $data['deliveryAddress']['postCode'] );
		$order->setDeliveryAddressCountry( $data['deliveryAddress']['state'] );
		
		
		foreach($this->getProducts() as $item) {
			/**
			 * @var Product_EShopData $product
			 */
			$product = $item['product'];
			$count = $item['count'];
			$price = $item['price']??0.0;
			
			if($price>0) {
				$product->getPriceEntity( $this->pricelist )->setPrice( $price );
			}
			
			$order_item = new Order_Item();
			$order_item->setupProduct( $order->getPricelist(), $product, $count );
			
			$order->addItem( $order_item );
		}
		
		
		
		$delivery_method = Delivery_Method::get( $data['deliveryId'] );
		$order->setDeliveryMethod( $delivery_method, $data['originalId'] );
		
		$payment_method = Payment_Method::get( $data['paymentId'] );
		$order->setPaymentMethod( $payment_method );
		
		
		$order->recalculate();
		$order->save();
		
		$order->newOrder();
		
		
		$this->response([
			'order_id' => $order->getId(),
			'internal_id' => $order->getId(),
			'variableSymbol' => $order->getId()
		]);
	}
	
	public function handle_orderCancel() : void
	{
		
		$order = Order::getByNumber( $this->request_data['order_id']??'', $this->eshop );
		if(!$order) {
			$this->responseUnknownItem();
		}
		
		$order->cancel('Cancelled by API');
		
		$this->response([
			'status' => true
		]);
	}
	
	/** @noinspection SpellCheckingInspection */
	protected function handle_orderStatus() : void
	{
		$order = Order::getByNumber( $this->request_data['order_id']??'', $this->eshop );
		if(!$order) {
			$this->responseUnknownItem();
		}
		
		$status = match ($order->getStatus()?->getCode()) {
			Order_Status_Cancelled::CODE => 4, //storno z pohledu obchodu (obchod stornoval objednávku)
			
			Order_Status_ReadyForDispatch::CODE,
			Order_Status_DispatchStarted::CODE => 3, //objednávka potvrzena (obchod objednávku přijal a potvrzuje, že ji začíná zpracovávat)
			
			Order_Status_Dispatched::CODE => 0, //objednávka vyexpedována (obchod odeslal objednávku zákazníkovi)
			
			//Order_Status_WaitingForPayment::CODE = 'waiting_for_payment';
			//Order_Status_WaitingForGoodsToBeStocked::CODE = 'waiting_for_goods_to_be_stocked';
			
			Order_Status_Delivered::CODE => 9, //objednávka dokončena (zákazník zaplatil a převzal objednávku)
			Order_Status_Returned::CODE => 7, //vráceno ve 14 denní lhůtě (zákazník vrátil zboží v zákonné 14 denní lhůtě)
			
			default => 8 //objednávka byla dokončena na Heurece (objednávka byla správně dokončena na Heurece)
	
		};
		
		$this->response([
			'order_id' => $order->getId(),
			'status' => $status
		]);
	}
	
	protected function handle_paymentStatus() : void
	{
		$order = Order::getByNumber( $this->request_data['order_id']??'', $this->eshop );
		if(!$order) {
			$this->responseUnknownItem();
		}

		if($this->request_data['status']??0==1) {
			$order->paid();
		}
		
		$this->response([
			'status' => true
		]);
	}
	
}