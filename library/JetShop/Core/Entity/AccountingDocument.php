<?php
namespace JetShop;

use Jet\DataModel;
use Jet\DataModel_Definition;

use Jet\Form_Definition;
use Jet\Form_Field;
use JetApplication\CompanyInfo;
use JetApplication\Context_ProvidesContext_Interface;
use JetApplication\Currencies;
use JetApplication\Currency;
use JetApplication\Entity_AccountingDocument_Item;
use JetApplication\Entity_Address;
use JetApplication\Entity_WithEShopRelation;
use JetApplication\Invoice_VATOverviewItem;
use JetApplication\NumberSeries_Entity_Interface;
use JetApplication\NumberSeries_Entity_Trait;
use JetApplication\Payment_Kind;
use JetApplication\Pricelists;
use JetApplication\Pricelist;
use JetApplication\EShop;
use JetApplication\Context_ProvidesContext_Trait;
use JetApplication\Order;

#[DataModel_Definition(
	key: [
		'name' => 'key',
		'property_names' => [
			'key'
		],
		'type' => DataModel::KEY_TYPE_UNIQUE
	]
)]
abstract class Core_Entity_AccountingDocument extends Entity_WithEShopRelation implements NumberSeries_Entity_Interface, Context_ProvidesContext_Interface
{
	
	
	use Context_ProvidesContext_Trait;
	use NumberSeries_Entity_Trait;
	
	
	
	#[DataModel_Definition(
		type: DataModel::TYPE_STRING,
		max_len: 50,
	)]
	protected string $key = '';
	
	
	#[DataModel_Definition(
		type: DataModel::TYPE_STRING,
		is_key: true,
		max_len: 50,
	)]
	protected string $currency_code = '';
	
	#[DataModel_Definition(
		type: DataModel::TYPE_STRING,
		is_key: true,
		max_len: 50,
	)]
	protected string $pricelist_code = '';
	
	#[DataModel_Definition(
		type: DataModel::TYPE_BOOL
	)]
	protected bool $cancelled = false;
	
	#[DataModel_Definition(
		type: DataModel::TYPE_BOOL
	)]
	protected bool $locked = false;
	
	#[DataModel_Definition(
		type: DataModel::TYPE_BOOL
	)]
	protected bool $sent = false;
	
	
	#[DataModel_Definition(
		type: DataModel::TYPE_STRING,
		max_len: 255,
		is_key: true
	)]
	protected string $issuer_email = '';
	
	#[DataModel_Definition(
		type: DataModel::TYPE_STRING,
		max_len: 255,
		is_key: true
	)]
	protected string $issuer_phone = '';
	
	#[DataModel_Definition(
		type: DataModel::TYPE_STRING,
		max_len: 255
	)]
	protected string $issuer_company_name = '';
	
	#[DataModel_Definition(
		type: DataModel::TYPE_STRING,
		max_len: 50
	)]
	protected string $issuer_company_id = '';
	
	#[DataModel_Definition(
		type: DataModel::TYPE_STRING,
		max_len: 50
	)]
	protected string $issuer_company_vat_id = '';
	
	#[DataModel_Definition(
		type: DataModel::TYPE_STRING,
		max_len: 255
	)]
	protected string $issuer_first_name = '';
	
	#[DataModel_Definition(
		type: DataModel::TYPE_STRING,
		max_len: 255
	)]
	protected string $issuer_surname = '';
	
	#[DataModel_Definition(
		type: DataModel::TYPE_STRING,
		max_len: 255
	)]
	protected string $issuer_address_street_no = '';
	
	#[DataModel_Definition(
		type: DataModel::TYPE_STRING,
		max_len: 255
	)]
	protected string $issuer_address_town = '';
	
	#[DataModel_Definition(
		type: DataModel::TYPE_STRING,
		max_len: 255
	)]
	protected string $issuer_address_zip = '';
	
	#[DataModel_Definition(
		type: DataModel::TYPE_STRING,
		max_len: 255
	)]
	protected string $issuer_address_country = '';
	
	
	#[DataModel_Definition(
		type: DataModel::TYPE_STRING,
		max_len: 255
	)]
	protected string $issuer_info = '';
	
	
	#[DataModel_Definition(
		type: DataModel::TYPE_STRING,
		max_len: 255
	)]
	#[Form_Definition(
		type: Form_Field::TYPE_INPUT,
		label: 'Bank - name:'
	)]
	protected string $issuer_bank_name = '';
	
	#[DataModel_Definition(
		type: DataModel::TYPE_STRING,
		max_len: 255
	)]
	#[Form_Definition(
		type: Form_Field::TYPE_INPUT,
		label: 'Bank - account number:'
	)]
	protected string $issuer_bank_account_number = '';
	
	
	
	#[DataModel_Definition(
		type: DataModel::TYPE_INT,
		is_key: true,
	)]
	protected int $order_id = 0;
	
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
	protected string $customer_email = '';
	
	#[DataModel_Definition(
		type: DataModel::TYPE_STRING,
		max_len: 255,
		is_key: true
	)]
	protected string $customer_phone = '';
	
	#[DataModel_Definition(
		type: DataModel::TYPE_STRING,
		max_len: 255
	)]
	protected string $customer_company_name = '';
	
	#[DataModel_Definition(
		type: DataModel::TYPE_STRING,
		max_len: 50
	)]
	protected string $customer_company_id = '';
	
	#[DataModel_Definition(
		type: DataModel::TYPE_STRING,
		max_len: 50
	)]
	protected string $customer_company_vat_id = '';
	
	#[DataModel_Definition(
		type: DataModel::TYPE_STRING,
		max_len: 255
	)]
	protected string $customer_first_name = '';
	
	#[DataModel_Definition(
		type: DataModel::TYPE_STRING,
		max_len: 255
	)]
	protected string $customer_surname = '';
	
	#[DataModel_Definition(
		type: DataModel::TYPE_STRING,
		max_len: 255
	)]
	protected string $customer_address_street_no = '';
	
	#[DataModel_Definition(
		type: DataModel::TYPE_STRING,
		max_len: 255
	)]
	protected string $customer_address_town = '';
	
	#[DataModel_Definition(
		type: DataModel::TYPE_STRING,
		max_len: 255
	)]
	protected string $customer_address_zip = '';
	
	#[DataModel_Definition(
		type: DataModel::TYPE_STRING,
		max_len: 255
	)]
	protected string $customer_address_country = '';
	
	#[DataModel_Definition(
		type: DataModel::TYPE_STRING,
		max_len: 65536
	)]
	protected string $invoice_perex = '';
	
	#[DataModel_Definition(
		type: DataModel::TYPE_STRING,
		max_len: 50,
		is_key: true
	)]
	protected string $payment_kind = '';
	
	#[DataModel_Definition(
		type: DataModel::TYPE_FLOAT
	)]
	protected float $total_without_vat = 0.0;
	
	#[DataModel_Definition(
		type: DataModel::TYPE_FLOAT
	)]
	protected float $total_vat = 0.0;
	
	#[DataModel_Definition(
		type: DataModel::TYPE_FLOAT
	)]
	protected float $total_round = 0.0;
	
	#[DataModel_Definition(
		type: DataModel::TYPE_FLOAT
	)]
	protected float $total = 0.0;
	
	/**
	 * @var Entity_AccountingDocument_Item[]
	 */
	#[DataModel_Definition(
		type: DataModel::TYPE_DATA_MODEL,
		data_model_class: Entity_AccountingDocument_Item::class
	)]
	protected array $items = [];
	
	
	public function getId() : int
	{
		return $this->id;
	}
	
	public function setId( int $id ) : void
	{
		$this->id = $id;
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
	
	public function getPricelistCode(): string
	{
		return $this->pricelist_code;
	}
	
	public function getPricelist() : Pricelist
	{
		return Pricelists::get( $this->pricelist_code );
	}
	
	public function setPricelistCode( string $pricelist_code ): void
	{
		$this->pricelist_code = $pricelist_code;
	}
	
	
	public function getNumberSeriesEntityShop(): ?EShop
	{
		return $this->getEshop();
	}
	
	
	public function getIssuerEmail(): string
	{
		return $this->issuer_email;
	}
	
	public function setIssuerEmail( string $issuer_email ): void
	{
		$this->issuer_email = $issuer_email;
	}
	
	public function getIssuerPhone(): string
	{
		return $this->issuer_phone;
	}
	
	public function setIssuerPhone( string $issuer_phone ): void
	{
		$this->issuer_phone = $issuer_phone;
	}
	
	public function getIssuerCompanyName(): string
	{
		return $this->issuer_company_name;
	}
	
	public function setIssuerCompanyName( string $issuer_company_name ): void
	{
		$this->issuer_company_name = $issuer_company_name;
	}
	
	public function getIssuerCompanyId(): string
	{
		return $this->issuer_company_id;
	}
	
	public function setIssuerCompanyId( string $issuer_company_id ): void
	{
		$this->issuer_company_id = $issuer_company_id;
	}
	
	public function getIssuerCompanyVatId(): string
	{
		return $this->issuer_company_vat_id;
	}
	
	public function setIssuerCompanyVatId( string $issuer_company_vat_id ): void
	{
		$this->issuer_company_vat_id = $issuer_company_vat_id;
	}
	
	public function getIssuerFirstName(): string
	{
		return $this->issuer_first_name;
	}
	
	public function setIssuerFirstName( string $issuer_first_name ): void
	{
		$this->issuer_first_name = $issuer_first_name;
	}
	
	public function getIssuerSurname(): string
	{
		return $this->issuer_surname;
	}
	
	public function setIssuerSurname( string $issuer_surname ): void
	{
		$this->issuer_surname = $issuer_surname;
	}
	
	public function getIssuerAddressStreetNo(): string
	{
		return $this->issuer_address_street_no;
	}
	
	public function setIssuerAddressStreetNo( string $issuer_address_street_no ): void
	{
		$this->issuer_address_street_no = $issuer_address_street_no;
	}
	
	public function getIssuerAddressTown(): string
	{
		return $this->issuer_address_town;
	}
	
	public function setIssuerAddressTown( string $issuer_address_town ): void
	{
		$this->issuer_address_town = $issuer_address_town;
	}
	
	public function getIssuerAddressZip(): string
	{
		return $this->issuer_address_zip;
	}
	
	public function setIssuerAddressZip( string $issuer_address_zip ): void
	{
		$this->issuer_address_zip = $issuer_address_zip;
	}
	
	public function getIssuerAddressCountry(): string
	{
		return $this->issuer_address_country;
	}
	
	public function setIssuerAddressCountry( string $issuer_address_country ): void
	{
		$this->issuer_address_country = $issuer_address_country;
	}
	
	public function getIssuerInfo(): string
	{
		return $this->issuer_info;
	}
	
	public function setIssuerInfo( string $issuer_info ): void
	{
		$this->issuer_info = $issuer_info;
	}
	
	public function getIssuerBankName(): string
	{
		return $this->issuer_bank_name;
	}
	
	public function setIssuerBankName( string $issuer_bank_name ): void
	{
		$this->issuer_bank_name = $issuer_bank_name;
	}
	
	public function getIssuerBankAccountNumber(): string
	{
		return $this->issuer_bank_account_number;
	}
	
	public function setIssuerBankAccountNumber( string $issuer_bank_account_number ): void
	{
		$this->issuer_bank_account_number = $issuer_bank_account_number;
	}
	
	
	
	
	
	public function getOrderId(): int
	{
		return $this->order_id;
	}
	
	public function setOrderId( int $order_id ): void
	{
		$this->order_id = $order_id;
	}
	
	
	
	public function getCustomerId() : int
	{
		return $this->customer_id;
	}
	
	public function setCustomerId( int $customer_id ) : void
	{
		$this->customer_id = $customer_id;
	}
	
	public function getCustomerEmail() : string
	{
		return $this->customer_email;
	}
	
	public function setCustomerEmail( string $customer_email ) : void
	{
		$this->customer_email = $customer_email;
	}
	
	public function getCustomerPhone() : string
	{
		return $this->customer_phone;
	}
	
	public function setCustomerPhone( string $customer_phone ) : void
	{
		$this->customer_phone = $customer_phone;
	}
	
	public function getCustomerCompanyName() : string
	{
		return $this->customer_company_name;
	}
	
	public function setCustomerCompanyName( string $customer_company_name ) : void
	{
		$this->customer_company_name = $customer_company_name;
	}
	
	public function getCustomerCompanyId() : string
	{
		return $this->customer_company_id;
	}
	
	public function setCustomerCompanyId( string $customer_company_id ) : void
	{
		$this->customer_company_id = $customer_company_id;
	}
	
	public function getCustomerCompanyVatId() : string
	{
		return $this->customer_company_vat_id;
	}
	
	public function setCustomerCompanyVatId( string $customer_company_vat_id ) : void
	{
		$this->customer_company_vat_id = $customer_company_vat_id;
	}
	
	public function getCustomerFirstName() : string
	{
		return $this->customer_first_name;
	}
	
	public function setCustomerFirstName( string $customer_first_name ) : void
	{
		$this->customer_first_name = $customer_first_name;
	}
	
	public function getCustomerSurname(): string
	{
		return $this->customer_surname;
	}
	
	public function setCustomerSurname( string $customer_surname ): void
	{
		$this->customer_surname = $customer_surname;
	}
	
	public function getCustomerAddressStreetNo() : string
	{
		return $this->customer_address_street_no;
	}
	
	public function setCustomerAddressStreetNo( string $customer_address_street_no ) : void
	{
		$this->customer_address_street_no = $customer_address_street_no;
	}
	
	public function getCustomerAddressTown() : string
	{
		return $this->customer_address_town;
	}
	
	public function setCustomerAddressTown( string $customer_address_town ) : void
	{
		$this->customer_address_town = $customer_address_town;
	}
	
	public function getCustomerAddressZip() : string
	{
		return $this->customer_address_zip;
	}
	
	public function setCustomerAddressZip( string $customer_address_zip ) : void
	{
		$this->customer_address_zip = $customer_address_zip;
	}
	
	public function getCustomerAddressCountry() : string
	{
		return $this->customer_address_country;
	}
	
	public function setCustomerAddressCountry( string $customer_address_country ) : void
	{
		$this->customer_address_country = $customer_address_country;
	}
	
	public function getInvoicePerex(): string
	{
		return $this->invoice_perex;
	}
	
	public function setInvoicePerex( string $invoice_perex ): void
	{
		$this->invoice_perex = $invoice_perex;
	}
	
	
	public function getPaymentKind(): ?Payment_Kind
	{
		return Payment_Kind::get( $this->payment_kind );
	}
	
	public function setPaymentKind( Payment_Kind $payment_kind ): void
	{
		if($payment_kind->isAllowedForInvoices()) {
			$this->payment_kind = $payment_kind->getCode();
			return;
		}
		
		$this->payment_kind = $payment_kind->getAlternativeKindForInvoices()->getCode();
	}
	
	
	
	public function getTotal() : float
	{
		return $this->total;
	}
	
	public function getTotalWithoutVat(): float
	{
		return $this->total_without_vat;
	}
	
	public function setTotalWithoutVat( float $total_without_vat ): void
	{
		$this->total_without_vat = $total_without_vat;
	}
	
	public function getTotalVat(): float
	{
		return $this->total_vat;
	}
	
	public function setTotalVat( float $total_vat ): void
	{
		$this->total_vat = $total_vat;
	}
	
	public function getTotalRound(): float
	{
		return $this->total_round;
	}
	
	public function setTotalRound( float $total_round ): void
	{
		$this->total_round = $total_round;
	}
	
	
	
	abstract public function getItems() : iterable;
	
	public function addItem( Entity_AccountingDocument_Item $item ) : void
	{
		$this->items[] = $item;
	}
	
	/**
	 * @return Invoice_VATOverviewItem[]
	 */
	public function getVATOverview() : array
	{
		/**
		 * @var Invoice_VATOverviewItem[] $overview
		 */
		
		$pricelist = $this->getPricelist();
		$overview = [];
		
		foreach( $this->items as $item ) {
			$vat_rate_key = round($item->getVatRate()*100);
			
			if(!isset($overview[$vat_rate_key])) {
				$overview[$vat_rate_key] = new Invoice_VATOverviewItem( $item->getVatRate() );
			}
			
			$overview[$vat_rate_key]->addTaxBase( $pricelist->round_WithoutVAT( $item->getNumberOfUnits() * $item->getPricePerUnit_WithoutVat() ) );
			$overview[$vat_rate_key]->addTax( $pricelist->round_VAT( $item->getNumberOfUnits() * $item->getPricePerUnit_Vat() ) );
		}
		
		return $overview;
	}
	
	
	
	
	public function recalculate() : void
	{
		$this->total_without_vat = 0.0;
		$this->total_vat = 0.0;
		
		$this->total_round = 0.0;
		$this->total = 0.0;
		
		
		$pricelist = $this->getPricelist();
		
		foreach( $this->items as $item ) {
			$this->total_without_vat += $pricelist->round_WithoutVAT( $item->getNumberOfUnits() * $item->getPricePerUnit_WithoutVat() );
			$this->total_vat         += $pricelist->round_VAT( $item->getNumberOfUnits() * $item->getPricePerUnit_Vat() );
		}
		
		
		$total_wo_round = $this->total_without_vat + $this->total_vat;
		
		if($this->total_vat!=0) {
			$this->total = $pricelist->round_WithVAT( $total_wo_round );
		} else {
			$this->total = $pricelist->round_WithoutVAT( $total_wo_round );
		}
		
		$this->total_round = $pricelist->round_VAT( $this->total - $total_wo_round );
	}
	
	protected function generateKey() : void
	{
		$this->key = md5( time().uniqid().uniqid() );
	}
	
	public function getKey() : string
	{
		return $this->key;
	}
	
	public function beforeSave(): void
	{
		parent::beforeSave();
		if($this->getIsNew()) {
			$this->generateKey();
		}
	}
	
	public function afterAdd(): void
	{
		parent::afterAdd();
		$this->generateNumber();
	}
	
	
	public static function get( int $id ) : static|null
	{
		return static::load( $id );
	}
	
	public static function getByKey( string $key ) : static|null
	{
		$orders = static::fetch(['order' => [
			'key' => $key
		]]);
		
		if(count($orders)!=1) {
			return null;
		}
		
		return $orders[0];
	}
	
	
	public static function getByNumber( string $number, EShop $eshop ) : static|null
	{
		$where = $eshop->getWhere();
		$where[] = 'AND';
		$where[] = [
			'number' => $number
		];
		
		$orders = static::fetch(['' => $where]);
		
		if(count($orders)!=1) {
			return null;
		}
		
		return $orders[0];
	}
	
	/**
	 * @return static[]
	 */
	public static function getList() : iterable
	{
		$where = [];
		
		return static::fetchInstances( $where );
	}
	
	/**
	 * @return static[]
	 */
	public static function getListByOrder( Order $order ) : iterable
	{
		$where = [
			$order->getEshop()->getWhere(),
			'AND',
			'order_id' => $order->getId()
		];
		
		$list = static::fetchInstances( $where );
		$list->getQuery()->setOrderBy('-id');
		
		return $list;
	}
	
	
	public function setIssuerAddress( Entity_Address $address ) : void
	{
		$this->setIssuerCompanyName( $address->getCompanyName() );
		$this->setIssuerCompanyId( $address->getCompanyId() );
		$this->setIssuerCompanyVatId( $address->getCompanyVatId() );
		$this->setIssuerFirstName( $address->getFirstName() );
		$this->setIssuerSurname( $address->getSurname() );
		$this->setIssuerAddressStreetNo( $address->getAddressStreetNo() );
		$this->setIssuerAddressTown( $address->getAddressTown() );
		$this->setIssuerAddressZip( $address->getAddressZip() );
		$this->setIssuerAddressCountry( $address->getAddressCountry() );
	}
	
	public function getIssuerAddress() : Entity_Address
	{
		$address = new Entity_Address();
		
		$address->setCompanyName( $this->getIssuerCompanyName( ) );
		$address->setCompanyId( $this->getIssuerCompanyId( ) );
		$address->setCompanyVatId( $this->getIssuerCompanyVatId( ) );
		$address->setFirstName( $this->getIssuerFirstName( ) );
		$address->setSurname( $this->getIssuerSurname( ) );
		$address->setAddressStreetNo( $this->getIssuerAddressStreetNo( ) );
		$address->setAddressTown( $this->getIssuerAddressTown( ) );
		$address->setAddressZip( $this->getIssuerAddressZip( ) );
		$address->setAddressCountry( $this->getIssuerAddressCountry( ) );
		
		return $address;
	}
	
	
	public function setCustomerAddress( Entity_Address $address ) : void
	{
		$this->setCustomerCompanyName( $address->getCompanyName() );
		$this->setCustomerCompanyId( $address->getCompanyId() );
		$this->setCustomerCompanyVatId( $address->getCompanyVatId() );
		$this->setCustomerFirstName( $address->getFirstName() );
		$this->setCustomerSurname( $address->getSurname() );
		$this->setCustomerAddressStreetNo( $address->getAddressStreetNo() );
		$this->setCustomerAddressTown( $address->getAddressTown() );
		$this->setCustomerAddressZip( $address->getAddressZip() );
		$this->setCustomerAddressCountry( $address->getAddressCountry() );
	}
	
	public function getCustomerAddress() : Entity_Address
	{
		$address = new Entity_Address();
		
		$address->setCompanyName( $this->getCustomerCompanyName( ) );
		$address->setCompanyId( $this->getCustomerCompanyId( ) );
		$address->setCompanyVatId( $this->getCustomerCompanyVatId( ) );
		$address->setFirstName( $this->getCustomerFirstName( ) );
		$address->setSurname( $this->getCustomerSurname( ) );
		$address->setAddressStreetNo( $this->getCustomerAddressStreetNo( ) );
		$address->setAddressTown( $this->getCustomerAddressTown( ) );
		$address->setAddressZip( $this->getCustomerAddressZip( ) );
		$address->setAddressCountry( $this->getCustomerAddressCountry( ) );
		
		return $address;
	}
	
	
	public function getAdminTitle(): string
	{
		return $this->getNumber();
	}
	
	
	public function hasVAT() : bool
	{
		return $this->getTotalVat()!=0;
	}
	
	
	public function isEditable() : bool
	{
		return !$this->locked;
	}
	
	
	
	
	public static function createByOrder( Order $order ) : static
	{
		$invoice = new static();
		
		$invoice->setEshop( $order->getEshop() );
		
		$invoice->setOrderId( $order->getId() );
		
		$invoice->setCurrencyCode( $order->getCurrencyCode() );
		$invoice->setPricelistCode( $order->getPricelistCode() );
		
		$invoice->setCustomerId( $order->getCustomerId() );
		$invoice->setCustomerAddress( $order->getBillingAddress() );
		$invoice->setCustomerEmail( $order->getEmail() );
		$invoice->setCustomerPhone( $order->getPhone() );
		
		$company_info = CompanyInfo::get( $order->getEshop() );
		
		$invoice->setIssuerEmail( $company_info->getEmail() );
		$invoice->setIssuerPhone( $company_info->getPhone() );
		$invoice->setIssuerAddress( $company_info->getAddress() );
		$invoice->setIssuerInfo( $company_info->getInvoiceInfo() );
		$invoice->setIssuerBankName( $company_info->getBankName() );
		$invoice->setIssuerBankAccountNumber( $company_info->getBankAccountNumber() );
		
		$invoice->setPaymentKind( $order->getPaymentMethod()->getKind() );
		
		$item_class_name = static::getDataModelDefinition(static::class)->getProperty('items')->getValueDataModelClass();
		
		foreach($order->getItems() as $item) {
			if($item->getTotalAmount()!=0) {
				/**
				 * @var Entity_AccountingDocument_Item $item_class_name
				 */
				$invoice->addItem( $item_class_name::createFrom( $item ) );
			}
		}
		
		$invoice->recalculate();
		
		return $invoice;
	}
	
	public function isCancelled(): bool
	{
		return $this->cancelled;
	}
	
	
	
	abstract public function cancel() : void;
}
