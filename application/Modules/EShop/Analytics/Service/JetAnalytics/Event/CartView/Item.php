<?php
/**
 * @copyright Copyright (c) Miroslav Marek <mirek.marek@web-jet.cz>
 * @license EUPL 1.2  https://eupl.eu/1.2/en/
 * @author Miroslav Marek <mirek.marek@web-jet.cz>
 */
namespace JetApplicationModule\EShop\Analytics\Service\JetAnalytics;

use Jet\Data_DateTime;
use Jet\DataModel;
use Jet\DataModel_Definition;
use Jet\DataModel_IDController_AutoIncrement;
use Jet\DataModel_Related_1toN;
use JetApplication\EShopEntity_HasEShopRelation_Interface;
use JetApplication\EShopEntity_HasEShopRelation_Trait;
use JetApplication\Pricelists;
use JetApplication\ShoppingCart_Item;

#[DataModel_Definition(
	name: 'ja_event_cart_view_item',
	database_table_name: 'ja_event_cart_view_item',
	parent_model_class: Event_CartView::class,
	id_controller_class: DataModel_IDController_AutoIncrement::class,
	id_controller_options: [
		'id_property_name' => 'id'
	]
)]
class Event_CartView_Item extends DataModel_Related_1toN implements EShopEntity_HasEShopRelation_Interface
{
	
	use EShopEntity_HasEShopRelation_Trait;
	
	#[DataModel_Definition(
		type: DataModel::TYPE_ID_AUTOINCREMENT,
		is_id: true
	)]
	protected int $id = 0;
	
	#[DataModel_Definition(
		type: DataModel::TYPE_DATE_TIME
	)]
	protected ?Data_DateTime $date_time = null;
	
	#[DataModel_Definition(
		type: DataModel::TYPE_INT,
		is_key: true,
		related_to: 'main.id'
	)]
	protected int $event_id = 0;
	
	#[DataModel_Definition(
		type: DataModel::TYPE_INT,
		is_key: true
	)]
	protected int $session_id = 0;
	
	#[DataModel_Definition(
		type: DataModel::TYPE_INT,
		is_key: true
	)]
	protected string $product_id = '';
	
	#[DataModel_Definition(
		type: DataModel::TYPE_FLOAT
	)]
	protected float $number_of_units = 0.0;
	
	#[DataModel_Definition(
		type: DataModel::TYPE_FLOAT
	)]
	protected float $price_per_unit = 0.0;
	
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
		type: DataModel::TYPE_STRING,
		max_len: 50
	)]
	protected string $measure_unit_code = '';
	
	#[DataModel_Definition(
		type: DataModel::TYPE_INT
	)]
	protected int $selected_gift_id = 0;
	
	#[DataModel_Definition(
		type: DataModel::TYPE_INT
	)]
	protected int $auto_offer_id = 0;
	
	
	
	public function getArrayKeyValue(): string
	{
		return $this->id;
	}
	
	public static function createNew( Event $event, ShoppingCart_Item $cart_item ) : static
	{
		$item = new static();
		$item->setEshop( $event->getEshop() );
		$item->session_id = $event->getSessionId();
		$item->date_time = $event->getDateTime();
		
		$pricelist = Pricelists::getCurrent();
		
		$item->product_id = $cart_item->getProductId();
		$item->number_of_units = $cart_item->getNumberOfUnits();
		$item->price_per_unit = $cart_item->getProduct()->getPrice( $pricelist );
		$item->currency_code = $pricelist->getCurrencyCode();
		$item->pricelist_code = $pricelist->getCode();
		$item->measure_unit_code = $cart_item->getMeasureUnit()?$cart_item->getMeasureUnit()->getCode():'';
		$item->selected_gift_id = $cart_item->getSelectedGiftId();
		$item->auto_offer_id = $cart_item->getAutoOfferId();
		
		return $item;
	}
	
	public function getId(): int
	{
		return $this->id;
	}
	
	public function getDateTime(): ?Data_DateTime
	{
		return $this->date_time;
	}
	
	public function getEventId(): int
	{
		return $this->event_id;
	}
	
	public function getSessionId(): int
	{
		return $this->session_id;
	}
	
	public function getProductId(): string
	{
		return $this->product_id;
	}
	
	public function getNumberOfUnits(): float
	{
		return $this->number_of_units;
	}
	
	public function getPricePerUnit(): float
	{
		return $this->price_per_unit;
	}
	
	public function getCurrencyCode(): string
	{
		return $this->currency_code;
	}
	
	public function getPricelistCode(): string
	{
		return $this->pricelist_code;
	}
	
	public function getMeasureUnitCode(): string
	{
		return $this->measure_unit_code;
	}
	
	public function getSelectedGiftId(): int
	{
		return $this->selected_gift_id;
	}
	
	public function getAutoOfferId(): int
	{
		return $this->auto_offer_id;
	}
	
	
	
}