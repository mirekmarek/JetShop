<?php
namespace JetShop;

use Jet\Data_DateTime;
use Jet\DataModel;
use Jet\DataModel_Definition;

use Jet\Form;
use Jet\Logger;
use JetApplication\EShopEntity_Admin_Interface;
use JetApplication\EShopEntity_Admin_Trait;
use JetApplication\Admin_Managers_DeliveryNote;
use JetApplication\Context_ProvidesContext_Interface;
use JetApplication\EShopEntity_AccountingDocument;
use JetApplication\DeliveryNote_Item;
use JetApplication\EShopEntity_Definition;
use JetApplication\EShopEntity_HasNumberSeries_Interface;
use JetApplication\Order;

#[DataModel_Definition(
	name: 'delivery_note',
	database_table_name: 'delivery_notes',
)]
#[EShopEntity_Definition(
	admin_manager_interface: Admin_Managers_DeliveryNote::class
)]
abstract class Core_DeliveryNote extends EShopEntity_AccountingDocument implements
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
	
	/**
	 * @var DeliveryNote_Item[]
	 */
	#[DataModel_Definition(
		type: DataModel::TYPE_DATA_MODEL,
		data_model_class: DeliveryNote_Item::class
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
		return 'Delivery note';
	}
	
	
	/**
	 * @return DeliveryNote_Item[]
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
	
	
	
	public static function createByOrder( Order $order ) : static
	{
		$invoice = parent::createByOrder( $order );
		
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
			event: 'delivery_note_cancelled',
			event_message: 'Delivery note '.$this->getNumber().' cancelled',
			context_object_id: $this->getId(),
			context_object_name: $this->getNumber(),
			context_object_data: $this
		);
	}
	
	
	public function isEditable(): bool
	{
		if( !static::getAdminManager()::getCurrentUserCanEdit() ) {
			return false;
		}
		
		return parent::isEditable();
	}
	
	
	public function setEditable( bool $editable ): void
	{
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
	
}
