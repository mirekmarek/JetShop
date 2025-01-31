<?php
/**
 * @copyright Copyright (c) Miroslav Marek <mirek.marek@web-jet.cz>
 * @license EUPL 1.2  https://eupl.eu/1.2/en/
 * @author Miroslav Marek <mirek.marek@web-jet.cz>
 */
namespace JetShop;


use Jet\Data_DateTime;
use Jet\DataModel;
use Jet\DataModel_Definition;

use JetApplication\DeliveryTerm_Info;
use JetApplication\Order;
use JetApplication\Order_Item_SetItem;
use JetApplication\EShopEntity_AccountingDocument_Item;

#[DataModel_Definition(
	name: 'order_item',
	database_table_name: 'orders_items',
	parent_model_class: Order::class
)]
abstract class Core_Order_Item extends EShopEntity_AccountingDocument_Item {

	
	#[DataModel_Definition(
		type: DataModel::TYPE_INT,
		is_id: true,
		related_to: 'main.id',
	)]
	protected int $order_id = 0;
	
	#[DataModel_Definition(
		type: DataModel::TYPE_FLOAT
	)]
	protected float $number_of_units_available = 0.0;
	
	#[DataModel_Definition(
		type: DataModel::TYPE_FLOAT
	)]
	protected float $number_of_units_not_available = 0.0;
	
	
	#[DataModel_Definition(
		type: DataModel::TYPE_DATE
	)]
	protected Data_DateTime|null $available_units_promised_delivery_date = null;
	
	#[DataModel_Definition(
		type: DataModel::TYPE_DATE
	)]
	protected Data_DateTime|null $not_available_units_promised_delivery_date = null;
	
	#[DataModel_Definition(
		type: DataModel::TYPE_STRING,
		max_len: 65536
	)]
	protected string|null $not_available_units_delivery_tem_info = '';
	
	
	/**
	 * @var Order_Item_SetItem[]
	 */
	#[DataModel_Definition(
		type: DataModel::TYPE_DATA_MODEL,
		data_model_class: Order_Item_SetItem::class
	)]
	protected array $set_items = [];
	
	
	/**
	 * @return Order_Item_SetItem[]
	 */
	public function getSetItems(): array
	{
		return $this->set_items;
	}
	
	
	public function getNumberOfUnitsAvailable(): float
	{
		return $this->number_of_units_available;
	}
	
	public function setNumberOfUnitsAvailable( float $number_of_units_available ): void
	{
		$this->number_of_units_available = $number_of_units_available;
	}
	
	public function getNumberOfUnitsNotAvailable(): float
	{
		return $this->number_of_units_not_available;
	}
	
	public function setNumberOfUnitsNotAvailable( float $number_of_units_not_available ): void
	{
		$this->number_of_units_not_available = $number_of_units_not_available;
	}
	
	
	public function getAvailableUnitsPromisedDeliveryDate(): ?Data_DateTime
	{
		return $this->available_units_promised_delivery_date;
	}
	
	public function setAvailableUnitsPromisedDeliveryDate( Data_DateTime|null|string $date ): void
	{
		$this->available_units_promised_delivery_date = Data_DateTime::catchDate( $date );
	}
	
	public function getNotAvailableUnitsPromisedDeliveryDate(): ?Data_DateTime
	{
		return $this->not_available_units_promised_delivery_date;
	}
	
	public function setNotAvailableUnitsPromisedDeliveryDate( Data_DateTime|null|string $date ): void
	{
		$this->not_available_units_promised_delivery_date = Data_DateTime::catchDate( $date );
	}
	
	public function getNotAvailableUnitsDeliveryTemInfo(): ?DeliveryTerm_Info
	{
		return $this->not_available_units_delivery_tem_info ? DeliveryTerm_Info::fromJSON( $this->not_available_units_delivery_tem_info ) : null;
	}
	
	public function setNotAvailableUnitsDeliveryTemInfo( ?DeliveryTerm_Info $not_available_units_delivery_tem_info ): void
	{
		if($not_available_units_delivery_tem_info) {
			$not_available_units_delivery_tem_info = $not_available_units_delivery_tem_info->toJSON();
		} else {
			$not_available_units_delivery_tem_info = '';
		}
		
		$this->not_available_units_delivery_tem_info = $not_available_units_delivery_tem_info;
	}
	
	public function clone() : static
	{
		$new_item = parent::clone();

		$clone = [
			'number_of_units_available',
			'number_of_units_not_available',
			'available_units_promised_delivery_date',
			'not_available_units_promised_delivery_date',
			'not_available_units_delivery_tem_info',
		];
		
		foreach($clone as $k) {
			$new_item->{$k} = $this->{$k};
		}
		
		return $new_item;
	}
	
}