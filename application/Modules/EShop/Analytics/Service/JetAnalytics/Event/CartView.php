<?php
/**
 * @copyright Copyright (c) Miroslav Marek <mirek.marek@web-jet.cz>
 * @license EUPL 1.2  https://eupl.eu/1.2/en/
 * @author Miroslav Marek <mirek.marek@web-jet.cz>
 */
namespace JetApplicationModule\EShop\Analytics\Service\JetAnalytics;

use Jet\DataModel;
use Jet\DataModel_Definition;
use JetApplication\Pricelists;
use JetApplication\ShoppingCart;

#[DataModel_Definition(
	name: 'ja_event_cart_view',
	database_table_name: 'ja_event_cart_view',
)]
class Event_CartView extends Event
{
	
	#[DataModel_Definition(
		type: DataModel::TYPE_DATA_MODEL,
		data_model_class: Event_CartView_Item::class
	)]
	protected array $items = [];
	
	#[DataModel_Definition(
		type: DataModel::TYPE_STRING,
		max_len: 50
	)]
	protected string $currency_code = '';
	
	#[DataModel_Definition(
		type: DataModel::TYPE_STRING,
		max_len: 50
	)]
	protected string $pricelist_code = '';
	
	#[DataModel_Definition(
		type: DataModel::TYPE_FLOAT
	)]
	protected float $amount = 0.0;
	
	
	public function cancelDefaultEvent(): bool
	{
		return false;
	}
	
	public function init( ShoppingCart $cart ) : void
	{
		foreach($cart->getItems() as $item) {
			$this->items[] = Event_CartView_Item::createNew( $this, $item );
		}
		
		$pricelist = Pricelists::getCurrent();
		
		$this->currency_code = $pricelist->getCurrencyCode();
		$this->pricelist_code = $pricelist->getCode();
		$this->amount = $cart->getAmount();
	}
}