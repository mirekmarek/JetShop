<?php
/**
 * @copyright Copyright (c) Miroslav Marek <mirek.marek@web-jet.cz>
 * @license EUPL 1.2  https://eupl.eu/1.2/en/
 * @author Miroslav Marek <mirek.marek@web-jet.cz>
 */
namespace JetShop;


use Jet\DataModel_Definition;
use JetApplication\EShopEntity_ChangeHistory_Item;
use JetApplication\ReturnOfGoods_ChangeHistory;

#[DataModel_Definition(
	name: 'return_of_goods_change_history_item',
	database_table_name: 'returns_of_goods_change_history_items',
	parent_model_class: ReturnOfGoods_ChangeHistory::class
)]
abstract class Core_ReturnOfGoods_ChangeHistory_Item extends EShopEntity_ChangeHistory_Item {

}