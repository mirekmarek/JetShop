<?php
/**
 * @copyright Copyright (c) Miroslav Marek <mirek.marek@web-jet.cz>
 * @license EUPL 1.2  https://eupl.eu/1.2/en/
 * @author Miroslav Marek <mirek.marek@web-jet.cz>
 */
namespace JetApplicationModule\EShop\Analytics\Service\JetAnalytics;

use Jet\DataModel;
use Jet\DataModel_Definition;
use Jet\Tr;
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
			foreach(Event_CheckoutInProgress_Item::createNew( $this, $item ) as $created_item) {
				$this->items[] = $created_item;
			}
		}
	}
	
	
	public function getTitle(): string
	{
		return Tr::_('Checkout in progress');
	}
	
	public function getCssClass(): string
	{
		return 'info';
	}
	
	public function getIcon(): string
	{
		return 'cash-register';
	}
	
	public function showLongDetails(): string
	{
		// TODO:
		return '';
	}
}