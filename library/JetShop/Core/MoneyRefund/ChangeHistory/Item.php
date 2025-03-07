<?php
/**
 * @copyright Copyright (c) Miroslav Marek <mirek.marek@web-jet.cz>
 * @license EUPL 1.2  https://eupl.eu/1.2/en/
 * @author Miroslav Marek <mirek.marek@web-jet.cz>
 */
namespace JetShop;


use Jet\DataModel_Definition;
use JetApplication\MoneyRefund_ChangeHistory;
use JetApplication\EShopEntity_ChangeHistory_Item;

#[DataModel_Definition(
	name: 'money_refunds_change_history_item',
	database_table_name: 'money_refunds_change_history_items',
	parent_model_class: MoneyRefund_ChangeHistory::class
)]
abstract class Core_MoneyRefund_ChangeHistory_Item extends EShopEntity_ChangeHistory_Item {

}