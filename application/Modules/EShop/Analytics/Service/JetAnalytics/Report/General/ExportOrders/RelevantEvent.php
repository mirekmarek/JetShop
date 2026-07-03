<?php
/**
 * @copyright Copyright (c) Miroslav Marek <mirek.marek@web-jet.cz>
 * @license EUPL 1.2  https://eupl.eu/1.2/en/
 * @author Miroslav Marek <mirek.marek@web-jet.cz>
 */
namespace JetApplicationModule\EShop\Analytics\Service\JetAnalytics;



use JetApplication\Product;

class Report_General_ExportOrders_RelevantEvent
{
	protected Event_Purchase $event;
	
	/**
	 * @var array<Event_Purchase_Item>
	 */
	protected array $relevant_items = [];
	protected float $relevant_qty = 0.0;
	protected float $relevant_amount = 0.0;
	
	/**
	 * @var array<Event_Purchase_Item>
	 */
	protected array $non_relevant_items = [];
	protected float $non_relevant_qty = 0.0;
	protected float $non_relevant_amount = 0.0;
	
	/**
	 * @param Event_Purchase $event
	 * @param Event_Purchase_Item[] $relevant_items
	 * @param Event_Purchase_Item[] $non_relevant_items
	 */
	public function __construct( Event_Purchase $event, array $relevant_items, array $non_relevant_items )
	{
		
		
		$this->event = $event;
		$this->relevant_items = $relevant_items;
		$this->non_relevant_items = $non_relevant_items;
		
		foreach($relevant_items as $relevant_item) {
			$this->relevant_qty += $relevant_item->getNumberOfUnits();
			$this->relevant_amount += $relevant_item->getTotalAmountWithVat();
		}
		
		foreach($non_relevant_items as $non_relevant_item) {
			$this->non_relevant_qty += $non_relevant_item->getNumberOfUnits();
			$this->non_relevant_amount += $non_relevant_item->getTotalAmountWithVat();
		}
	}
	
	public function getEvent(): Event_Purchase
	{
		return $this->event;
	}
	
	/**
	 * @return Event_Purchase_Item[]
	 */
	public function getRelevantItems(): array
	{
		return $this->relevant_items;
	}
	
	public function getRelevantQty(): float
	{
		return $this->relevant_qty;
	}
	
	public function getRelevantAmount(): float
	{
		return $this->relevant_amount;
	}
	
	/**
	 * @return Event_Purchase_Item[]
	 */
	public function getNonRelevantItems(): array
	{
		return $this->non_relevant_items;
	}
	
	public function getNonRelevantQty(): float
	{
		return $this->non_relevant_qty;
	}
	
	public function getNonRelevantAmount(): float
	{
		return $this->non_relevant_amount;
	}
	
	protected static array $names = [];
	
	public function getItemName( Event_Purchase_Item $item ) : string
	{
		if(!array_key_exists($item->getItemId(), static::$names)) {
			static::$names[$item->getItemId()] = Product::get($item->getItemId())?->getAdminTitle() ?? '??? '.$item->getItemId();
		}
		
		return static::$names[$item->getItemId()];
	}
	
	
}