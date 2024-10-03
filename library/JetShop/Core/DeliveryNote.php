<?php
namespace JetShop;

use Jet\Data_DateTime;
use Jet\DataModel;
use Jet\DataModel_Definition;

use Jet\Logger;
use JetApplication\Context_ProvidesContext_Interface;
use JetApplication\Entity_AccountingDocument;
use JetApplication\DeliveryNote_Item;
use JetApplication\NumberSeries_Entity_Interface;
use JetApplication\Order;

#[DataModel_Definition(
	name: 'delivery_note',
	database_table_name: 'delivery_notes',
)]
abstract class Core_DeliveryNote extends Entity_AccountingDocument implements NumberSeries_Entity_Interface, Context_ProvidesContext_Interface
{
	
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
	
}
