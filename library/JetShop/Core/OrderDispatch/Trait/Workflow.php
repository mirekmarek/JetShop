<?php
/**
 * @copyright Copyright (c) Miroslav Marek <mirek.marek@web-jet.cz>
 * @license EUPL 1.2  https://eupl.eu/1.2/en/
 * @author Miroslav Marek <mirek.marek@web-jet.cz>
 */
namespace JetShop;

use Jet\Data_DateTime;
use Jet\UI_messages;
use JetApplication\Complaint;
use JetApplication\Delivery_Method;
use JetApplication\Order;
use JetApplication\OrderDispatch;
use JetApplication\OrderDispatch_Item;
use JetApplication\OrderDispatch_Status;
use JetApplication\OrderDispatch_Status_Cancel;
use JetApplication\OrderDispatch_Status_Canceled;
use JetApplication\OrderDispatch_Status_Delivered;
use JetApplication\OrderDispatch_Status_Lost;
use JetApplication\OrderDispatch_Status_Pending;
use JetApplication\OrderDispatch_Status_PreparedConsignmentCreated;
use JetApplication\OrderDispatch_Status_PreparedConsignmentCreateProblem;
use JetApplication\OrderDispatch_Status_PreparedConsignmentNotCreated;
use JetApplication\OrderDispatch_Status_Returned;
use JetApplication\OrderDispatch_Status_Returning;
use JetApplication\OrderDispatch_Status_Sent;
use JetApplication\Product_EShopData;



trait Core_OrderDispatch_Trait_Workflow
{
	public static function newByComplaint( Complaint $complaint, int $product_id, int $delivery_method, string $delivery_point_code ) : static
	{
		$_order = Order::get($complaint->getOrderId());
		$product = Product_EShopData::get( $product_id, $complaint->getEshop() );
		
		$delivery_method = Delivery_Method::get( $delivery_method );
		
		
		$dispatch = new static();
		
		$dispatch->setDispatchDate( Data_DateTime::now() );
		
		$dispatch->setEshop( $complaint->getEshop() );
		$dispatch->setContext( $complaint->getProvidesContext() );
		$dispatch->setOrderId( $complaint->getOrderId() );
		
		$dispatch->setWarehouse( $_order->getWarehouse() );
		
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
		
		$dispatch->setStatus( OrderDispatch_Status_Pending::get() );
		
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
		
		$dispatch->setStatus( OrderDispatch_Status_Pending::get() );
		
		return $dispatch;
	}
	
	
	
	
	public function isReadyToCreateConsignment() : bool
	{
		/**
		 * @var OrderDispatch_Status $status
		 */
		$status = $this->getStatus();
		
		if(
			!$status::isReadyToCreateConsignment() ||
			count($this->packets)==0
		) {
			return false;
		}
		
		return true;
	}
	
	
	public function isConsignmentCreated() : bool
	{
		/**
		 * @var OrderDispatch_Status $status
		 */
		$status = $this->getStatus();
		
		return $status::isConsignmentCreated();
	}
	
	
	public function setIsPrepared() : void
	{
		if(!$this->isReadyToCreateConsignment()) {
			return;
		}
		
		$this->setStatus( OrderDispatch_Status_PreparedConsignmentNotCreated::get() );
	}

	
	public function createConsignment() : bool
	{
		/**
		 * @var OrderDispatch_Status $status
		 */
		$status = $this->getStatus();
		
		/**
		 * @var OrderDispatch $this
		 */
		if( $status::canCreateConsignment() ) {
			return $this->getCarrier()->createConsignment( $this );
		}
		
		return false;
	}
	
	
	public function rollBack( bool $ignore_errors ) : bool
	{
		/**
		 * @var OrderDispatch_Status $status
		 */
		$status = $this->getStatus();
		
		if(!$status::isRollbackPossible()) {
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
		$this->status = OrderDispatch_Status_Pending::get()::getCode();
		$this->consignment_id = '';
		$this->tracking_number = '';
		
		$this->save();
		
		
		return true;
	}
	
	
	public function setConsignmentCreateError( string $error_message ) : void
	{
		$this->setConsignmentCreateErrorMessage( $error_message );
		$this->save();
		
		$this->setStatus( OrderDispatch_Status_PreparedConsignmentCreateProblem::get() );
		
	}
	
	public function setConsignmentCreated( string $consignment_id, $tracking_number ) : void
	{
		$this->setStatus( OrderDispatch_Status_PreparedConsignmentCreated::get(), params: [
			'consignment_id' => $consignment_id,
			'tracking_number' => $tracking_number
		] );
		
	}
	
	public function cancel() : void
	{
		/**
		 * @var OrderDispatch_Status $status
		 */
		$status = $this->getStatus();
		
		if(!$status::canBeCancelled()) {
			return;
		}
		
		$this->setStatus( OrderDispatch_Status_Cancel::get() );
	}
	
	public function confirmCancellation() : void
	{
		$this->setStatus( OrderDispatch_Status_Canceled::get() );
	}
	
	
	public function sent() : void
	{
		$this->setStatus( OrderDispatch_Status_Sent::get() );
	}
	
	
	public function delivered() : void
	{
		$this->setStatus( OrderDispatch_Status_Delivered::get() );
	}
	
	
	public function returning() : void
	{
		$this->setStatus( OrderDispatch_Status_Returning::get() );
	}
	
	
	public function returned() : void
	{
		$this->setStatus( OrderDispatch_Status_Returned::get() );
	}
	
	public function lost() : void
	{
		$this->setStatus( OrderDispatch_Status_Lost::get() );
	}
	
}