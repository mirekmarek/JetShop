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
use JetApplication\Context;
use JetApplication\Context_HasContext_Interface;
use JetApplication\Context_HasContext_Trait;
use JetApplication\Currencies;
use JetApplication\Currency;
use JetApplication\Customer_Address;
use JetApplication\EShop;
use JetApplication\EShopEntity_Admin_Interface;
use JetApplication\EShopEntity_Admin_Trait;
use JetApplication\EShopEntity_Definition;
use JetApplication\EShopEntity_Event;
use JetApplication\EShopEntity_HasEvents_Interface;
use JetApplication\EShopEntity_HasEvents_Trait;
use JetApplication\EShopEntity_HasGet_Interface;
use JetApplication\EShopEntity_HasGet_Trait;
use JetApplication\EShopEntity_HasNumberSeries_Interface;
use JetApplication\EShopEntity_HasStatus_Interface;
use JetApplication\EShopEntity_HasStatus_Trait;
use JetApplication\EShopEntity_WithEShopRelation;
use JetApplication\MoneyRefund_Event;
use JetApplication\MoneyRefund_Event_InternalNote;
use JetApplication\MoneyRefund_Event_MessageForCustomer;
use JetApplication\MoneyRefund_Event_NewRequest;
use JetApplication\MoneyRefund_Note;
use JetApplication\MoneyRefund_Status;
use JetApplication\MoneyRefund_Status_New;
use JetApplication\MoneyRefund_VirtualStatus_Rollback;
use JetApplication\Order;
use JetApplication\Admin_Managers_MoneyRefund;

#[DataModel_Definition(
	name: 'money_refund',
	database_table_name: 'money_refund',
)]
#[EShopEntity_Definition(
	entity_name_readable: 'Money refundation',
	admin_manager_interface: Admin_Managers_MoneyRefund::class
)]
abstract class Core_MoneyRefund extends EShopEntity_WithEShopRelation implements
	EShopEntity_HasGet_Interface,
	EShopEntity_HasStatus_Interface,
	EShopEntity_HasEvents_Interface,
	EShopEntity_Admin_Interface,
	EShopEntity_HasNumberSeries_Interface,
	Context_HasContext_Interface
{
	protected static array $flags = [];
	
	
	use EShopEntity_HasGet_Trait;
	use EShopEntity_HasStatus_Trait;
	use EShopEntity_HasEvents_Trait;
	use EShopEntity_Admin_Trait;
	use Core_EShopEntity_HasNumberSeries_Trait;
	use Context_HasContext_Trait;
	
	#[DataModel_Definition(
		type: DataModel::TYPE_INT,
		is_key: true,
	)]
	protected int $order_id = 0;
	
	#[DataModel_Definition(
		type: DataModel::TYPE_STRING,
		is_key: true,
		max_len: 50,
	)]
	protected string $order_number = '';
	
	#[DataModel_Definition(
		type: DataModel::TYPE_DATE_TIME
	)]
	protected ?Data_DateTime $date_started = null;
	
	#[DataModel_Definition(
		type: DataModel::TYPE_INT,
		is_key: true,
	)]
	protected int $customer_id = 0;
	
	#[DataModel_Definition(
		type: DataModel::TYPE_STRING,
		max_len: 255,
		is_key: true
	)]
	protected string $email = '';
	
	#[DataModel_Definition(
		type: DataModel::TYPE_STRING,
		max_len: 255,
		is_key: true
	)]
	protected string $phone = '';
	
	
	#[DataModel_Definition(
		type: DataModel::TYPE_STRING,
		max_len: 255
	)]
	protected string $company_name = '';
	
	#[DataModel_Definition(
		type: DataModel::TYPE_STRING,
		max_len: 50
	)]
	protected string $company_id = '';
	
	#[DataModel_Definition(
		type: DataModel::TYPE_STRING,
		max_len: 50
	)]
	protected string $company_vat_id = '';
	
	
	#[DataModel_Definition(
		type: DataModel::TYPE_STRING,
		max_len: 255
	)]
	protected string $first_name = '';
	
	#[DataModel_Definition(
		type: DataModel::TYPE_STRING,
		max_len: 255
	)]
	protected string $surname = '';
	
	#[DataModel_Definition(
		type: DataModel::TYPE_STRING,
		max_len: 255
	)]
	protected string $address_street_no = '';
	
	#[DataModel_Definition(
		type: DataModel::TYPE_STRING,
		max_len: 255
	)]
	protected string $address_town = '';
	
	#[DataModel_Definition(
		type: DataModel::TYPE_STRING,
		max_len: 255
	)]
	protected string $address_zip = '';
	
	#[DataModel_Definition(
		type: DataModel::TYPE_STRING,
		max_len: 255
	)]
	protected string $address_country = '';
	
	
	#[DataModel_Definition(
		type: DataModel::TYPE_STRING,
		max_len: 65535,
	)]
	protected string $internal_summary = '';
	
	#[DataModel_Definition(
		type: DataModel::TYPE_FLOAT,
	)]
	protected float $amount_to_be_refunded = 0.0;
	
	#[DataModel_Definition(
		type: DataModel::TYPE_STRING,
		is_key: true,
		max_len: 50,
	)]
	protected string $currency_code = '';
	
	
	public function afterAdd(): void
	{
		$this->generateNumber();
		
		$this->createEvent( MoneyRefund_Event_NewRequest::new() )->handleImmediately();
	}
	
	
	public function getAdminTitle(): string
	{
		return $this->getNumber();
	}
	
	public static function getNumberSeriesEntityTitle(): string
	{
		return static::getEntityDefinition()->getEntityNameReadable();
	}
	
	public static function getNumberSeriesEntityIsPerShop(): bool
	{
		return true;
	}
	
	public function setOrder( Order $order ) : void
	{
		$this->setEshop( $order->getEshop() );
		$this->order_id = $order->getId();
		$this->order_number = $order->getNumber();
		$this->currency_code = $order->getCurrency()->getCode();
		
		$this->customer_id = $order->getCustomerId();
		
		$this->email = $order->getEmail();
		$this->phone = $order->getPhone();
		
		$ba = $order->getBillingAddress();
		$this->company_name = $ba->getCompanyName();
		$this->company_id = $ba->getCompanyId();
		$this->company_vat_id = $ba->getCompanyVatId();
		
		$this->first_name = $ba->getFirstName();
		$this->surname = $ba->getSurname();
		
		$this->address_street_no = $ba->getAddressStreetNo();
		$this->address_town = $ba->getAddressTown();
		$this->address_zip = $ba->getAddressZip();
		$this->address_country = $ba->getAddressCountry();
		
		$this->currency_code = $order->getCurrency()->getCode();
		$this->status = MoneyRefund_Status_New::getCode();
		
		$this->date_started = Data_DateTime::now();
	}
	
	public function getOrderId(): int
	{
		return $this->order_id;
	}
	
	public function getOrderNumber(): string
	{
		return $this->order_number;
	}
	
	
	
	public function getNumberSeriesEntityShop(): ?EShop
	{
		return $this->getEshop();
	}
	
	public function getCurrencyCode(): string
	{
		return $this->currency_code;
	}
	
	public function getCurrency() : Currency
	{
		return Currencies::get( $this->currency_code );
	}
	
	public function setCurrencyCode( string $currency_code ): void
	{
		$this->currency_code = $currency_code;
	}
	
	public function getInternalSummary(): string
	{
		return $this->internal_summary;
	}
	
	public function setInternalSummary( string $internal_summary ): void
	{
		$this->internal_summary = $internal_summary;
	}
	
	public function getDateStarted(): ?Data_DateTime
	{
		return $this->date_started;
	}
	
	public function setDateStarted( ?Data_DateTime $date_started ): void
	{
		$this->date_started = $date_started;
	}
	
	public function getCustomerId(): int
	{
		return $this->customer_id;
	}
	
	public function setCustomerId( int $customer_id ): void
	{
		$this->customer_id = $customer_id;
	}
	
	public function getEmail(): string
	{
		return $this->email;
	}
	
	public function setEmail( string $email ): void
	{
		$this->email = $email;
	}
	
	public function getPhone(): string
	{
		return $this->phone;
	}
	
	public function setPhone( string $phone ): void
	{
		$this->phone = $phone;
	}
	
	public function getCompanyName(): string
	{
		return $this->company_name;
	}
	
	public function setCompanyName( string $company_name ): void
	{
		$this->company_name = $company_name;
	}
	
	public function getCompanyId(): string
	{
		return $this->company_id;
	}
	
	public function setCompanyId( string $company_id ): void
	{
		$this->company_id = $company_id;
	}
	
	public function getCompanyVatId(): string
	{
		return $this->company_vat_id;
	}
	
	public function setCompanyVatId( string $company_vat_id ): void
	{
		$this->company_vat_id = $company_vat_id;
	}
	
	
	
	public function getFirstName(): string
	{
		return $this->first_name;
	}
	
	public function setFirstName( string $first_name ): void
	{
		$this->first_name = $first_name;
	}
	
	public function getSurname(): string
	{
		return $this->surname;
	}
	
	public function setSurname( string $surname ): void
	{
		$this->surname = $surname;
	}
	
	public function getAddressStreetNo(): string
	{
		return $this->address_street_no;
	}
	
	public function setAddressStreetNo( string $address_street_no ): void
	{
		$this->address_street_no = $address_street_no;
	}
	
	public function getAddressTown(): string
	{
		return $this->address_town;
	}
	
	public function setAddressTown( string $address_town ): void
	{
		$this->address_town = $address_town;
	}
	
	public function getAddressZip(): string
	{
		return $this->address_zip;
	}
	
	public function setAddressZip( string $address_zip ): void
	{
		$this->address_zip = $address_zip;
	}
	
	public function getAddressCountry(): string
	{
		return $this->address_country;
	}
	
	public function setAddressCountry( string $address_country ): void
	{
		$this->address_country = $address_country;
	}
	
	public function getAmountToBeRefunded(): float
	{
		return $this->amount_to_be_refunded;
	}
	
	public function setAmountToBeRefunded( float $amount_to_be_refunded ): void
	{
		$this->amount_to_be_refunded = $amount_to_be_refunded;
	}
	
	
	
	/**
	 * @return static[]
	 */
	public static function getByOrder( Order $order ) : array
	{
		$where = $order->getEshop()->getWhere();
		$where[] = 'AND';
		$where['order_id'] = $order->getId();
		
		return static::fetch( [''=>$where], order_by: ['-id'] );
		
	}
	
	/**
	 * @return static[]
	 */
	public static function getByContext( Context $context ) : array
	{
		
		$where = $context->getWhere();
		
		return static::fetch( [''=>$where], order_by: ['-id'] );
		
	}
	
	
	public function newNote( MoneyRefund_Note $note ) : void
	{
		if( $note->getSentToCustomer() ) {
			$this->messageForCustomer( $note );
		} else {
			$this->internalNote( $note );
		}
	}
	
	public function messageForCustomer( MoneyRefund_Note $note ) : void
	{
		$event = $this->createEvent( MoneyRefund_Event_MessageForCustomer::new() );
		$event->setContext( $note );
		$event->handleImmediately();
	}
	
	public function internalNote( MoneyRefund_Note $note ) : void
	{
		$event = $this->createEvent( MoneyRefund_Event_InternalNote::new() );
		$event->setContext( $note );
		$event->handleImmediately();
	}
	
	/**
	 * @return MoneyRefund_Event[]
	 */
	public function getHistory() : array
	{
		return MoneyRefund_Event::getEventsList( $this->getId() );
	}
	
	public function getAddress() : Customer_Address
	{
		$address = new Customer_Address();
		
		$address->setCompanyName( $this->getCompanyName( ) );
		$address->setCompanyId( $this->getCompanyId( ) );
		$address->setCompanyVatId( $this->getCompanyVatId( ) );
		
		$address->setFirstName( $this->getFirstName( ) );
		$address->setSurname( $this->getSurname( ) );
		$address->setAddressStreetNo( $this->getAddressStreetNo( ) );
		$address->setAddressTown( $this->getAddressTown( ) );
		$address->setAddressZip( $this->getAddressZip( ) );
		$address->setAddressCountry( $this->getAddressCountry( ) );
		
		return $address;
	}
	
	public static function getStatusList(): array
	{
		return MoneyRefund_Status::getList();
	}
	
	
	public function rollback() : void
	{
		$this->setStatus( MoneyRefund_VirtualStatus_Rollback::get() );
	}
	
	public function createEvent( EShopEntity_Event|MoneyRefund_Event $event ): MoneyRefund_Event
	{
		$event->init( $this->getEshop() );
		$event->setMoneyRefund( $this );
		
		return $event;
	}
}