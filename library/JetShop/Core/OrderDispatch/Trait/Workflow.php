<?php
namespace JetShop;

use Jet\Data_DateTime;
use Jet\Logger;
use Jet\UI_messages;
use JetApplication\Complaint;
use JetApplication\Delivery_Method_EShopData;
use JetApplication\Order;
use JetApplication\OrderDispatch;
use JetApplication\OrderDispatch_Event;
use JetApplication\OrderDispatch_Item;
use JetApplication\Product_EShopData;



trait Core_OrderDispatch_Trait_Workflow
{
	public static function newByComplaint( Complaint $complaint, int $product_id, int $delivery_method, string $delivery_point_code ) : static
	{
		$_order = Order::get($complaint->getOrderId());
		$product = Product_EShopData::get( $product_id, $complaint->getEshop() );
		
		$delivery_method = Delivery_Method_EShopData::get( $delivery_method, $complaint->getEshop() );
		
		
		$dispatch = new static();
		
		$dispatch->setDispatchDate( Data_DateTime::now() );
		
		$dispatch->setEshop( $complaint->getEshop() );
		$dispatch->setContext( $complaint->getProvidesContext() );
		$dispatch->setOrderId( $complaint->getOrderId() );
		
		$dispatch->setWarehouse( $_order->getWarehouse() );
		
		$dispatch->setStatus( OrderDispatch::STATUS_PENDING );
		
		
		$dispatch->setRecipientEmail( $complaint->getEmail() );
		$dispatch->setRecipientPhone( $complaint->getPhone() );
		
		$dispatch->setRecipientCompany( $complaint->getDeliveryCompanyName() );
		$dispatch->setRecipientFirstName( $complaint->getDeliveryFirstName() );
		$dispatch->setRecipientSurname( $complaint->getDeliverySurname() );
		
		$dispatch->setRecipientStreet( $complaint->getDeliveryAddressStreetNo() );
		$dispatch->setRecipientTown( $complaint->getDeliveryAddressTown() );
		$dispatch->setRecipientZip( $complaint->getDeliveryAddressZip() );
		$dispatch->setRecipientCountry( $complaint->getDeliveryAddressCountry() );
		
		
		$dispatch->setCodCurrency( $_order->getCurrency() );
		
		
		$dispatch->setFinancialValue( $product->getPrice( $_order->getPricelist() ) );
		
		
		$dispatch->setCarrierCode( $delivery_method->getCarrierCode() );
		$dispatch->setCarrierServiceCode( $delivery_method->getCarrierServiceCode() );
		
		$dispatch->setDeliveryPointCode( $delivery_point_code );
		
		
		$dispatch_item = new OrderDispatch_Item();
		$dispatch_item->setProductId( $product->getId() );
		$dispatch_item->setTitle( $product->getName() );
		$dispatch_item->setNumberOfUnits( 1, $product->getKind()?->getMeasureUnit() );
		$dispatch_item->setInternalCode( $product->getInternalCode() );
		$dispatch_item->setEAN( $product->getEan() );
		
		$dispatch->items[] = $dispatch_item;

		
		
		$dispatch->save();
		
		Logger::info(
			event: 'order_dispatch:created',
			event_message: 'Order dispatch has been created',
			context_object_id: $dispatch->getId(),
			context_object_name: 'dispatch',
			context_object_data:$dispatch
		);
		
		return $dispatch;
	}
	
	public static function newByOrder( Order $order ) : static
	{
		
		$dispatch = new static();
		
		$dispatch->setWarehouse( $order->getWarehouse() );
		
		$dispatch->setDispatchDate( Data_DateTime::now() );
		
		$dispatch->setEshop( $order->getEshop() );
		
		$dispatch->setContext( $order->getProvidesContext() );
		
		$dispatch->setOrderId( $order->getId() );
		
		$dispatch->setStatus( OrderDispatch::STATUS_PENDING );
		
		
		$dispatch->setRecipientEmail( $order->getEmail() );
		$dispatch->setRecipientPhone( $order->getPhone() );
		
		$dispatch->setRecipientCompany( $order->getDeliveryCompanyName() );
		$dispatch->setRecipientFirstName( $order->getDeliveryFirstName() );
		$dispatch->setRecipientSurname( $order->getDeliverySurname() );
		
		$dispatch->setRecipientStreet( $order->getDeliveryAddressStreetNo() );
		$dispatch->setRecipientTown( $order->getDeliveryAddressTown() );
		$dispatch->setRecipientZip( $order->getDeliveryAddressZip() );
		$dispatch->setRecipientCountry( $order->getDeliveryAddressCountry() );
		
		if($order->getPaymentMethod()->getKind()->isCOD()) {
			$dispatch->setCod( $order->getTotalAmount_WithVAT() );
		}
		$dispatch->setCodCurrency( $order->getCurrency() );
		
		$dispatch->setFinancialValue( $order->getProductAmount_WithVAT() );
		
		$delivery_method = $order->getDeliveryMethod();
		
		$dispatch->setCarrierCode( $delivery_method->getCarrierCode() );
		$dispatch->setCarrierServiceCode( $delivery_method->getCarrierServiceCode() );
		$dispatch->setDeliveryPointCode( $order->getDeliveryPersonalTakeoverDeliveryPointCode() );
		
		foreach($order->getItems() as $order_item) {
			if( $order_item->isPhysicalProduct() ) {
				$product = Product_EShopData::get( $order_item->getItemId(), $order->getEshop() );
				
				$dispatch_item = new OrderDispatch_Item();
				$dispatch_item->setProductId( $order_item->getItemId() );
				$dispatch_item->setTitle( $order_item->getTitle() );
				$dispatch_item->setNumberOfUnits( $order_item->getNumberOfUnits(), $order_item->getMeasureUnit() );
				$dispatch_item->setInternalCode( $product->getInternalCode() );
				$dispatch_item->setEAN( $product->getEan() );
				
				
				$dispatch->items[] = $dispatch_item;
			}
		}
		
		
		$dispatch->save();
		
		Logger::info(
			event: 'order_dispatch:created',
			event_message: 'Order dispatch has been created',
			context_object_id: $dispatch->getId(),
			context_object_name: 'dispatch',
			context_object_data:$dispatch
		);
		
		return $dispatch;
	}
	
	
	
	
	public function isReadyToCreateConsignment() : bool
	{
		if(
			in_array($this->status, [
				static::STATUS_PREPARED_CONSIGNMENT_CREATED,
				static::STATUS_CANCEL,
				static::STATUS_CANCELED
			]) ||
			count($this->packets)==0
		) {
			return false;
		}
		
		return true;
	}
	
	
	public function isConsignmentCreated() : bool
	{
		return $this->status==static::STATUS_PREPARED_CONSIGNMENT_CREATED;
	}
	
	
	public function setIsPrepared() : void
	{
		if(!$this->isReadyToCreateConsignment()) {
			return;
		}
		
		$this->status = static::STATUS_PREPARED_CONSIGNMENT_NOT_CREATED;
		$this->save();
		
		Logger::info(
			event: 'order_dispatch:set_is_prepared',
			event_message: 'Order dispatch is prepared',
			context_object_id: $this->getId(),
			context_object_name: 'dispatch',
			context_object_data: $this
		);
		
	}

	
	public function createConsignment() : bool
	{
		/**
		 * @var OrderDispatch $this
		 */
		if(
			$this->getStatus()==static::STATUS_PREPARED_CONSIGNMENT_NOT_CREATED ||
			$this->getStatus()==static::STATUS_PREPARED_CONSIGNMENT_CREATE_PROBLEM
		) {
			$this->getCarrier()->createConsignment( $this );
		}
		
		return false;
	}
	
	
	public function rollBack( bool $ignore_errors ) : bool
	{
		/**
		 * @var OrderDispatch $this
		 */
		
		if(!in_array(
			$this->status,
			[
				static::STATUS_PREPARED_CONSIGNMENT_NOT_CREATED,
				static::STATUS_PREPARED_CONSIGNMENT_CREATE_PROBLEM,
				static::STATUS_PREPARED_CONSIGNMENT_CREATED,
				static::STATUS_CANCEL,
				static::STATUS_CANCELED,
			]
		)) {
			return false;
		}
		
		if( $this->consignment_id || $this->tracking_number ) {
			$error_message = '';
			if(!$this->getCarrier()->cancelConsignment( $this, $error_message )) {
				if(!$ignore_errors) {
					UI_messages::danger( $error_message );
					
					return false;
				}
			}
		}
		
		$this->consignment_create_error_message = '';
		$this->status = static::STATUS_PENDING;
		$this->consignment_id = '';
		$this->tracking_number = '';
		
		$this->save();
		
		Logger::info(
			event: 'order_dispatch:rollback',
			event_message: 'Order dispatch rollback',
			context_object_id: $this->getId(),
			context_object_name: 'dispatch',
			context_object_data: $this
		);
		
		
		return true;
	}
	
	
	public function setConsignmentCreateError( string $error_message ) : void
	{
		$this->consignment_create_error_message = $error_message;
		$this->status = static::STATUS_PREPARED_CONSIGNMENT_CREATE_PROBLEM;
		$this->save();
		
		Logger::info(
			event: 'order_dispatch:consignment_create_error',
			event_message: 'Consignment creation error: '.$error_message,
			context_object_id: $this->getId(),
			context_object_name: 'dispatch',
			context_object_data: $this
		);
		
	}
	
	public function setConsignmentCreated( string $consignment_id, $tracking_number ) : void
	{
		$this->consignment_create_error_message = '';
		$this->status = static::STATUS_PREPARED_CONSIGNMENT_CREATED;
		$this->consignment_id = $consignment_id;
		$this->tracking_number = $tracking_number;
		$this->save();
		
		Logger::info(
			event: 'order_dispatch:consignment_created',
			event_message: 'Consignment created',
			context_object_id: $this->getId(),
			context_object_name: 'dispatch',
			context_object_data: $this
		);
		
	}
	
	public function cancel() : void
	{
		if(
			!in_array($this->status, [
				static::STATUS_PENDING,
				static::STATUS_PREPARED_CONSIGNMENT_NOT_CREATED,
				static::STATUS_PREPARED_CONSIGNMENT_CREATE_PROBLEM,
				static::STATUS_PREPARED_CONSIGNMENT_CREATED
			])
		) {
			return;
		}
		
		$this->status = static::STATUS_CANCEL;
		$this->save();
		
		Logger::info(
			event: 'order_dispatch:cancel',
			event_message: 'Order dispatch cancel',
			context_object_id: $this->getId(),
			context_object_name: 'dispatch',
			context_object_data: $this
		);
		
	}
	
	public function confirmCancellation() : void
	{
		if(
			$this->status != static::STATUS_CANCEL
		) {
			return;
		}
		
		$this->status = static::STATUS_CANCELED;
		$this->save();
		
		Logger::info(
			event: 'order_dispatch:cancelled',
			event_message: 'Order dispatch cancellation confirmed',
			context_object_id: $this->getId(),
			context_object_name: 'dispatch',
			context_object_data: $this
		);
		
		
	}
	
	
	public function sent() : void
	{
		/**
		 * @var OrderDispatch $this
		 */

		if(
			$this->status != static::STATUS_PREPARED_CONSIGNMENT_CREATED
		) {
			return;
		}
		
		$this->status = static::STATUS_SENT;
		$this->dispatch_date = Data_DateTime::now();
		$this->save();
		
		Logger::info(
			event: 'order_dispatch:sent',
			event_message: 'Order dispatched and sent',
			context_object_id: $this->getId(),
			context_object_name: 'dispatch',
			context_object_data: $this
		);
		
		OrderDispatch_Event::newEvent(
			$this,
			OrderDispatch::EVENT_SENT
		)->handleImmediately();
	}
	
	
	public function delivered() : void
	{
		/**
		 * @var OrderDispatch $this
		 */
		
		if( $this->status == static::STATUS_DELIVERED ) {
			return;
		}
		
		$this->status = static::STATUS_DELIVERED;
		$this->save();
		
		Logger::info(
			event: 'order_dispatch:delivered',
			event_message: 'Consignment Delivered',
			context_object_id: $this->getId(),
			context_object_name: 'dispatch',
			context_object_data: $this
		);

		
		OrderDispatch_Event::newEvent(
			$this,
			OrderDispatch::EVENT_DELIVERED
		)->handleImmediately();
	}
	
	
	public function returning() : void
	{
		/**
		 * @var OrderDispatch $this
		 */
		
		if( $this->status == static::STATUS_RETURNING ) {
			return;
		}
		
		$this->status = static::STATUS_RETURNING;
		$this->save();
		
		Logger::info(
			event: 'order_dispatch:returning',
			event_message: 'Consignment Returning',
			context_object_id: $this->getId(),
			context_object_name: 'dispatch',
			context_object_data: $this
		);

		
		OrderDispatch_Event::newEvent(
			$this,
			OrderDispatch::EVENT_RETURNING
		)->handleImmediately();
	}
	
	
	public function returned() : void
	{
		/**
		 * @var OrderDispatch $this
		 */
		
		if( $this->status == static::STATUS_RETURNED ) {
			return;
		}
		
		$this->status = static::STATUS_RETURNED;
		$this->save();
		
		Logger::info(
			event: 'order_dispatch:returned',
			event_message: 'Consignment Returned',
			context_object_id: $this->getId(),
			context_object_name: 'dispatch',
			context_object_data: $this
		);

		
		OrderDispatch_Event::newEvent(
			$this,
			OrderDispatch::EVENT_RETURNED
		)->handleImmediately();
	}
	
	public function lost() : void
	{
		/**
		 * @var OrderDispatch $this
		 */
		
		if( $this->status == static::STATUS_LOST ) {
			return;
		}
		
		$this->status = static::STATUS_LOST;
		$this->save();
		
		Logger::info(
			event: 'order_dispatch:lost',
			event_message: 'Consignment Lost',
			context_object_id: $this->getId(),
			context_object_name: 'dispatch',
			context_object_data: $this
		);
		
		
		OrderDispatch_Event::newEvent(
			$this,
			OrderDispatch::EVENT_LOST
		)->handleImmediately();
	}
	
}