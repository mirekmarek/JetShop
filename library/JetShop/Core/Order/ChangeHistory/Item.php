<?php
namespace JetShop;

use Jet\DataModel_Definition;
use JetApplication\EShopEntity_ChangeHistory_Item;
use JetApplication\Order_ChangeHistory;

#[DataModel_Definition(
	name: 'orders_change_history_item',
	database_table_name: 'orders_change_history_items',
	parent_model_class: Order_ChangeHistory::class
)]
abstract class Core_Order_ChangeHistory_Item extends EShopEntity_ChangeHistory_Item {
}