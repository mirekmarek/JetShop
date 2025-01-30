<?php
namespace JetShop;

use Jet\DataModel;
use Jet\DataModel_Definition;

use JetApplication\EShopEntity_AccountingDocument_Item_SetItem;
use JetApplication\Invoice_Item;

#[DataModel_Definition(
	name: 'invoice_item_set_item',
	database_table_name: 'invoices_items_set_items',
	parent_model_class: Invoice_Item::class
)]
abstract class Core_Invoice_Item_SetItem extends EShopEntity_AccountingDocument_Item_SetItem {
	
	#[DataModel_Definition(
		type: DataModel::TYPE_INT,
		is_id: true,
		related_to: 'main.id',
	)]
	protected int $invoice_id = 0;
	
	#[DataModel_Definition(
		type: DataModel::TYPE_INT,
		is_id: true,
		related_to: 'parent.id',
	)]
	protected int $invoice_item_id = 0;
	
}