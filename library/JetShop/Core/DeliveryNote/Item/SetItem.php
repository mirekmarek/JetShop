<?php
namespace JetShop;

use Jet\DataModel;
use Jet\DataModel_Definition;

use JetApplication\EShopEntity_AccountingDocument_Item_SetItem;
use JetApplication\DeliveryNote_Item;

#[DataModel_Definition(
	name: 'delivery_note_item_set_item',
	database_table_name: 'delivery_notes_items_set_items',
	parent_model_class: DeliveryNote_Item::class
)]
abstract class Core_DeliveryNote_Item_SetItem extends EShopEntity_AccountingDocument_Item_SetItem {
	
	#[DataModel_Definition(
		type: DataModel::TYPE_INT,
		is_id: true,
		related_to: 'main.id',
	)]
	protected int $delivery_note_id = 0;
	
	#[DataModel_Definition(
		type: DataModel::TYPE_INT,
		is_id: true,
		related_to: 'parent.id',
	)]
	protected int $delivery_note_item_id = 0;
	
}