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
use Jet\Logger;
use JetApplication\CompanyInfo;
use JetApplication\EShopEntity_Admin_Interface;
use JetApplication\EShopEntity_Admin_Trait;
use JetApplication\Application_Service_Admin_ProformaInvoice;
use JetApplication\Context_ProvidesContext_Interface;
use JetApplication\EShopEntity_AccountingDocument;
use JetApplication\ProformaInvoice;
use JetApplication\ProformaInvoice_Item;
use JetApplication\EShopEntity_Definition;
use JetApplication\EShopEntity_HasNumberSeries_Interface;
use JetApplication\Invoices;
use JetApplication\Application_Service_General;
use JetApplication\Order;

#[DataModel_Definition(
	name: 'proforma_invoice',
	database_table_name: 'proforma_invoices',
)]
#[EShopEntity_Definition(
	entity_name_readable: 'Proforma invoice',
	admin_manager_interface: Application_Service_Admin_ProformaInvoice::class
)]
abstract class Core_ProformaInvoice extends EShopEntity_AccountingDocument implements
	EShopEntity_HasNumberSeries_Interface,
	Context_ProvidesContext_Interface,
	EShopEntity_Admin_Interface
{
	use EShopEntity_Admin_Trait;
	
	#[DataModel_Definition(
		type: DataModel::TYPE_DATE_TIME
	)]
	protected ?Data_DateTime $invoice_date = null;
	
	#[DataModel_Definition(
		type: DataModel::TYPE_DATE_TIME
	)]
	protected ?Data_DateTime $due_date = null;
	
	#[DataModel_Definition(
		type: DataModel::TYPE_DATE_TIME
	)]
	protected ?Data_DateTime $cancelled_date = null;
	
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
	 * @var ProformaInvoice_Item[]
	 */
	#[DataModel_Definition(
		type: DataModel::TYPE_DATA_MODEL,
		data_model_class: ProformaInvoice_Item::class
	)]
	protected array $items = [];
	
	
	public static function getNumberSeriesEntityIsPerShop() : bool
	{
		return true;
	}
	
	public static function getNumberSeriesEntityTitle() : string
	{
		return 'Proforma Invoices';
	}
	
	public static function getOrCreate( Order $order ) : static
	{
		$invoices = ProformaInvoice::getListByOrder( $order );
		if(!count($invoices)) {
			$invoice = Invoices::createProformaInvoiceForOrder( $order );
		} else {
			foreach($invoices as $invoice) {
				return $invoice;
			}
		}
		
		/** @noinspection PhpUndefinedVariableInspection */
		return $invoice;
	}
	
	
	/**
	 * @return ProformaInvoice_Item[]
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
	
	
	public static function createByOrder( Order $order ) : static
	{
		$invoice = parent::createByOrder( $order );
		
		$company_info = CompanyInfo::get( $invoice->getEshop() );
		$invoice->setTextBeforeItems( $company_info->getTextBeforeProformaInvoiceItems() );
		$invoice->setTextAfterItems( $company_info->getTextAftereProformaInvoiceItems() );
		
		
		$invoice->setInvoiceDate( Data_DateTime::now() );
		$invoice->setDueDate( Data_DateTime::now() );
		
		return $invoice;
	}
	
	
	
	
	public function cancel() : void
	{
		if($this->cancelled) {
			return;
		}
		
		$this->cancelled = true;
		$this->cancelled_date = Data_DateTime::now();
		$this->save();
		
		Logger::info(
			event: 'proforma_invoice_cancelled',
			event_message: 'Proforma Invoice '.$this->getNumber().' cancelled',
			context_object_id: $this->getId(),
			context_object_name: $this->getNumber(),
			context_object_data: $this
		);
	}
	
	
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
	
	public function isEditable() : bool
	{
		return false;
	}
	
	public function generatePDF() : string
	{
		return Application_Service_General::Invoices()->generateProformaInvoicePDF( $this );
	}
	
	public function getPaymentQrCodeImageFilename(): string
	{
		return $this->payment_qr_code_image_filename;
	}
	
	public function setPaymentQrCodeImageFilename( string $payment_qr_code_image_filename ): void
	{
		$this->payment_qr_code_image_filename = $payment_qr_code_image_filename;
	}
	
	public function getIsPaid() : bool
	{
		return false;
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
