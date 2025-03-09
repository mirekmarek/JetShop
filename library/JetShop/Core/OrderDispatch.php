<?php
/**
 * @copyright Copyright (c) Miroslav Marek <mirek.marek@web-jet.cz>
 * @license EUPL 1.2  https://eupl.eu/1.2/en/
 * @author Miroslav Marek <mirek.marek@web-jet.cz>
 */
namespace JetShop;


use Jet\Data_DateTime;
use Jet\DataModel;
use Jet\DataModel_Definition;

use Jet\DataModel_Fetch_Instances;
use Jet\Tr;
use JetApplication\EShopEntity_Admin_Interface;
use JetApplication\EShopEntity_Admin_Trait;
use JetApplication\Admin_Managers_OrderDispatch;
use JetApplication\Carrier;
use JetApplication\Carrier_DeliveryPoint;
use JetApplication\Carrier_Document;
use JetApplication\Carrier_Service;
use JetApplication\Complaint;
use JetApplication\Context;
use JetApplication\Context_HasContext_Interface;
use JetApplication\Context_HasContext_Trait;
use JetApplication\Context_ProvidesContext_Interface;
use JetApplication\Context_ProvidesContext_Trait;
use JetApplication\Currencies;
use JetApplication\Currency;
use JetApplication\EShopEntity_HasEvents_Interface;
use JetApplication\EShopEntity_HasEvents_Trait;
use JetApplication\EShopEntity_HasGet_Interface;
use JetApplication\EShopEntity_HasGet_Trait;
use JetApplication\EShopEntity_HasStatus_Interface;
use JetApplication\EShopEntity_HasStatus_Trait;
use JetApplication\EShopEntity_WithEShopRelation;
use JetApplication\EShopEntity_Definition;
use JetApplication\EShopEntity_HasNumberSeries_Interface;
use JetApplication\EShopEntity_HasNumberSeries_Trait;
use JetApplication\OrderDispatch;
use JetApplication\OrderDispatch_Item;
use JetApplication\OrderDispatch_Packet;
use JetApplication\Order;
use JetApplication\OrderDispatch_Status_Cancel;
use JetApplication\OrderDispatch_Status_Pending;
use JetApplication\OrderDispatch_Status_PreparedConsignmentCreated;
use JetApplication\OrderDispatch_Status_PreparedConsignmentCreateProblem;
use JetApplication\OrderDispatch_Status_PreparedConsignmentNotCreated;
use JetApplication\OrderDispatch_TrackingHistory;
use JetApplication\EShop;
use JetApplication\WarehouseManagement_Warehouse;
use JetApplication\OrderDispatch_Trait_Forms;
use JetApplication\OrderDispatch_Trait_Workflow;


#[DataModel_Definition(
	name: 'order_dispatch',
	database_table_name: 'order_dispatches',
)]
#[EShopEntity_Definition(
	entity_name_readable: 'Order dispatch',
	admin_manager_interface: Admin_Managers_OrderDispatch::class
)]
abstract class Core_OrderDispatch extends EShopEntity_WithEShopRelation implements
	EShopEntity_HasGet_Interface,
	EShopEntity_HasEvents_Interface,
	EShopEntity_HasStatus_Interface,
	EShopEntity_HasNumberSeries_Interface,
	Context_HasContext_Interface,
	Context_ProvidesContext_Interface,
	EShopEntity_Admin_Interface
{
	use EShopEntity_HasGet_Trait;
	use EShopEntity_HasNumberSeries_Trait;
	use EShopEntity_HasEvents_Trait;
	use EShopEntity_HasStatus_Trait;
	use EShopEntity_Admin_Trait;
	use OrderDispatch_Trait_Forms;
	use OrderDispatch_Trait_Workflow;
	use Context_HasContext_Trait;
	use Context_ProvidesContext_Trait;
	
	#[DataModel_Definition(
		type: DataModel::TYPE_BOOL,
		is_key: true
	)]
	protected bool $is_custom = false;
	
	
	#[DataModel_Definition(
		type: DataModel::TYPE_INT,
		is_key: true
	)]
	protected int $order_id = 0;
	
	protected Order|null|bool $order;
	
	#[DataModel_Definition(
		type: DataModel::TYPE_INT,
		is_key: true
	)]
	protected int $warehouse_id = 0;
	
	protected ?WarehouseManagement_Warehouse $warehouse = null;
	
	#[DataModel_Definition(
		type: DataModel::TYPE_DATE
	)]
	protected ?Data_DateTime $dispatch_date = null;
	
	#[DataModel_Definition(
		type: DataModel::TYPE_FLOAT
	)]
	protected float $cod = 0.0;
	
	#[DataModel_Definition(
		type: DataModel::TYPE_STRING,
		max_len: 50
	)]
	protected string $cod_currency_code = '';
	
	#[DataModel_Definition(
		type: DataModel::TYPE_FLOAT
	)]
	protected float $financial_value = 0.0;
	
	
	#[DataModel_Definition(
		type: DataModel::TYPE_STRING,
		max_len: 255
	)]
	protected string $service_codes = '';
	
	#[DataModel_Definition(
		type: DataModel::TYPE_STRING,
		max_len: 100
	)]
	protected string $recipient_first_name = '';
	
	#[DataModel_Definition(
		type: DataModel::TYPE_STRING,
		max_len: 100
	)]
	protected string $recipient_surname = '';
	
	#[DataModel_Definition(
		type: DataModel::TYPE_STRING,
		max_len: 100
	)]
	protected string $recipient_street = '';
	
	
	#[DataModel_Definition(
		type: DataModel::TYPE_STRING,
		max_len: 100
	)]
	protected string $recipient_company = '';
	
	
	#[DataModel_Definition(
		type: DataModel::TYPE_STRING,
		max_len: 100
	)]
	protected string $recipient_town = '';
	
	#[DataModel_Definition(
		type: DataModel::TYPE_STRING,
		max_len: 100
	)]
	protected string $recipient_zip = '';
	
	#[DataModel_Definition(
		type: DataModel::TYPE_STRING,
		max_len: 100
	)]
	protected string $recipient_country = '';
	
	#[DataModel_Definition(
		type: DataModel::TYPE_STRING,
		max_len: 100
	)]
	protected string $recipient_phone = '';
	
	#[DataModel_Definition(
		type: DataModel::TYPE_STRING,
		max_len: 100
	)]
	protected string $recipient_email = '';
	
	
	#[DataModel_Definition(
		type: DataModel::TYPE_STRING,
		max_len: 100
	)]
	protected string $sender_name = '';
	
	#[DataModel_Definition(
		type: DataModel::TYPE_STRING,
		max_len: 100
	)]
	protected string $sender_street = '';
	
	#[DataModel_Definition(
		type: DataModel::TYPE_STRING,
		max_len: 100
	)]
	protected string $sender_town = '';
	
	#[DataModel_Definition(
		type: DataModel::TYPE_STRING,
		max_len: 100
	)]
	protected string $sender_zip = '';
	
	#[DataModel_Definition(
		type: DataModel::TYPE_STRING,
		max_len: 100
	)]
	protected string $sender_country = '';
	
	#[DataModel_Definition(
		type: DataModel::TYPE_STRING,
		max_len: 100
	)]
	protected string $sender_phone = '';
	
	#[DataModel_Definition(
		type: DataModel::TYPE_STRING,
		max_len: 100
	)]
	protected string $sender_email = '';
	
	
	#[DataModel_Definition(
		type: DataModel::TYPE_STRING,
		max_len: 100
	)]
	protected string $payment_code = '';
	
	#[DataModel_Definition(
		type: DataModel::TYPE_STRING,
		max_len: 100
	)]
	protected string $carrier_code = '';
	
	
	#[DataModel_Definition(
		type: DataModel::TYPE_STRING,
		max_len: 100
	)]
	protected string $carrier_service_code = '';
	
	#[DataModel_Definition(
		type: DataModel::TYPE_STRING,
		max_len: 100
	)]
	protected string $delivery_point_code = '';
	
	
	#[DataModel_Definition(
		type: DataModel::TYPE_STRING,
		max_len: 65536
	)]
	protected string $consignment_create_error_message = '';
	
	#[DataModel_Definition(
		type: DataModel::TYPE_STRING,
		max_len: 100
	)]
	protected string $tracking_number = '';
	
	#[DataModel_Definition(
		type: DataModel::TYPE_STRING,
		max_len: 100
	)]
	protected string $consignment_id = '';
	
	#[DataModel_Definition(
		type: DataModel::TYPE_STRING,
		max_len: 65536
	)]
	protected string $our_note = '';
	
	#[DataModel_Definition(
		type: DataModel::TYPE_STRING,
		max_len: 65536
	)]
	protected string $driver_note = '';
	
	#[DataModel_Definition(
		type: DataModel::TYPE_STRING,
		max_len: 65536
	)]
	protected string $recipient_note = '';
	
	#[DataModel_Definition(
		type: DataModel::TYPE_INT,
	)]
	protected int $boxes_count = 0;
	
	#[DataModel_Definition(
		type: DataModel::TYPE_CUSTOM_DATA
	)]
	protected array $additional_consignment_parameters = [];
	
	/**
	 * @var OrderDispatch_Item[]
	 */
	#[DataModel_Definition(
		type: DataModel::TYPE_DATA_MODEL,
		data_model_class: OrderDispatch_Item::class
	)]
	protected array $items = [];
	
	
	/**
	 * @var OrderDispatch_Packet[]
	 */
	#[DataModel_Definition(
		type: DataModel::TYPE_DATA_MODEL,
		data_model_class: OrderDispatch_Packet::class
	)]
	protected array $packets = [];
	
	protected ?OrderDispatch_Packet $new_packet = null;
	
	public static function getNumberSeriesEntityIsPerShop() : bool
	{
		return false;
	}
	
	public static function getNumberSeriesEntityTitle() : string
	{
		return 'Order dispatch';
	}
	
	
	public static function getDimensionsUnits() : string
	{
		return 'cm';
	}
	
	public static function getVolumeUnits() : string
	{
		return 'cm3';
	}
	
	public static function getWeightUnits() : string
	{
		return 'kg';
	}
	
	public static function getContextScope() : array
	{
		
		return [
			Order::getProvidesContextType()     => Tr::_( 'Order' ),
			Complaint::getProvidesContextType() => Tr::_( 'Complaint' )
		];
	}


	/**
	 * @return DataModel_Fetch_Instances|static[]
	 */
	public static function getListOfToBeCanceled( WarehouseManagement_Warehouse $warehouse ): DataModel_Fetch_Instances|iterable
	{
		$list =  static::fetchInstances( [
			'status_code' => OrderDispatch_Status_Cancel::getCode(),
			'AND',
			'warehouse_id' => $warehouse->getId()
		] );
		
		$list->getQuery()->setOrderBy(['created']);
		
		return $list;
	}
	
	/**
	 * @return DataModel_Fetch_Instances|static[]
	 */
	public static function getListOfPending( WarehouseManagement_Warehouse $warehouse ): DataModel_Fetch_Instances|iterable
	{
		$list =  static::fetchInstances( [
			'status_code' => OrderDispatch_Status_Pending::getCode(),
			'AND',
			'warehouse_id' => $warehouse->getId()
		] );
		
		$list->getQuery()->setOrderBy(['created']);
		
		return $list;
	}
	
	/**
	 * @return DataModel_Fetch_Instances|static[]
	 */
	public static function getListOfInProgress( Context $context ) : DataModel_Fetch_Instances|iterable
	{
		$list =  static::fetchInstances( [
			$context->getWhere(),
			'AND',
			'status_code' => [
				OrderDispatch_Status_Pending::getCode(),
				OrderDispatch_Status_PreparedConsignmentCreated::getCode(),
				OrderDispatch_Status_PreparedConsignmentNotCreated::getCode(),
				OrderDispatch_Status_PreparedConsignmentCreateProblem::getCode()
			]
		] );
		
		$list->getQuery()->setOrderBy(['created']);
		
		return $list;
	}
	
	
	/**
	 * @return DataModel_Fetch_Instances|static[]
	 */
	public static function getListByContext( Context $context ) : DataModel_Fetch_Instances|iterable
	{
		$list =  static::fetchInstances( $context->getWhere() );
		
		$list->getQuery()->setOrderBy(['-created']);
		
		return $list;
	}
	
	/**
	 * @return DataModel_Fetch_Instances|static[]
	 */
	public static function getListOfPrepared( WarehouseManagement_Warehouse $warehouse ) : DataModel_Fetch_Instances|iterable
	{
		$list =  static::fetchInstances( [
			'status_code' => [
				static::STATUS_PREPARED_CONSIGNMENT_CREATED,
				static::STATUS_PREPARED_CONSIGNMENT_NOT_CREATED,
				static::STATUS_PREPARED_CONSIGNMENT_CREATE_PROBLEM
			],
			'AND',
			'warehouse_id' => $warehouse->getId()
		] );
		
		$list->getQuery()->setOrderBy(['created']);
		
		return $list;
	}
	
	
	/**
	 * @return DataModel_Fetch_Instances|static[]
	 */
	public static function getListOfSent( WarehouseManagement_Warehouse $warehouse, ?Data_DateTime $dispatch_date=null ) : DataModel_Fetch_Instances|iterable
	{
		if( !$dispatch_date ) {
			$dispatch_date = Data_DateTime::now();
		}
		
		$dispatch_date->setOnlyDate(true);
		
		$list =  static::fetchInstances( [
			'warehouse_id' => $warehouse->getId(),
			'AND',
			'status_code' => [
				static::STATUS_SENT,
				static::STATUS_ON_THE_WAY,
			],
			'AND',
			'dispatch_date' => $dispatch_date
		] );
		
		$list->getQuery()->setOrderBy(['created']);
		
		return $list;
	}
	
	public static function getByNumber( string $number ) : ?static
	{
		return static::load([
			'number' => $number
		]);
	}
	
	public function afterAdd(): void
	{
		parent::afterAdd();
		$this->generateNumber();
	}
	
	public function getIsCustom(): bool
	{
		return $this->is_custom;
	}
	
	public function setIsCustom( bool $is_custom ): void
	{
		$this->is_custom = $is_custom;
	}
	
	
	
	public function getNumberSeriesEntityData(): ?Data_DateTime
	{
		return $this->getDispatchDate();
	}
	
	public function getNumberSeriesEntityShop(): ?EShop
	{
		return $this->getEshop();
	}
	
	public function getWarehouseId(): int
	{
		return $this->warehouse_id;
	}

	public function setWarehouseId( int $warehouse_id ): void
	{
		$this->warehouse_id = $warehouse_id;
	}
	
	public function getWarehouse() : WarehouseManagement_Warehouse
	{
		if(!$this->warehouse) {
			$this->warehouse = WarehouseManagement_Warehouse::get( $this->getWarehouseId() );
		}
		
		return $this->warehouse;
	}

	public function getOrderId(): int
	{
		return $this->order_id;
	}
	
	public function getOrder() : ?Order
	{
		if($this->order===null) {
			$this->order = Order::get( $this->order_id );
			if(!$this->order) {
				$this->order = false;
			}
		}
		
		return $this->order?:null;
	}

	public function setOrderId( int $order_id ): void
	{
		$this->order_id = $order_id;
	}
	
	public function getDispatchDate(): ?Data_DateTime
	{
		return $this->dispatch_date;
	}

	public function setDispatchDate( ?Data_DateTime $dispatch_date ): void
	{
		$this->dispatch_date = $dispatch_date;
	}
	
	public function getCod(): float
	{
		return $this->cod;
	}

	public function setCod( float $cod ): void
	{
		$this->cod = $cod;
	}
	

	public function getCodCurrency(): Currency
	{
		return Currencies::get( $this->cod_currency_code );
	}
	
	public function setCodCurrency( Currency $cod_currency ): void
	{
		$this->cod_currency_code = $cod_currency->getCode();
	}
	
	public function getFinancialValue(): float
	{
		return $this->financial_value;
	}
	
	public function setFinancialValue( float $financial_value ): void
	{
		$this->financial_value = $financial_value;
	}
	
	public function getServiceCodes(): string
	{
		return $this->service_codes;
	}

	public function setServiceCodes( string $service_codes ): void
	{
		$this->service_codes = $service_codes;
	}

	public function getRecipientFirstName(): string
	{
		return $this->recipient_first_name;
	}

	public function setRecipientFirstName( string $recipient_first_name ): void
	{
		$this->recipient_first_name = $recipient_first_name;
	}
	

	public function getRecipientSurname(): string
	{
		return $this->recipient_surname;
	}
	
	public function setRecipientSurname( string $recipient_surname ): void
	{
		$this->recipient_surname = $recipient_surname;
	}
	
	
	
	public function getRecipientCompany(): string
	{
		return $this->recipient_company;
	}
	
	public function setRecipientCompany( string $recipient_company ): void
	{
		$this->recipient_company = $recipient_company;
	}
	
	

	public function getRecipientStreet(): string
	{
		return $this->recipient_street;
	}

	public function setRecipientStreet( string $recipient_street ): void
	{
		$this->recipient_street = $recipient_street;
	}

	public function getRecipientTown(): string
	{
		return $this->recipient_town;
	}

	public function setRecipientTown( string $recipient_town ): void
	{
		$this->recipient_town = $recipient_town;
	}

	public function getRecipientZip(): string
	{
		return $this->recipient_zip;
	}

	public function setRecipientZip( string $recipient_zip ): void
	{
		$this->recipient_zip = $recipient_zip;
	}

	public function getRecipientCountry(): string
	{
		return $this->recipient_country;
	}

	public function setRecipientCountry( string $recipient_country ): void
	{
		$this->recipient_country = $recipient_country;
	}
	
	public function getRecipientPhone(): string
	{
		return $this->recipient_phone;
	}
	
	public function setRecipientPhone( string $recipient_phone ): void
	{
		$this->recipient_phone = $recipient_phone;
	}

	public function getRecipientEmail(): string
	{
		return $this->recipient_email;
	}

	public function setRecipientEmail( string $recipient_email ): void
	{
		$this->recipient_email = $recipient_email;
	}

	public function getSenderName(): string
	{
		return $this->sender_name;
	}

	public function setSenderName( string $sender_name ): void
	{
		$this->sender_name = $sender_name;
	}

	public function getSenderStreet(): string
	{
		return $this->sender_street;
	}

	public function setSenderStreet( string $sender_street ): void
	{
		$this->sender_street = $sender_street;
	}

	public function getSenderTown(): string
	{
		return $this->sender_town;
	}

	public function setSenderTown( string $sender_town ): void
	{
		$this->sender_town = $sender_town;
	}
	
	public function getSenderZip(): string
	{
		return $this->sender_zip;
	}

	public function setSenderZip( string $sender_zip ): void
	{
		$this->sender_zip = $sender_zip;
	}

	public function getSenderCountry(): string
	{
		return $this->sender_country;
	}
	
	public function setSenderCountry( string $sender_country ): void
	{
		$this->sender_country = $sender_country;
	}

	
	
	public function getSenderPhone(): string
	{
		return $this->sender_phone;
	}

	public function setSenderPhone( string $sender_phone ): void
	{
		$this->sender_phone = $sender_phone;
	}

	public function getSenderEmail(): string
	{
		return $this->sender_email;
	}

	public function setSenderEmail( string $sender_email ): void
	{
		$this->sender_email = $sender_email;
	}

	public function getPaymentCode(): string
	{
		return $this->payment_code;
	}

	public function setPaymentCode( string $payment_code ): void
	{
		$this->payment_code = $payment_code;
	}
	
	public function getCarrierCode(): string
	{
		return $this->carrier_code;
	}
	
	public function setCarrierCode( string $carrier_code ): void
	{
		$this->carrier_code = $carrier_code;
	}
	
	
	public function getCarrier() : ?Carrier
	{
		return Carrier::get( $this->getCarrierCode() );
	}
	
	
	public function getCarrierServiceCode(): string
	{
		return $this->carrier_service_code;
	}

	public function setCarrierServiceCode( string $carrier_service_code ): void
	{
		$this->carrier_service_code = $carrier_service_code;
	}
	
	public function getCarrierService() : ?Carrier_Service
	{
		return $this->getCarrier()?->getService( $this->getCarrierServiceCode() );
	}
	

	public function getDeliveryPointCode(): string
	{
		return $this->delivery_point_code;
	}

	public function setDeliveryPointCode( string $delivery_point_code ): void
	{
		$this->delivery_point_code = $delivery_point_code;
	}
	
	public function getDeliveryPoint() : ?Carrier_DeliveryPoint
	{
		if(!$this->getDeliveryPointCode()) {
			return null;
		}
		
		return Carrier_DeliveryPoint::getPoint(
			$this->getCarrier(),
			$this->getDeliveryPointCode()
		);
	}
	
	public function getConsignmentCreateErrorMessage(): string
	{
		return $this->consignment_create_error_message;
	}
	
	public function setConsignmentCreateErrorMessage( string $consignment_create_error_message ): void
	{
		$this->consignment_create_error_message = $consignment_create_error_message;
	}
	
	public function getTrackingNumber(): string
	{
		return $this->tracking_number;
	}

	public function setTrackingNumber( string $tracking_number ): void
	{
		$this->tracking_number = $tracking_number;
	}
	
	public function getTrackingURL() : string
	{
		/**
		 * @var OrderDispatch $this
		 */
		return $this->getCarrier()?->getTrackingURL( $this )??'';
	}
	
	public function getConsignmentId(): string
	{
		return $this->consignment_id;
	}
	
	public function setConsignmentId( string $consignment_id ): void
	{
		$this->consignment_id = $consignment_id;
	}
	

	
	public function getOurNote(): string
	{
		return $this->our_note;
	}
	
	public function setOurNote( string $our_note ): void
	{
		$this->our_note = $our_note;
	}
	
	public function getDriverNote(): string
	{
		return $this->driver_note;
	}
	
	public function setDriverNote( string $driver_note ): void
	{
		$this->driver_note = $driver_note;
	}
	
	public function getRecipientNote(): string
	{
		return $this->recipient_note;
	}
	
	public function setRecipientNote( string $recipient_note ): void
	{
		$this->recipient_note = $recipient_note;
	}
	
	public function getBoxesCount(): int
	{
		return $this->boxes_count;
	}
	
	public function setBoxesCount( int $boxes_count ): void
	{
		$this->boxes_count = $boxes_count;
	}
	
	/**
	 * @return OrderDispatch_Item[]
	 */
	public function getItems(): array
	{
		return $this->items;
	}
	
	public function addItem( OrderDispatch_Item $item ) : void
	{
		$this->items[] = $item;
	}
	
	/**
	 * @return OrderDispatch_Packet[]
	 */
	public function getPackets(): array
	{
		/**
		 * @var OrderDispatch $this
		 */
		
		foreach($this->packets as $p) {
			$p->setDispatch( $this );
		}
		
		return $this->packets;
	}
	
	public function removePacket( int $id ) : void
	{
		if(isset($this->packets[$id])) {
			$this->packets[$id]->delete();
			unset( $this->packets[$id] );
		}
	}
	
	
	public function setWarehouse( WarehouseManagement_Warehouse $warehouse ) : void
	{
		$this->setWarehouseId( $warehouse->getId() );
		$this->warehouse = $warehouse;
		
		$this->setSenderName( $warehouse->getAddressName() );
		$this->setSenderStreet( $warehouse->getAddressStreetNo() );
		$this->setSenderTown( $warehouse->getAddressTown() );
		$this->setSenderZip( $warehouse->getAddressZip() );
		$this->setSenderCountry( $warehouse->getAddressCountry() );
		
		$this->setSenderEmail( $warehouse->getPublicEmail() );
		$this->setSenderPhone( $warehouse->getPublicPhone() );
		
		foreach($this->items as $item) {
			$wh_card = $warehouse->getCard( $item->getProductId() );
			
			$item->setWarehouseSector( $wh_card->getSector() );
			$item->setWarehouseRack( $wh_card->getRack() );
			$item->setWarehousePosition( $wh_card->getPosition() );
			
		}
		
	}
	
	public function getAdditionalConsignmentParameters(): array
	{
		return $this->additional_consignment_parameters;
	}
	
	public function setAdditionalConsignmentParameters( array $additional_consignment_parameters ): void
	{
		$this->additional_consignment_parameters = $additional_consignment_parameters;
	}
	
	public function hasAdditionalConsignmentParameter( bool $code ) : bool
	{
		return in_array($code, $this->additional_consignment_parameters);
	}
	
	public function addAdditionalConsignmentParameter( bool $code ) : void
	{
		if( $this->hasAdditionalConsignmentParameter($code) ) {
			return;
		}
		$this->additional_consignment_parameters[] = $code;
		$this->save();
	}
	
	public function removeAdditionalConsignmentParameter( bool $code ) : void
	{
		$new_params = [];
		foreach($this->additional_consignment_parameters as $param) {
			if($param!=$code) {
				$new_params[] = $param;
			}
		}
		$this->additional_consignment_parameters = $new_params;
		$this->save();
	}
	

	public function getTotalWeight() : float
	{
		$w = 0.0;
		
		foreach($this->getPackets() as $p) {
			$w += $p->getWeight();
		}
		
		return $w;
	}
	
	public function getLabel( string &$error_message = '' ) : ?Carrier_Document
	{
		if(!$this->isConsignmentCreated()) {
			return null;
		}
		
		/**
		 * @var OrderDispatch $this
		 */
		return $this->getCarrier()->getPacketLabel( $this, $error_message );
	}
	
	public function actualizeTracking( string &$error_message = '' ) : bool
	{
		
		/**
		 * @var OrderDispatch $this
		 */
		return $this->getCarrier()->actualizeTracking( $this, $error_message );
	}
	
	public static function getStatusScope() : array
	{
		return [
			static::STATUS_PENDING => Tr::_('Awaiting processing'),
			
			static::STATUS_PREPARED_CONSIGNMENT_NOT_CREATED    => Tr::_('Waiting for consignment to be created at the carrier'),
			static::STATUS_PREPARED_CONSIGNMENT_CREATE_PROBLEM => Tr::_('Consignment creation problem'),
			static::STATUS_PREPARED_CONSIGNMENT_CREATED        => Tr::_('Ready to send'),
			
			static::STATUS_SENT       => Tr::_('Sent'),
			static::STATUS_ON_THE_WAY => Tr::_('On the way'),
			static::STATUS_DELIVERED  => Tr::_('Delivered'),
			static::STATUS_RETURNING  => Tr::_('Returning'),
			static::STATUS_RETURNED   => Tr::_('Returned'),
			static::STATUS_LOST       => Tr::_('Lost'),
			
			static::STATUS_CANCEL   => Tr::_('Cancellation in progress'),
			static::STATUS_CANCELED => Tr::_('Canceled'),
		
		];
	}
	
	/**
	 * @return OrderDispatch_TrackingHistory[]
	 */
	public function getTrackingHistory() : array
	{
		return OrderDispatch_TrackingHistory::getForOrderDispatch( $this->getId() );
	}
	
	/**
	 * @param OrderDispatch_TrackingHistory[] $new_tracking_history
	 * @param string $new_status
	 *
	 * @return void
	 */
	public function setTrackingData(
		array $new_tracking_history,
		string $new_status
	) : void
	{
		if(!$new_tracking_history) {
			return;
		}
		
		$current_tracking = $this->getTrackingHistory();
		
		foreach( $new_tracking_history as $tr ) {
			if(!isset($current_tracking[$tr->getChecksum()])) {
				$tr->save();
				$current_tracking[$tr->getChecksum()] = $tr;
			}
		}
		
		foreach( $current_tracking as $tr ) {
			if(!isset($new_tracking_history[$tr->getChecksum()])) {
				$tr->delete();
				unset( $current_tracking[$tr->getChecksum()] );
			}
			
		}
		
		switch($new_status) {
			case static::STATUS_ON_THE_WAY:
				if( $this->status_code==static::STATUS_SENT ) {
					$this->status_code = static::STATUS_ON_THE_WAY;
					$this->save();
				}
				break;
			case static::STATUS_LOST:
				$this->lost();
				break;
			case static::STATUS_RETURNING:
				$this->returning();
				break;
			case static::STATUS_RETURNED:
				$this->returned();
				break;
			case static::STATUS_DELIVERED:
				$this->delivered();
				break;
		}
	}
	
	public function getAdminTitle(): string
	{
		return $this->number;
	}
	
	public function isEditable() : bool
	{
		if($this->status_code!=static::STATUS_PENDING) {
			return false;
		}
		
		return true;
	}
	
}