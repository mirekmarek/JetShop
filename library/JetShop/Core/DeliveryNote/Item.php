<?php
namespace JetShop;

use Jet\DataModel;
use Jet\DataModel_Definition;

use JetApplication\DeliveryNote;
use JetApplication\DeliveryNote_Item_SetItem;
use JetApplication\EShopEntity_AccountingDocument_Item;

#[DataModel_Definition(
	name: 'delivery_note_item',
	database_table_name: 'delivery_notes_items',
	parent_model_class: DeliveryNote::class
)]
abstract class Core_DeliveryNote_Item extends EShopEntity_AccountingDocument_Item {
	
	
	#[DataModel_Definition(
		type: DataModel::TYPE_INT,
		is_id: true,
		related_to: 'main.id',
	)]
	protected int $delivery_note_id = 0;
	
	
	/**
	 * @var DeliveryNote_Item_SetItem[]
	 */
	#[DataModel_Definition(
		type: DataModel::TYPE_DATA_MODEL,
		data_model_class: DeliveryNote_Item_SetItem::class
	)]
	protected array $set_items = [];
	
	
	/**
	 * @return DeliveryNote_Item_SetItem[]
	 */
	public function getSetItems(): array
	{
		return $this->set_items;
	}
	
}