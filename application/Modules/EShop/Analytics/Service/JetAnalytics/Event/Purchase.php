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
	name: 'ja_event_purchase',
	database_table_name: 'ja_event_purchase',
)]
class Event_Purchase extends Event_CheckoutStarted
{
	
	#[DataModel_Definition(
		type: DataModel::TYPE_DATA_MODEL,
		data_model_class: Event_Purchase_Item::class
	)]
	protected array $items = [];
	
	#[DataModel_Definition(
		type: DataModel::TYPE_STRING,
		max_len: 50
	)]
	protected string $order_number = '';
	
	#[DataModel_Definition(
		type: DataModel::TYPE_INT
	)]
	protected int $order_id = 0;
	
	
	public function init( Order $order ) : void
	{
		parent::init( $order );
		
		$this->session->setPurchased( true );
	}
	
	protected function initItems( Order $order ) : void
	{
		foreach($order->getItems() as $item) {
			$this->items[] = Event_Purchase_Item::createNew( $this, $item );
		}
	}
	
	
	public function getTitle(): string
	{
		return Tr::_('Purchase');
	}
	
	public function getCssClass(): string
	{
		return 'success';
	}
	
	public function getItems() : string
	{
		return 'thumbs-up';
	}
	
	
	public function showShortDetails(): string
	{
		//TODO:
		return '';
	}
	
}