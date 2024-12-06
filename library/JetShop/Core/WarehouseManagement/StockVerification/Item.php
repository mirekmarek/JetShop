<?php
namespace JetShop;

use Jet\DataModel;
use Jet\DataModel_Definition;
use Jet\DataModel_Related_1toN;
use Jet\DataModel_IDController_AutoIncrement;

use JetApplication\MeasureUnit;
use JetApplication\MeasureUnits;
use JetApplication\Product;
use JetApplication\WarehouseManagement_StockVerification;

#[DataModel_Definition(
	name: 'whm_stock_verification_item',
	database_table_name: 'whm_stock_verification_items',
	id_controller_class: DataModel_IDController_AutoIncrement::class,
	id_controller_options: ['id_property_name'=>'id'],
	parent_model_class: WarehouseManagement_StockVerification::class
)]
abstract class Core_WarehouseManagement_StockVerification_Item extends DataModel_Related_1toN {
	
	#[DataModel_Definition(
		type: DataModel::TYPE_INT,
		is_id: true,
		related_to: 'main.id',
	)]
	protected int $stock_verification_id = 0;
	
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
	protected float $number_of_units_expected = 0.0;
	
	#[DataModel_Definition(
		type: DataModel::TYPE_FLOAT
	)]
	protected float $number_of_units_reality = 0.0;
	
	
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
	
	public function getStockVerificationId(): int
	{
		return $this->stock_verification_id;
	}
	
	public function setStockVerificationId( int $receipt_of_goods_id ): void
	{
		$this->stock_verification_id = $receipt_of_goods_id;
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
	
	public function getNumberOfUnitsExpected(): float
	{
		return $this->number_of_units_expected;
	}
	
	public function setNumberOfUnitsExpected( float $number_of_units_expected ): void
	{
		$this->number_of_units_expected = $number_of_units_expected;
	}
	
	public function getNumberOfUnitsReality(): float
	{
		return $this->number_of_units_reality;
	}
	
	public function setNumberOfUnitsReality( float $number_of_units_reality ): void
	{
		$this->number_of_units_reality = $number_of_units_reality;
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
	
	
	
	public function setupProduct( WarehouseManagement_StockVerification $vrf, Product $product ) : void
	{
		$this->product_id = $product->getId();
		$this->product_internal_code = $product->getInternalCode();
		$this->product_ean = $product->getEan();
		$this->product_supplier_code = $product->getSupplierCode();
		$this->product_name = $product->getInternalName();
		$this->setMeasureUnit( $product->getKind()?->getMeasureUnit() );
		
		
		$wh_card = $vrf->getWarehouse()->getCard( $product->getId() );
		$this->sector = $wh_card->getSector();
		$this->rack = $wh_card->getRack();
		$this->position = $wh_card->getPosition();
		$this->number_of_units_expected = $wh_card->getInStock();
		
	}

	
}