<?php
/**
 * @copyright Copyright (c) Miroslav Marek <mirek.marek@web-jet.cz>
 * @license EUPL 1.2  https://eupl.eu/1.2/en/
 * @author Miroslav Marek <mirek.marek@web-jet.cz>
 */
namespace JetApplicationModule\EShop\Analytics\Service\JetAnalytics;

use Jet\DataModel;
use Jet\DataModel_Definition;
use JetApplication\Order;

#[DataModel_Definition(
	name: 'ja_event_checkout_in_progress',
	database_table_name: 'ja_event_checkout_in_progress',
)]
class Event_CheckoutInProgress extends Event_CheckoutStarted
{
	
	#[DataModel_Definition(
		type: DataModel::TYPE_DATA_MODEL,
		data_model_class: Event_CheckoutInProgress_Item::class
	)]
	protected array $items = [];
	
	protected function initItems( Order $order ) : void
	{
		foreach($order->getItems() as $item) {
			$this->items[] = Event_CheckoutInProgress_Item::createNew( $this, $item );
		}
	}
}