<?php
namespace JetShop;

use Jet\DataModel;
use Jet\DataModel_Definition;

use JetApplication\Invoice;
use JetApplication\Invoice_Item_SetItem;
use JetApplication\Entity_AccountingDocument_Item;

#[DataModel_Definition(
	name: 'invoice_item',
	database_table_name: 'invoices_items',
	parent_model_class: Invoice::class
)]
abstract class Core_Invoice_Item extends Entity_AccountingDocument_Item {
	
	
	#[DataModel_Definition(
		type: DataModel::TYPE_INT,
		is_id: true,
		related_to: 'main.id',
	)]
	protected int $invoice_id = 0;
	
	
	/**
	 * @var Invoice_Item_SetItem[]
	 */
	#[DataModel_Definition(
		type: DataModel::TYPE_DATA_MODEL,
		data_model_class: Invoice_Item_SetItem::class
	)]
	protected array $set_items = [];
	
	
	/**
	 * @return Invoice_Item_SetItem[]
	 */
	public function getSetItems(): array
	{
		return $this->set_items;
	}
	
	public function negatePrice() : void
	{
		$this->price_per_unit = -1 * $this->price_per_unit;
		$this->price_per_unit_with_vat = -1 * $this->price_per_unit_with_vat;
		$this->price_per_unit_without_vat = -1 * $this->price_per_unit_without_vat;
		$this->price_per_unit_vat = -1 * $this->price_per_unit_vat;
	}
}