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

use Jet\Form;
use Jet\Form_Definition;
use Jet\Form_Field;
use Jet\Form_Field_Date;
use Jet\Form_Field_Float;
use Jet\Form_Field_Select;
use Jet\Form_Field_Textarea;
use JetApplication\CompanyInfo;
use JetApplication\EShopEntity_Admin_Interface;
use JetApplication\EShopEntity_Admin_Trait;
use JetApplication\Admin_Managers_Invoice;
use JetApplication\Context_ProvidesContext_Interface;
use JetApplication\EShopEntity_AccountingDocument;
use JetApplication\EShopEntity_Price;
use JetApplication\Invoice;
use JetApplication\Invoice_VATOverviewItem;
use JetApplication\EShopEntity_Definition;
use JetApplication\EShopEntity_HasNumberSeries_Interface;
use JetApplication\Invoice_Item;
use JetApplication\Managers_General;
use JetApplication\Order;
use JetApplication\Payment_Kind;

#[DataModel_Definition(
	name: 'invoice',
	database_table_name: 'invoices',
)]
#[EShopEntity_Definition(
	entity_name_readable: 'Invoice',
	admin_manager_interface: Admin_Managers_Invoice::class
)]
abstract class Core_Invoice extends EShopEntity_AccountingDocument implements
	EShopEntity_HasNumberSeries_Interface,
	Context_ProvidesContext_Interface,
	EShopEntity_Admin_Interface
{
	use EShopEntity_Admin_Trait;
	
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
	#[Form_Definition(
		type: Form_Field::TYPE_TEXTAREA,
		label: 'Text before items:'
	)]
	protected string $text_before_items = '';
	
	#[DataModel_Definition(
		type: DataModel::TYPE_STRING,
		max_len: 65536
	)]
	#[Form_Definition(
		type: Form_Field::TYPE_TEXTAREA,
		label: 'Text after items:'
	)]
	protected string $text_after_items = '';
	
	
	#[DataModel_Definition(
		type: DataModel::TYPE_STRING,
		max_len: 255
	)]
	protected string $payment_qr_code_image_filename = '';
	
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
	
	public static function getNumberSeriesEntityIsPerShop() : bool
	{
		return true;
	}
	
	public static function getNumberSeriesEntityTitle() : string
	{
		return 'Invoice';
	}
	
	
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
		
		$company_info = CompanyInfo::get( $invoice->getEshop() );
		$invoice->setTextBeforeItems( $company_info->getTextBeforeInvoiceItems() );
		$invoice->setTextAfterItems( $company_info->getTextAftereInvoiceItems() );
		
		
		$invoice->setInvoiceDate( Data_DateTime::now() );
		$invoice->setDateOfTaxableSupply( Data_DateTime::now() );
		$invoice->setDueDate( Data_DateTime::now() );
		
		return $invoice;
	}
	
	public function prepareCorrectionInvoice() : Invoice
	{
		$c_invoice = new Invoice();
		
		/**
		 * @var Invoice $this
		 */
		$c_invoice->setIsCorrectionOfInvoice( $this );
		
		$c_invoice->setEshop( $this->getEshop() );
		
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
	
	
	protected ?Form $correction_invoice_form = null;
	protected ?Invoice $new_correction_invoice = null;
	

	
	
	

	public function getAddForm(): Form
	{
		return new Form( '', [] );
	}
	
	public function catchAddForm(): bool
	{
		return false;
	}
	
	public function getEditForm(): Form
	{
		return new Form( '', [] );
	}
	
	public function catchEditForm(): bool
	{
		return false;
	}
	
	public function getNewCorrectionInvoice() : static
	{
		if(!$this->new_correction_invoice) {
			$this->new_correction_invoice = $this->prepareCorrectionInvoice();
		}
		
		return $this->new_correction_invoice;
	}
	
	public function getCreateCorrectionInvoiceForm() : Form
	{
		if(!$this->correction_invoice_form) {
			$this->getNewCorrectionInvoice();
			
			$fields = [];
			$pricelist = $this->getPricelist();
			
			$payment_kind = new Form_Field_Select('payment_kind', 'Payment method:');
			$payment_kind->setSelectOptions( Payment_Kind::getInvoiceScope() );
			$payment_kind->setFieldValueCatcher( function( string $value ) {
				$this->new_correction_invoice->setPaymentKind( Payment_Kind::get( $value ) );
			} );
			$fields[] = $payment_kind;
			
			
			$reason = new Form_Field_Textarea('reason', 'Reason of correction:');
			$reason->setIsRequired( true );
			$reason->setErrorMessages([
				Form_Field_Textarea::ERROR_CODE_EMPTY => 'Please enter the reason of invoice correction'
			]);
			$reason->setFieldValueCatcher(function( string $value ) {
				$this->new_correction_invoice->setCorrectionReason( $value );
			});
			$fields[] = $reason;
			
			$invoice_date = new Form_Field_Date('invoice_date', 'Invoice date:');
			$invoice_date->setDefaultValue( Data_DateTime::now() );
			$invoice_date->setFieldValueCatcher( function( Data_DateTime $value ) {
				$this->new_correction_invoice->setInvoiceDate( $value );
			} );
			$fields[] = $invoice_date;
			
			$date_of_taxable_supply = new Form_Field_Date('date_of_taxable_supply', 'Date of taxable supply:');
			$date_of_taxable_supply->setDefaultValue( Data_DateTime::now() );
			$date_of_taxable_supply->setFieldValueCatcher( function( Data_DateTime $value ) {
				$this->new_correction_invoice->setDateOfTaxableSupply( $value );
			} );
			$fields[] = $date_of_taxable_supply;
			
			$due_date = new Form_Field_Date('due_date', 'Due date:');
			$due_date->setDefaultValue( Data_DateTime::now() );
			$due_date->setFieldValueCatcher( function( Data_DateTime $value ) {
				$this->new_correction_invoice->setDueDate( $value );
			} );
			$fields[] = $due_date;
			
			$i = 0;
			foreach( $this->new_correction_invoice->getItems() as $item ) {
				$i++;
				
				$number_of_units = new Form_Field_Float('/number_of_units/'.$i, '' );
				$number_of_units->setDefaultValue( $item->getNumberOfUnits() );
				$number_of_units->input()->setDataAttribute('i', $i);
				$number_of_units->input()->setDataAttribute('vat', $item->getVatRate());
				$number_of_units->input()->setDataAttribute('round_with_vat', $pricelist->getRoundPrecision_WithVAT());
				$number_of_units->input()->setDataAttribute('round_without_vat', $pricelist->getRoundPrecision_WithoutVAT());
				$number_of_units->input()->setDataAttribute('round_vat', $pricelist->getRoundPrecision_VAT());
				$number_of_units->input()->addJsAction('onchange', "CorrectionInvoice.calcItem_numberOfUnits(this);");
				$number_of_units->setFieldValueCatcher( function( float $value ) use ($item) {
					$item->setNumberOfUnits( $value, $item->getMeasureUnit() );
				} );
				$fields[] = $number_of_units;
				
				$per_unit_with_vat = new Form_Field_Float('/price_per_unit_with_vat/'.$i, '' );
				$per_unit_with_vat->setDefaultValue( $pricelist->round_WithVAT( -1*$item->getPricePerUnit_WithVat() ) );
				$per_unit_with_vat->input()->setDataAttribute('i', $i);
				$per_unit_with_vat->input()->setDataAttribute('vat', $item->getVatRate());
				$per_unit_with_vat->input()->setDataAttribute('round_with_vat', $pricelist->getRoundPrecision_WithVAT());
				$per_unit_with_vat->input()->setDataAttribute('round_without_vat', $pricelist->getRoundPrecision_WithoutVAT());
				$per_unit_with_vat->input()->setDataAttribute('round_vat', $pricelist->getRoundPrecision_VAT());
				$per_unit_with_vat->input()->addJsAction('onchange', "CorrectionInvoice.calcItem_WithVAT(this);");
				$fields[] = $per_unit_with_vat;
				
				$per_unit_without_vat = new Form_Field_Float('/price_per_unit_without_vat/'.$i, '' );
				$per_unit_without_vat->setDefaultValue( $pricelist->round_WithoutVAT( -1*$item->getPricePerUnit_WithoutVat() ) );
				$per_unit_without_vat->input()->setDataAttribute('i', $i);
				$per_unit_without_vat->input()->setDataAttribute('vat', $item->getVatRate());
				$per_unit_without_vat->input()->setDataAttribute('round_with_vat', $pricelist->getRoundPrecision_WithVAT());
				$per_unit_without_vat->input()->setDataAttribute('round_without_vat', $pricelist->getRoundPrecision_WithoutVAT());
				$per_unit_without_vat->input()->setDataAttribute('round_vat', $pricelist->getRoundPrecision_VAT());
				$per_unit_without_vat->input()->addJsAction('onchange', "CorrectionInvoice.calcItem_WithoutVAT(this);");
				$per_unit_without_vat->setFieldValueCatcher( function( float $value ) use ($item) {
					$price = new class extends EShopEntity_Price {};
					
					$price->setPricelistCode( $this->getPricelistCode() );
					$price->setVatRate( $item->getVatRate() );
					
					$price->setPriceWithoutVAT( $value );
					
					$item->setupPricePerUnit( $price );
				} );
				
				$fields[] = $per_unit_without_vat;
				
				$vat_rate = new Form_Field_Float('/vat_rate/'.$i, '' );
				$vat_rate->setDefaultValue( $item->getVatRate() );
				$vat_rate->setIsReadonly( true );
				$fields[] = $vat_rate;
				
				$total_amount_without_vat = new Form_Field_Float('/total_amount_without_vat/'.$i, '' );
				$total_amount_without_vat->setDefaultValue( $pricelist->round_WithoutVAT( -1*$item->getTotalAmount_WithoutVat() ) );
				$total_amount_without_vat->setIsReadonly( true );
				$fields[] = $total_amount_without_vat;
				
				$total_amount_with_vat = new Form_Field_Float('/total_amount_with_vat/'.$i, '' );
				$total_amount_with_vat->setDefaultValue( $pricelist->round_WithVAT( -1*$item->getTotalAmount_WithVat() ) );
				$total_amount_with_vat->setIsReadonly( true );
				$fields[] = $total_amount_with_vat;
			}
			
			$this->correction_invoice_form = new Form( 'correction_invoice_form', $fields );
		}
		
		return $this->correction_invoice_form;
	}
	
	public function catchCorrectionInvoiceForm(): bool|static
	{
		$form = $this->getCreateCorrectionInvoiceForm();
		if(!$form->catch()) {
			return false;
		}
		
		$correction_invoice = $this->getNewCorrectionInvoice();
		
		foreach( $correction_invoice->items as $k=>$item ) {
			if($item->getNumberOfUnits()==0) {
				unset( $correction_invoice->items[$k] );
			}
		}
		
		$correction_invoice->recalculate();
		
		$correction_invoice->save();
		
		return $correction_invoice;
	}
	
	public function isEditable() : bool
	{
		return false;
	}
	
	public function generatePDF() : string
	{
		return Managers_General::Invoices()->generateInvoicePDF( $this );
	}
	
	
	public function getPaymentQrCodeImageFilename(): string
	{
		return $this->payment_qr_code_image_filename;
	}
	
	public function setPaymentQrCodeImageFilename( string $payment_qr_code_image_filename ): void
	{
		$this->payment_qr_code_image_filename = $payment_qr_code_image_filename;
	}
	
	
	public function getTextBeforeItems(): string
	{
		return $this->text_before_items;
	}
	
	public function setTextBeforeItems( string $text_before_items ): void
	{
		$this->text_before_items = $text_before_items;
	}
	
	public function getTextAfterItems(): string
	{
		return $this->text_after_items;
	}
	
	public function setTextAfterItems( string $text_after_items ): void
	{
		$this->text_after_items = $text_after_items;
	}
	
	
	protected ?Form $texts_edit_form = null;
	
	public function getTextsEditForm(): Form
	{
		if(!$this->texts_edit_form) {
			$this->texts_edit_form = $this->createForm('texts_edit_form', ['text_before_items', 'text_after_items']);
		}
		
		return $this->texts_edit_form;
	}
}
