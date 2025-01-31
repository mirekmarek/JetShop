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

use JetApplication\Currencies;
use JetApplication\Currency;
use JetApplication\MeasureUnit;
use JetApplication\MeasureUnits;
use JetApplication\Product;
use JetApplication\WarehouseManagement_StockCard;
use JetApplication\WarehouseManagement_TransferBetweenWarehouses;

#[DataModel_Definition(
	name: 'whm_transfer_between_warehouses_items',
	database_table_name: 'whm_transfer_between_warehouses_items',
	id_controller_class: DataModel_IDController_AutoIncrement::class,
	id_controller_options: ['id_property_name'=>'id'],
	parent_model_class: WarehouseManagement_TransferBetweenWarehouses::class
)]
abstract class Core_WarehouseManagement_TransferBetweenWarehouses_Item extends DataModel_Related_1toN {
	
	#[DataModel_Definition(
		type: DataModel::TYPE_INT,
		is_id: true,
		related_to: 'main.id',
	)]
	protected int $transfer_id = 0;
	
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
		type: DataModel::TYPE_FLOAT
	)]
	protected float $number_of_units = 0.0;
	
	#[DataModel_Definition(
		type: DataModel::TYPE_STRING,
		max_len: 64,
	)]
	protected string $measure_unit = '';
	
	
	#[DataModel_Definition(
		type: DataModel::TYPE_STRING,
		max_len: 50,
	)]
	protected string $source_sector = '';
	
	#[DataModel_Definition(
		type: DataModel::TYPE_STRING,
		max_len: 50,
	)]
	protected string $source_rack = '';
	
	#[DataModel_Definition(
		type: DataModel::TYPE_STRING,
		max_len: 50,
	)]
	protected string $source_position = '';
	
	
	#[DataModel_Definition(
		type: DataModel::TYPE_STRING,
		max_len: 50,
	)]
	protected string $target_sector = '';
	
	#[DataModel_Definition(
		type: DataModel::TYPE_STRING,
		max_len: 50,
	)]
	protected string $target_rack = '';
	
	#[DataModel_Definition(
		type: DataModel::TYPE_STRING,
		max_len: 50,
	)]
	protected string $target_position = '';
	
	
	
	
	#[DataModel_Definition(
		type: DataModel::TYPE_STRING,
		max_len: 50,
	)]
	protected string $currency_code = '';
	
	#[DataModel_Definition(
		type: DataModel::TYPE_FLOAT,
	)]
	protected float $price_per_unit = 0;
	
	#[DataModel_Definition(
		type: DataModel::TYPE_FLOAT,
	)]
	protected float $recalculated_price_per_unit = 0.0;
	
	
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
	
	
	
	
	public function getNumberOfUnits(): float
	{
		return $this->number_of_units;
	}
	
	public function setNumberOfUnits( float $number_of_units ): void
	{
		if( $this->getMeasureUnit() ) {
			$number_of_units = $this->getMeasureUnit()->round( $number_of_units );
		}
		$this->number_of_units = $number_of_units;
	}
	
	
	public function getMeasureUnit(): ?MeasureUnit
	{
		return MeasureUnits::get( $this->measure_unit );
	}
	
	public function setMeasureUnit( ?MeasureUnit $measure_unit ): void
	{
		$this->measure_unit = $measure_unit ? $measure_unit->getCode() : '';
	}
	
	public function setProductInternalCode( string $product_internal_code ): void
	{
		$this->product_internal_code = $product_internal_code;
	}
	
	public function getProductInternalCode(): string
	{
		return $this->product_internal_code;
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
	
	public function getSourceSector(): string
	{
		return $this->source_sector;
	}
	
	public function setSourceSector( string $source_sector ): void
	{
		$this->source_sector = $source_sector;
	}
	
	public function getSourceRack(): string
	{
		return $this->source_rack;
	}
	
	public function setSourceRack( string $source_rack ): void
	{
		$this->source_rack = $source_rack;
	}
	
	public function getSourcePosition(): string
	{
		return $this->source_position;
	}
	
	public function setSourcePosition( string $source_position ): void
	{
		$this->source_position = $source_position;
	}
	
	public function getTargetSector(): string
	{
		return $this->target_sector;
	}
	
	public function setTargetSector( string $target_sector ): void
	{
		$this->target_sector = $target_sector;
	}
	
	public function getTargetRack(): string
	{
		return $this->target_rack;
	}
	
	public function setTargetRack( string $target_rack ): void
	{
		$this->target_rack = $target_rack;
	}
	
	public function getTargetPosition(): string
	{
		return $this->target_position;
	}
	
	public function setTargetPosition( string $target_position ): void
	{
		$this->target_position = $target_position;
	}
	
	public function getCurrency(): ?Currency
	{
		return Currencies::get( $this->currency_code );
	}
	
	public function setCurrency( Currency $currency ): void
	{
		$this->currency_code = $currency->getCode();
	}

	
	public function getPricePerUnit(): float
	{
		return $this->price_per_unit;
	}
	
	public function setPricePerUnit( float $price_per_unit ): void
	{
		$this->price_per_unit = $price_per_unit;
	}
	
	public function getRecalculatedPricePerUnit(): float
	{
		return $this->recalculated_price_per_unit;
	}
	
	public function setRecalculatedPricePerUnit( float $recalculated_price_per_unit ): void
	{
		$this->recalculated_price_per_unit = $recalculated_price_per_unit;
	}
	
	public function setupCard( WarehouseManagement_TransferBetweenWarehouses $transfer, WarehouseManagement_StockCard $card ) : void 
	{
		$product = Product::get( $card->getProductId() );
		if(!$product) {
			return;
		}
		
		$this->product_id = $card->getProductId();
		$this->product_internal_code = $product->getInternalCode();
		$this->product_supplier_code = $product->getSupplierCode();
		$this->product_ean = $product->getEan();
		$this->product_name = $product->getInternalName();
		$this->setMeasureUnit( $product->getKind()?->getMeasureUnit() );
		
		$this->source_sector = $card->getSector();
		$this->source_rack = $card->getRack();
		$this->source_position = $card->getPosition();
		
		$this->setCurrency( $card->getCurrency() );
		$this->setPricePerUnit( $card->getPricePerUnit() );
		
		$this->number_of_units = 0.0;
		
		$cart = $transfer->getTargetWarehouse()->getCard( $this->getProductId() );
		
		$this->setTargetSector( $card->getSector() );
		$this->setTargetRack( $card->getRack() );
		$this->setTargetPosition( $card->getPosition() );

	}
	
	
}