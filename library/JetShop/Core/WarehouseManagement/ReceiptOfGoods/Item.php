<?php
/**
 * @copyright Copyright (c) Miroslav Marek <mirek.marek@web-jet.cz>
 * @license EUPL 1.2  https://eupl.eu/1.2/en/
 * @author Miroslav Marek <mirek.marek@web-jet.cz>
 */
namespace JetShop;


use Jet\DataModel;
use Jet\DataModel_Definition;
use Jet\DataModel_Related_1toN;
use Jet\DataModel_IDController_AutoIncrement;

use JetApplication\MeasureUnit;
use JetApplication\MeasureUnits;
use JetApplication\Product;
use JetApplication\Supplier_GoodsOrder_Item;
use JetApplication\WarehouseManagement_ReceiptOfGoods;

#[DataModel_Definition(
	name: 'whm_receipt_of_goods_items',
	database_table_name: 'whm_receipt_of_goods_items',
	id_controller_class: DataModel_IDController_AutoIncrement::class,
	id_controller_options: ['id_property_name'=>'id'],
	parent_model_class: WarehouseManagement_ReceiptOfGoods::class
)]
abstract class Core_WarehouseManagement_ReceiptOfGoods_Item extends DataModel_Related_1toN {
	
	#[DataModel_Definition(
		type: DataModel::TYPE_INT,
		is_id: true,
		related_to: 'main.id',
	)]
	protected int $receipt_of_goods_id = 0;
	
	#[DataModel_Definition(
		type: DataModel::TYPE_ID_AUTOINCREMENT,
		is_id: true,
	)]
	protected int $id = 0;
	
	
	
	
	#[DataModel_Definition(
		type: DataModel::TYPE_INT,
		is_key: true,
	)]
	protected int $product_id = 0;
	
	#[DataModel_Definition(
		type: DataModel::TYPE_STRING,
		max_len: 50,
		is_key: true,
	)]
	protected string $product_internal_code = '';
	
	#[DataModel_Definition(
		type: DataModel::TYPE_STRING,
		max_len: 50,
		is_key: true,
	)]
	protected string $product_supplier_code = '';
	
	#[DataModel_Definition(
		type: DataModel::TYPE_STRING,
		max_len: 50,
		is_key: true,
	)]
	protected string $product_ean = '';
	
	#[DataModel_Definition(
		type: DataModel::TYPE_STRING,
		max_len: 255
	)]
	protected string $product_name = '';
	
	#[DataModel_Definition(
		type: DataModel::TYPE_STRING,
		max_len: 64,
	)]
	protected string $measure_unit = '';
	
	
	#[DataModel_Definition(
		type: DataModel::TYPE_STRING,
		max_len: 50,
	)]
	protected string $sector = '';
	
	#[DataModel_Definition(
		type: DataModel::TYPE_STRING,
		max_len: 50,
	)]
	protected string $rack = '';
	
	#[DataModel_Definition(
		type: DataModel::TYPE_STRING,
		max_len: 50,
	)]
	protected string $position = '';
	
	
	
	#[DataModel_Definition(
		type: DataModel::TYPE_FLOAT
	)]
	protected float $units_received = 0.0;
	
	#[DataModel_Definition(
		type: DataModel::TYPE_FLOAT
	)]
	protected float $price_per_unit = 0.0;
	
	#[DataModel_Definition(
		type: DataModel::TYPE_FLOAT
	)]
	protected float $total_price = 0.0;
	
	
	public function getId(): int
	{
		return $this->id;
	}
	
	public function canBeDeleted() : bool
	{
		return true;
	}
	
	public function getArrayKeyValue(): string
	{
		return $this->product_id;
	}
	
	public function getProductId() : string
	{
		return $this->product_id;
	}
	
	public function setProductId( string $product_id ) : void
	{
		$this->product_id = $product_id;
	}
	
	public function getReceiptOfGoodsId(): int
	{
		return $this->receipt_of_goods_id;
	}
	
	public function setReceiptOfGoodsId( int $receipt_of_goods_id ): void
	{
		$this->receipt_of_goods_id = $receipt_of_goods_id;
	}
	
	public function getProductInternalCode(): string
	{
		return $this->product_internal_code;
	}
	
	public function setProductInternalCode( string $product_internal_code ): void
	{
		$this->product_internal_code = $product_internal_code;
	}
	
	public function getProductSupplierCode(): string
	{
		return $this->product_supplier_code;
	}
	
	public function setProductSupplierCode( string $product_supplier_code ): void
	{
		$this->product_supplier_code = $product_supplier_code;
	}
	
	public function getProductEan(): string
	{
		return $this->product_ean;
	}
	
	public function setProductEan( string $product_ean ): void
	{
		$this->product_ean = $product_ean;
	}
	
	public function getProductName(): string
	{
		return $this->product_name;
	}
	
	public function setProductName( string $product_name ): void
	{
		$this->product_name = $product_name;
	}
	
	public function getMeasureUnit(): ?MeasureUnit
	{
		return MeasureUnits::get( $this->measure_unit );
	}
	
	public function setMeasureUnit( ?MeasureUnit $measure_unit ): void
	{
		$this->measure_unit = $measure_unit ? $measure_unit->getCode() : '';
	}

	
	public function getUnitsReceived(): float
	{
		return $this->units_received;
	}
	
	public function setUnitsReceived( float $units_received ): void
	{
		$this->units_received = $units_received;
		$this->recalculate();
	}
	
	public function getPricePerUnit(): float
	{
		return $this->price_per_unit;
	}
	
	public function setPricePerUnit( float $price_per_unit ): void
	{
		$this->price_per_unit = $price_per_unit;
		$this->recalculate();
	}
	
	protected function recalculate() : void
	{
		$this->total_price = $this->price_per_unit*$this->units_received;
	}
	
	public function getTotalPrice(): float
	{
		return $this->total_price;
	}
	
	public function getSector(): string
	{
		return $this->sector;
	}
	
	public function setSector( string $sector ): void
	{
		$this->sector = $sector;
	}
	
	public function getRack(): string
	{
		return $this->rack;
	}
	
	public function setRack( string $rack ): void
	{
		$this->rack = $rack;
	}
	
	public function getPosition(): string
	{
		return $this->position;
	}
	
	public function setPosition( string $position ): void
	{
		$this->position = $position;
	}
	
	
	
	public function setupProduct( WarehouseManagement_ReceiptOfGoods $rog, Product $product, float $units_received ) : void
	{
		$this->product_id = $product->getId();
		$this->product_internal_code = $product->getInternalCode();
		$this->product_ean = $product->getEan();
		$this->product_supplier_code = $product->getSupplierCode();
		$this->product_name = $product->getInternalName();
		$this->units_received = $units_received;
		$this->setMeasureUnit( $product->getKind()?->getMeasureUnit() );
		
		$wh_card = $rog->getWarehouse()->getCard( $product->getId() );
		$this->sector = $wh_card->getSector();
		$this->rack = $wh_card->getRack();
		$this->position = $wh_card->getPosition();
		
	}
	
	public function setupOrderItem( WarehouseManagement_ReceiptOfGoods $rog, Supplier_GoodsOrder_Item $order_item ) : void
	{
		$this->product_id = $order_item->getProductId();
		$this->product_internal_code = $order_item->getProductInternalCode();
		$this->product_ean = $order_item->getProductEan();
		$this->product_supplier_code = $order_item->getProductSupplierCode();
		$this->product_name = $order_item->getProductName();
		$this->units_received = $order_item->getUnitsOrdered();
		$this->setMeasureUnit( $order_item->getMeasureUnit() );
		
		
		$wh_card = $rog->getWarehouse()->getCard( $order_item->getProductId() );
		$this->sector = $wh_card->getSector();
		$this->rack = $wh_card->getRack();
		$this->position = $wh_card->getPosition();
	}
	
}