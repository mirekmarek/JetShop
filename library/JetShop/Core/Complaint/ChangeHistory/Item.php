<?php
namespace JetShop;

use Jet\DataModel_Definition;
use JetApplication\Complaint_ChangeHistory;
use JetApplication\EShopEntity_ChangeHistory_Item;

#[DataModel_Definition(
	name: 'complaints_change_history_item',
	database_table_name: 'complaints_change_history_items',
	parent_model_class: Complaint_ChangeHistory::class
)]
abstract class Core_Complaint_ChangeHistory_Item extends EShopEntity_ChangeHistory_Item {

}