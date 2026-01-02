<?php
/**
 * @copyright Copyright (c) Miroslav Marek <mirek.marek@web-jet.cz>
 * @license EUPL 1.2  https://eupl.eu/1.2/en/
 * @author Miroslav Marek <mirek.marek@web-jet.cz>
 */
namespace JetApplicationModule\EShop\Analytics\Service\JetAnalytics;

use Jet\DataModel_Definition;

#[DataModel_Definition(
	name: 'ja_event_purchase_item_category',
	database_table_name: 'ja_event_purchase_item_category',
	parent_model_class: Event_CheckoutInProgress_Item::class

)]
class Event_Purchase_Item_Category extends Event_CheckoutStarted_Item_Category
{

}