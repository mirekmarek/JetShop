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
use JetApplication\Order_Item;
use JetApplication\Pricelists;

#[DataModel_Definition(
	name: 'ja_event_checkout_started_item',
	database_table_name: 'ja_event_checkout_started_item',
	parent_model_class: Event_CheckoutStarted::class,
	id_controller_class: DataModel_IDController_AutoIncrement::class,
	id_controller_options: [
		'id_property_name' => 'id'
	]
)]
class Event_CheckoutStarted_Item extends DataModel_Related_1toN implements EShopEntity_HasEShopRelation_Interface
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
		max_len: 100,
		is_key: true,
	)]
	protected string $type = '';
	
	#[DataModel_Definition(
		type: DataModel::TYPE_STRING,
		max_len: 100,
		is_key: true,
	)]
	protected string $sub_type = '';
	
	#[DataModel_Definition(
		type: DataModel::TYPE_STRING,
		max_len: 100,
		is_key: true,
	)]
	protected string $item_code = '';
	
	#[DataModel_Definition(
		type: DataModel::TYPE_STRING,
		max_len: 100,
		is_key: true,
	)]
	protected string $sub_code = '';
	
	#[DataModel_Definition(
		type: DataModel::TYPE_INT,
		is_key: true,
	)]
	protected int $item_id = 0;
	
	#[DataModel_Definition(
		type: DataModel::TYPE_FLOAT
	)]
	protected float $number_of_units = 0.0;
	
	#[DataModel_Definition(
		type: DataModel::TYPE_STRING,
		max_len: 64,
	)]
	protected string $measure_unit = '';
	
	
	#[DataModel_Definition(
		type: DataModel::TYPE_FLOAT
	)]
	protected float $vat_rate = 0.0;
	
	
	#[DataModel_Definition(
		type: DataModel::TYPE_FLOAT
	)]
	protected float $price_per_unit = 0.0;
	
	#[DataModel_Definition(
		type: DataModel::TYPE_FLOAT
	)]
	protected float $price_per_unit_with_vat = 0.0;
	
	#[DataModel_Definition(
		type: DataModel::TYPE_FLOAT
	)]
	protected float $price_per_unit_without_vat = 0.0;
	
	#[DataModel_Definition(
		type: DataModel::TYPE_FLOAT
	)]
	protected float $price_per_unit_vat = 0.0;
	
	
	#[DataModel_Definition(
		type: DataModel::TYPE_FLOAT
	)]
	protected float $total_amount = 0.0;
	
	#[DataModel_Definition(
		type: DataModel::TYPE_FLOAT
	)]
	protected float $total_amount_with_vat = 0.0;
	
	#[DataModel_Definition(
		type: DataModel::TYPE_FLOAT
	)]
	protected float $total_amount_without_vat = 0.0;
	
	#[DataModel_Definition(
		type: DataModel::TYPE_FLOAT
	)]
	protected float $total_amount_vat = 0.0;
	
	
	#[DataModel_Definition(
		type: DataModel::TYPE_FLOAT
	)]
	protected float $set_discount_per_unit = 0.0;
	
	
	public function getArrayKeyValue(): string
	{
		return $this->id;
	}
	
	public static function createNew( Event $event, Order_Item $order_item ) : static
	{
		$item = new static();
		$item->setEshop( $event->getEshop() );
		$item->session_id = $event->getSessionId();
		$item->date_time = $event->getDateTime();
		
		$pricelist = Pricelists::getCurrent();
		
		$item->currency_code = $pricelist->getCurrencyCode();
		$item->pricelist_code = $pricelist->getCode();
		
		
		$item->type = $order_item->getType();
		$item->sub_type = $order_item->getSubType();
		$item->item_code = $order_item->getItemCode();
		$item->sub_code = $order_item->getSubCode();
		$item->item_id = $order_item->getItemId();
		$item->number_of_units = $order_item->getNumberOfUnits();
		$item->measure_unit = $order_item->getNumberOfUnits();
		$item->vat_rate = $order_item->getVatRate();
		$item->price_per_unit = $order_item->getPricePerUnit();
		$item->price_per_unit_with_vat = $order_item->getPricePerUnit_WithVat();
		$item->price_per_unit_without_vat = $order_item->getPricePerUnit_WithoutVat();
		$item->price_per_unit_vat = $order_item->getPricePerUnit_Vat();
		$item->total_amount = $order_item->getTotalAmount();
		$item->total_amount_with_vat = $order_item->getTotalAmount_WithVat();
		$item->total_amount_without_vat = $order_item->getTotalAmount_WithoutVat();
		$item->total_amount_vat = $order_item->getTotalAmount_Vat();
		$item->set_discount_per_unit = $order_item->getSetDiscountPerUnit();
		
		return $item;
	}
	
	
}