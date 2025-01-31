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
use JetApplication\Supplier_GoodsOrder;
use JetApplication\Product;
use JetApplication\WarehouseManagement;


#[DataModel_Definition(
	name: 'supplier_goods_order_item',
	database_table_name: 'supplier_goods_orders_items',
	id_controller_class: DataModel_IDController_AutoIncrement::class,
	id_controller_options: ['id_property_name'=>'id'],
	parent_model_class: Supplier_GoodsOrder::class
)]
abstract class Core_Supplier_GoodsOrder_Item extends DataModel_Related_1toN
{
	
	#[DataModel_Definition(
		type: DataModel::TYPE_INT,
		is_id: true,
		related_to: 'main.id',
	)]
	protected int $order_id = 0;
	
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
		type: DataModel::TYPE_FLOAT
	)]
	protected float $units_ordered = 0;
	
	#[DataModel_Definition(
		type: DataModel::TYPE_FLOAT
	)]
	protected float $units_received = 0;
	
	
	
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
	
	
	public function getUnitsOrdered() : float
	{
		return $this->units_ordered;
	}
	
	public function setUnitsOrdered( float $units_ordered ) : void
	{
		$this->units_ordered = $units_ordered;
	}
	
	public function getUnitsReceived(): float
	{
		return $this->units_received;
	}
	
	public function setUnitsReceived( float $units_received ): void
	{
		$this->units_received = $units_received;
	}
	
	public function getMeasureUnit(): ?MeasureUnit
	{
		return MeasureUnits::get( $this->measure_unit );
	}
	
	public function setMeasureUnit( ?MeasureUnit $measure_unit ): void
	{
		$this->measure_unit = $measure_unit ? $measure_unit->getCode() : '';
	}
	
	
	
	public function getOrderId(): int
	{
		return $this->order_id;
	}
	
	public function setOrderId( int $order_id ): void
	{
		$this->order_id = $order_id;
	}
	
	public function getProductId(): int
	{
		return $this->product_id;
	}
	
	public function setProductId( int $product_id ): void
	{
		$this->product_id = $product_id;
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
	
	
	
	
	public function setupProduct( Supplier_GoodsOrder $order, Product $product, ?float $units_to_order=null ) : void
	{
		if( $units_to_order==null ) {
			$units_to_order =
				WarehouseManagement::howManyItemsMustBeOrdered( $product )
				+
				WarehouseManagement::howManyItemsShouldBeOrdered( $product );
		}
		
		$this->product_id = $product->getId();
		$this->product_internal_code = $product->getInternalCode();
		$this->product_ean = $product->getEan();
		$this->product_supplier_code = $product->getSupplierCode();
		$this->product_name = $product->getInternalName();
		$this->units_ordered = $units_to_order;
		$this->setMeasureUnit( $product->getKind()?->getMeasureUnit() );
	}
	
}