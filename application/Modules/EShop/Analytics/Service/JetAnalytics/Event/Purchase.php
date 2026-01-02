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
		
		$this->order_id = $order->getId();
		$this->order_number = $order->getNumber();
		
		$this->session->setPurchased( true );
	}
	
	protected function initItems( Order $order ) : void
	{
		foreach($order->getItems() as $item) {
			foreach(Event_Purchase_Item::createNew( $this, $item ) as $created_item) {
				$this->items[] = $created_item;
			}
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
	
	public function getIcon() : string
	{
		return 'thumbs-up';
	}
	
	public function save() : void
	{
		if(($e_id=static::dataFetchOne(
			select: ['id'],
			where: ['order_id'=>$this->order_id]
		))) {
			$this->id = $e_id;
			$this->setIsSaved();
			return;
		}
		
		parent::save();
	}
	
	public function getOrderNumber(): string
	{
		return $this->order_number;
	}
	
	public function getOrderId(): int
	{
		return $this->order_id;
	}
	
	/**
	 * @return array<Event_Purchase_Item>
	 */
	public function getItems(): array
	{
		return $this->items;
	}
	
	
	
	
}