<?php
namespace JetShop;

use Jet\DataModel;
use Jet\DataModel_Definition;
use Jet\DataModel_Related_1toN;
use Jet\DataModel_IDController_AutoIncrement;

use JetApplication\Entity_AccountingDocument_Item_SetItem;
use JetApplication\Entity_Price;
use JetApplication\MeasureUnit;
use JetApplication\Entity_AccountingDocument_Item;
use JetApplication\Pricelists_Pricelist;
use JetApplication\Product_SetItem;
use JetApplication\Product_ShopData;

#[DataModel_Definition(
	id_controller_class: DataModel_IDController_AutoIncrement::class,
	id_controller_options: ['id_property_name'=>'id'],
)]
abstract class Core_Entity_AccountingDocument_Item_SetItem extends DataModel_Related_1toN {
	
	
	#[DataModel_Definition(
		type: DataModel::TYPE_ID_AUTOINCREMENT,
		is_id: true,
	)]
	protected int $id = 0;
	
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
		type: DataModel::TYPE_STRING,
		max_len: 255
	)]
	protected string $title = '';
	
	#[DataModel_Definition(
		type: DataModel::TYPE_STRING,
		max_len: 65536
	)]
	protected string $description = '';
	
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
	
	
	
	public function getArrayKeyValue(): string
	{
		return $this->id;
	}
	
	
	public function getType() : string
	{
		return $this->type;
	}
	
	public function isDiscount() : bool
	{
		return $this->type == Entity_AccountingDocument_Item::ITEM_TYPE_DISCOUNT;
	}
	
	public function setType( string $type ) : void
	{
		$this->type = $type;
	}
	
	public function getSubType() : string
	{
		return $this->sub_type;
	}
	
	public function setSubType( string $sub_type ) : void
	{
		$this->sub_type = $sub_type;
	}
	
	public function getItemCode() : string
	{
		return $this->item_code;
	}
	
	public function setItemCode( string $item_code ) : void
	{
		$this->item_code = $item_code;
	}
	
	public function getSubCode() : string
	{
		return $this->sub_code;
	}
	
	public function setSubCode( string $sub_code ) : void
	{
		$this->sub_code = $sub_code;
	}
	
	public function getItemId() : string
	{
		return $this->item_id;
	}
	
	public function setItemId( string $item_id ) : void
	{
		$this->item_id = $item_id;
	}
	
	public function getTitle() : string
	{
		return $this->title;
	}
	
	public function setTitle( string $title ) : void
	{
		$this->title = $title;
	}
	
	public function getDescription() : string
	{
		return $this->description;
	}
	
	public function setDescription( string $description ) : void
	{
		$this->description = $description;
	}
	
	public function getNumberOfUnits() : float
	{
		return $this->number_of_units;
	}
	
	public function setNumberOfUnits( float $number_of_units, ?MeasureUnit $measure_unit ) : void
	{
		if($measure_unit) {
			$number_of_units = $measure_unit->round( $number_of_units );
			$this->measure_unit = $measure_unit->getCode();
		}
		
		$this->number_of_units = $number_of_units;
		
		$this->recalculate();
	}
	
	public function getMeasureUnit(): ?MeasureUnit
	{
		return MeasureUnit::get( $this->measure_unit );
	}
	
	protected function recalculate() : void
	{
		$this->total_amount             = $this->number_of_units * $this->price_per_unit;
		$this->total_amount_with_vat    = $this->number_of_units * $this->price_per_unit_with_vat;
		$this->total_amount_without_vat = $this->number_of_units * $this->price_per_unit_without_vat;
		$this->total_amount_vat         = $this->number_of_units * $this->price_per_unit_vat;
	}
	
	
	public function setupPricePerUnit( Entity_Price $price ) : void
	{
		$this->vat_rate = $price->getVatRate();
		$this->price_per_unit = $price->getPrice();
		$this->price_per_unit_with_vat = $price->getPrice_WithVAT();
		$this->price_per_unit_without_vat = $price->getPrice_WithoutVAT();
		$this->price_per_unit_vat = $price->getPrice_VAT();
		
		$this->recalculate();
	}
	
	
	public function getVatRate() : float
	{
		return $this->vat_rate;
	}
	
	public function getPricePerUnit() : float
	{
		return $this->price_per_unit;
	}
	
	public function getPricePerUnitWithVat(): float
	{
		return $this->price_per_unit_with_vat;
	}
	
	public function getPricePerUnitWithoutVat(): float
	{
		return $this->price_per_unit_without_vat;
	}
	
	public function getPricePerUnitVat(): float
	{
		return $this->price_per_unit_vat;
	}
	
	
	public function getTotalAmount() : float
	{
		return $this->total_amount;
	}
	
	public function getTotalAmountWithVat(): float
	{
		return $this->total_amount_with_vat;
	}
	
	public function getTotalAmountWithoutVat(): float
	{
		return $this->total_amount_without_vat;
	}
	
	public function getTotalAmountVat(): float
	{
		return $this->total_amount_vat;
	}
	
	
	
	public function setupBySetItem( Pricelists_Pricelist $pricelist, Product_ShopData $set, Product_SetItem $set_item, float $number_of_units ) : void
	{
		$product = Product_ShopData::get( $set_item->getItemProductId(), $set->getShop() );
		$kind = $product->getKind();
		
		if($product->isVirtual()) {
			$this->setType( Entity_AccountingDocument_Item::ITEM_TYPE_VIRTUAL_PRODUCT );
		} else {
			$this->setType( Entity_AccountingDocument_Item::ITEM_TYPE_PRODUCT );
		}
		
		$this->setItemId( $product->getId() );
		$this->setTitle( $product->getFullName() );
		$this->setItemCode( $product->getInternalCode() );
		
		$this->setNumberOfUnits( $number_of_units*$set_item->getCount(), $product->getKind()?->getMeasureUnit() );
		
		$this->setupPricePerUnit( $product->getPriceEntity( $pricelist ) );
	}
	
	protected static function getCloneProperties() : array
	{
		return [
			'type',
			'sub_type',
			'item_code',
			'sub_code',
			'item_id',
			'title',
			'description',
			'number_of_units',
			'measure_unit',
			'vat_rate',
			'price_per_unit',
			'price_per_unit_with_vat',
			'price_per_unit_without_vat',
			'price_per_unit_vat',
			'total_amount',
			'total_amount_with_vat',
			'total_amount_without_vat',
			'total_amount_vat',
		];
	}
	
	public function clone() : static
	{
		$new_item = new static();
		
		foreach(static::getCloneProperties() as $k) {
			$new_item->{$k} = $this->{$k};
		}
		
		return $new_item;
	}
	
	public static function createFrom( Entity_AccountingDocument_Item_SetItem $source ) : static
	{
		$new_item = new static();
		
		foreach(static::getCloneProperties() as $k) {
			$new_item->{$k} = $source->{$k};
		}
		
		return $new_item;
	}
	
}