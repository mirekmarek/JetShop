<?php
namespace JetShop;

use Jet\DataModel;
use Jet\DataModel_Definition;
use Jet\DataModel_IDController_AutoIncrement;
use Jet\DataModel_Related_1toN;
use JetApplication\MeasureUnit;
use JetApplication\MeasureUnits;
use JetApplication\OrderPersonalReceipt;

#[DataModel_Definition(
	name: 'order_personal_receipt_item',
	database_table_name: 'order_personal_receipts_items',
	id_controller_class: DataModel_IDController_AutoIncrement::class,
	id_controller_options: ['id_property_name'=>'id'],
	parent_model_class: OrderPersonalReceipt::class
)]
abstract class Core_OrderPersonalReceipt_Item extends DataModel_Related_1toN
{
	#[DataModel_Definition(
		type: DataModel::TYPE_ID_AUTOINCREMENT,
		is_key: true,
		is_id: true
	)]
	protected int $id = 0;
	
	#[DataModel_Definition(
		type: DataModel::TYPE_INT,
		related_to: 'main.id',
		is_key: true
	)]
	protected int $personal_receipt_id = 0;
	
	#[DataModel_Definition(
		type: DataModel::TYPE_INT,
		is_key: true
	)]
	protected int $product_id = 0;
	
	#[DataModel_Definition(
		type: DataModel::TYPE_STRING,
		max_len: 100
	)]
	protected string $title = '';
	
	#[DataModel_Definition(
		type: DataModel::TYPE_STRING,
		max_len: 100
	)]
	protected string $internal_code = '';
	
	#[DataModel_Definition(
		type: DataModel::TYPE_STRING,
		max_len: 100
	)]
	protected string $EAN = '';
	
	#[DataModel_Definition(
		type: DataModel::TYPE_FLOAT
	)]
	protected float $number_of_units = 0;
	
	#[DataModel_Definition(
		type: DataModel::TYPE_STRING,
		max_len: 64,
	)]
	protected string $measure_unit = '';
	
	
	#[DataModel_Definition(
		type: DataModel::TYPE_STRING,
		max_len: 50,
	)]
	protected string $warehouse_sector = '';
	
	#[DataModel_Definition(
		type: DataModel::TYPE_STRING,
		max_len: 50,
	)]
	protected string $warehouse_rack = '';
	
	#[DataModel_Definition(
		type: DataModel::TYPE_STRING,
		max_len: 50,
	)]
	protected string $warehouse_position = '';
	
	
	#[DataModel_Definition(
		type: DataModel::TYPE_STRING,
		max_len: 65536
	)]
	protected string $note = '';
	
	public function getArrayKeyValue() : string
	{
		return $this->id;
	}
	
	public function getProductId(): int
	{
		return $this->product_id;
	}
	
	public function setProductId( int $product_id ): void
	{
		$this->product_id = $product_id;
	}
	
	public function getTitle(): string
	{
		return $this->title;
	}
	
	public function setTitle( string $title ): void
	{
		$this->title = $title;
	}
	
	public function getNumberOfUnits(): float
	{
		return $this->number_of_units;
	}
	
	public function setNumberOfUnits( float $number_of_units, ?MeasureUnit $measure_unit ): void
	{
		if( $measure_unit ) {
			$number_of_units = $measure_unit->round( $number_of_units );
			$this->measure_unit = $measure_unit->getCode();
		}
		
		$this->number_of_units = $number_of_units;
	}
	
	public function getMeasureUnit(): ?MeasureUnit
	{
		return MeasureUnits::get( $this->measure_unit );
	}
	
	
	
	public function getNote(): string
	{
		return $this->note;
	}
	
	public function setNote( string $note ): void
	{
		$this->note = $note;
	}
	
	public function getInternalCode(): string
	{
		return $this->internal_code;
	}
	
	public function setInternalCode( string $internal_code ): void
	{
		$this->internal_code = $internal_code;
	}
	
	public function getEAN(): string
	{
		return $this->EAN;
	}
	
	public function setEAN( string $EAN ): void
	{
		$this->EAN = $EAN;
	}
	
	public function getWarehouseSector(): string
	{
		return $this->warehouse_sector;
	}
	
	public function setWarehouseSector( string $warehouse_sector ): void
	{
		$this->warehouse_sector = $warehouse_sector;
	}
	
	public function getWarehouseRack(): string
	{
		return $this->warehouse_rack;
	}
	
	public function setWarehouseRack( string $warehouse_rack ): void
	{
		$this->warehouse_rack = $warehouse_rack;
	}
	
	public function getWarehousePosition(): string
	{
		return $this->warehouse_position;
	}
	
	public function setWarehousePosition( string $warehouse_position ): void
	{
		$this->warehouse_position = $warehouse_position;
	}
	
	
}