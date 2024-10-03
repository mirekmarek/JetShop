<?php
namespace JetShop;

use Jet\Data_DateTime;
use Jet\DataModel;
use Jet\DataModel_Definition;

use JetApplication\Context_ProvidesContext_Interface;
use JetApplication\Entity_AccountingDocument;
use JetApplication\Invoice;
use JetApplication\Invoice_VATOverviewItem;
use JetApplication\NumberSeries_Entity_Interface;
use JetApplication\Invoice_Item;
use JetApplication\Order;

#[DataModel_Definition(
	name: 'invoice',
	database_table_name: 'invoices',
)]
abstract class Core_Invoice extends Entity_AccountingDocument implements NumberSeries_Entity_Interface, Context_ProvidesContext_Interface
{
	
	#[DataModel_Definition(
		type: DataModel::TYPE_BOOL
	)]
	protected bool $paid = false;
	
	#[DataModel_Definition(
		type: DataModel::TYPE_DATE_TIME
	)]
	protected ?Data_DateTime $paid_date_time = null;
	
	#[DataModel_Definition(
		type: DataModel::TYPE_BOOL
	)]
	protected bool $correction_invoice = false;
	
	#[DataModel_Definition(
		type: DataModel::TYPE_INT,
		is_key: true,
	)]
	protected int $correction_of_invoice_id = 0;
	
	#[DataModel_Definition(
		type: DataModel::TYPE_STRING,
		max_len: 50,
		is_key: true,
	)]
	protected string $correction_of_invoice_number = '';
	
	#[DataModel_Definition(
		type: DataModel::TYPE_STRING,
		max_len: 65535,
		is_key: true
	)]
	protected string $correction_reason = '';
	
	
	#[DataModel_Definition(
		type: DataModel::TYPE_DATE_TIME
	)]
	protected ?Data_DateTime $invoice_date = null;
	
	#[DataModel_Definition(
		type: DataModel::TYPE_DATE_TIME
	)]
	protected ?Data_DateTime $date_of_taxable_supply = null;
	
	#[DataModel_Definition(
		type: DataModel::TYPE_DATE_TIME
	)]
	protected ?Data_DateTime $due_date = null;
	
	#[DataModel_Definition(
		type: DataModel::TYPE_STRING,
		max_len: 65536
	)]
	protected string $invoice_perex = '';
	
	/**
	 * @var Invoice_Item[]
	 */
	#[DataModel_Definition(
		type: DataModel::TYPE_DATA_MODEL,
		data_model_class: Invoice_Item::class
	)]
	protected array $items = [];
	
	protected ?bool $has_correction = null;
	protected ?array $corrections = null;
	
	/**
	 * @return Invoice_Item[]
	 */
	public function getItems() : iterable
	{
		return $this->items;
	}
	
	
	public function getInvoiceDate(): ?Data_DateTime
	{
		return $this->invoice_date;
	}
	
	public function setInvoiceDate( ?Data_DateTime $invoice_date ): void
	{
		$this->invoice_date = $invoice_date;
	}
	
	public function getDateOfTaxableSupply(): ?Data_DateTime
	{
		return $this->date_of_taxable_supply;
	}
	
	public function setDateOfTaxableSupply( ?Data_DateTime $date_of_taxable_supply ): void
	{
		$this->date_of_taxable_supply = $date_of_taxable_supply;
	}
	
	public function getDueDate(): ?Data_DateTime
	{
		return $this->due_date;
	}
	
	public function setDueDate( ?Data_DateTime $due_date ): void
	{
		$this->due_date = $due_date;
	}
	
	public function getInvoicePerex(): string
	{
		return $this->invoice_perex;
	}
	
	public function setInvoicePerex( string $invoice_perex ): void
	{
		$this->invoice_perex = $invoice_perex;
	}
	
	
	public function getTotalAfterCorrection() : ?float
	{
		if( !$this->getIsCorrectionInvoice() ) {
			return null;
		}
		
		$orig_inv = static::get($this->correction_of_invoice_id);
		$total = $orig_inv->getTotal();
		
		//TODO: prev. corrections
		
		$total += $this->getTotal();
		
		return $total;
	}
	
	/**
	 * @return Invoice_VATOverviewItem[]
	 */
	public function getVATOverview() : array
	{
		$overview = parent::getVATOverview();
		
		if(!$this->getIsCorrectionInvoice()) {
			return $overview;
		}
		
		$pricelist = $this->getPricelist();
		$corrected_overview = static::get( $this->correction_of_invoice_id )->getVATOverview();
		
		//TODO: prev. corrections
		
		foreach($overview as $vat_rate_key=>$correction_vat_overview) {
			
			if(!isset($overview[$vat_rate_key])) {
				$overview[$vat_rate_key] = new Invoice_VATOverviewItem( $correction_vat_overview->getVatRate() );
			}
			
			$corrected_overview[$vat_rate_key]->taxBaseCorrection( $correction_vat_overview->getTaxBase() );
			$corrected_overview[$vat_rate_key]->taxCorrection( $correction_vat_overview->getTax() );
		}
		
		return $corrected_overview;

	}
	
	public function setIsCorrectionOfInvoice( Invoice $invoice ): void
	{
		$this->correction_invoice = true;
		$this->correction_of_invoice_id = $invoice->getId();
		$this->correction_of_invoice_number = $invoice->getNumber();
	}
	
	
	public function getIsCorrectionInvoice(): bool
	{
		return $this->correction_invoice;
	}
	
	public function getCorrectionOfInvoiceId(): int
	{
		return $this->correction_of_invoice_id;
	}
	
	public function getCorrectionOfInvoiceNumber(): string
	{
		return $this->correction_of_invoice_number;
	}
	
	
	public function getCorrectionReason(): string
	{
		return $this->correction_reason;
	}
	
	public function setCorrectionReason( string $correction_reason ): void
	{
		$this->correction_reason = $correction_reason;
	}
	
	public function getIsPaid(): bool
	{
		return $this->paid;
	}
	
	public function setPaid( bool $paid ): void
	{
		$this->paid = $paid;
	}
	
	public function getPaidDateTime(): ?Data_DateTime
	{
		return $this->paid_date_time;
	}
	
	public function setPaidDateTime( ?Data_DateTime $paid_date_time ): void
	{
		$this->paid_date_time = $paid_date_time;
	}
	
	
	
	
	public function hasCorrections() : bool
	{
		if($this->has_correction===null) {
			$ids = static::dataFetchCol(select:['id'], where: [
				'correction_of_invoice_id' => $this->id
			], raw_mode: true);
			
			$this->has_correction = count($ids)>0;
		}
		
		return $this->has_correction;
	}
	
	/**
	 * @return static[]
	 */
	public function getCorrections() : array
	{
		if($this->corrections===null) {
			$list = static::fetchInstances([
				'correction_of_invoice_id' => $this->id
			]);
			
			$list->getQuery()->setOrderBy('-id');
			
			$this->corrections = [];
			foreach($list as $item) {
				$this->corrections[] = $item;
			}
		}
		
		return $this->corrections;
	}
	
	
	
	public static function createByOrder( Order $order ) : static
	{
		$invoice = parent::createByOrder( $order );
		
		$invoice->setInvoiceDate( Data_DateTime::now() );
		$invoice->setDateOfTaxableSupply( Data_DateTime::now() );
		$invoice->setDueDate( Data_DateTime::now() );
		
		return $invoice;
	}
	
	public function prepareCorrectionInvoice() : static
	{
		$c_invoice = new static();
		
		/**
		 * @var Invoice $this
		 */
		$c_invoice->setIsCorrectionOfInvoice( $this );
		
		$c_invoice->setShop( $this->getShop() );
		
		$c_invoice->setOrderId( $this->getOrderId() );
		
		$c_invoice->setCurrencyCode( $this->getCurrencyCode() );
		$c_invoice->setPricelistCode( $this->getPricelistCode() );
		
		$c_invoice->setInvoiceDate( Data_DateTime::now() );
		$c_invoice->setDateOfTaxableSupply( Data_DateTime::now() );
		$c_invoice->setDueDate( Data_DateTime::now() );
		
		
		$c_invoice->setCustomerId( $this->getCustomerId() );
		$c_invoice->setCustomerAddress( $this->getCustomerAddress() );
		$c_invoice->setCustomerEmail( $this->getCustomerEmail() );
		$c_invoice->setCustomerPhone( $this->getCustomerPhone() );
		
		
		$c_invoice->setIssuerEmail( $this->getIssuerEmail() );
		$c_invoice->setIssuerPhone( $this->getIssuerPhone() );
		$c_invoice->setIssuerAddress( $this->getIssuerAddress() );
		$c_invoice->setIssuerInfo( $this->getIssuerInfo() );
		$c_invoice->setIssuerBankName( $this->getIssuerBankName() );
		$c_invoice->setIssuerBankAccountNumber( $this->getIssuerBankAccountNumber() );
		
		$c_invoice->setPaymentKind( $this->getPaymentKind() );
		
		foreach($this->getItems() as $item) {
			if($item->getTotalAmount()!=0) {
				$new_item = Invoice_Item::createFrom( $item );
				
				$new_item->negatePrice();
				
				$c_invoice->addItem( $new_item );
			}
		}
		
		
		$c_invoice->recalculate();
		
		$this->has_correction = null;
		$this->corrections = null;
		
		return $c_invoice;
	}
	
	public function cancel() : void
	{
	
	}
}
