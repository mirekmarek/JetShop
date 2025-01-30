<?php
namespace JetShop;

use Jet\DataModel;
use Jet\DataModel_Definition;

use JetApplication\InvoiceInAdvance;
use JetApplication\InvoiceInAdvance_Item_SetItem;
use JetApplication\EShopEntity_AccountingDocument_Item;

#[DataModel_Definition(
	name: 'invoice_in_advance_item',
	database_table_name: 'invoices_in_advance_items',
	parent_model_class: InvoiceInAdvance::class
)]
abstract class Core_InvoiceInAdvance_Item extends EShopEntity_AccountingDocument_Item {
	
	
	#[DataModel_Definition(
		type: DataModel::TYPE_INT,
		is_id: true,
		related_to: 'main.id',
	)]
	protected int $invoice_id = 0;
	
	
	/**
	 * @var InvoiceInAdvance_Item_SetItem[]
	 */
	#[DataModel_Definition(
		type: DataModel::TYPE_DATA_MODEL,
		data_model_class: InvoiceInAdvance_Item_SetItem::class
	)]
	protected array $set_items = [];
	
	
	/**
	 * @return InvoiceInAdvance_Item_SetItem[]
	 */
	public function getSetItems(): array
	{
		return $this->set_items;
	}
	
}