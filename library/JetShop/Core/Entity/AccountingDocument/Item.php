<?php
namespace JetShop;

use Jet\DataModel;
use Jet\DataModel_Definition;
use Jet\DataModel_Related_1toN;
use Jet\DataModel_IDController_AutoIncrement;

use Jet\Tr;
use Jet\UI;
use JetApplication\Delivery_Method_EShopData;
use JetApplication\Discounts_Discount;
use JetApplication\Entity_Price;
use JetApplication\MeasureUnit;
use JetApplication\MeasureUnits;
use JetApplication\Payment_Method_EShopData;
use JetApplication\Pricelist;
use JetApplication\Product_EShopData;
use JetApplication\Entity_AccountingDocument_Item_SetItem;
use JetApplication\Entity_AccountingDocument_Item;

#[DataModel_Definition(
	id_controller_class: DataModel_IDController_AutoIncrement::class,
	id_controller_options: ['id_property_name'=>'id'],
)]
abstract class Core_Entity_AccountingDocument_Item extends DataModel_Related_1toN {
	
	public const ITEM_TYPE_PRODUCT = 'product';
	public const ITEM_TYPE_VIRTUAL_PRODUCT = 'virtual_product';
	
	public const ITEM_TYPE_GIFT = 'gift';
	public const ITEM_TYPE_VIRTUAL_GIFT = 'virtual_gift';
	
	public const ITEM_TYPE_SERVICE = 'service';
	
	public const ITEM_TYPE_PAYMENT = 'payment';
	public const ITEM_TYPE_DELIVERY = 'delivery';
	public const ITEM_TYPE_DISCOUNT = 'discount';
	
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
	
	
	#[DataModel_Definition(
		type: DataModel::TYPE_FLOAT
	)]
	protected float $set_discount_per_unit = 0.0;
	
	/**
	 * @var Entity_AccountingDocument_Item_SetItem[]
	 */
	#[DataModel_Definition(
		type: DataModel::TYPE_DATA_MODEL,
		data_model_class: Entity_AccountingDocument_Item_SetItem::class
	)]
	protected array $set_items = [];
	
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
		return $this->id;
	}
	
	
	public function getType() : string
	{
		return $this->type;
	}
	
	public function isPhysicalProduct() : bool
	{
		return (
			$this->getType()==Entity_AccountingDocument_Item::ITEM_TYPE_PRODUCT ||
			$this->getType()==Entity_AccountingDocument_Item::ITEM_TYPE_GIFT
		);
	}
	
	public function isVirtualProduct() : bool
	{
		return (
			$this->getType()==Entity_AccountingDocument_Item::ITEM_TYPE_VIRTUAL_PRODUCT ||
			$this->getType()==Entity_AccountingDocument_Item::ITEM_TYPE_VIRTUAL_GIFT
		);
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
	
	public function setNumberOfUnits( float $number_of_units, ?MeasureUnit $measure_unit ) : void
	{
		if($measure_unit) {
			$this->measure_unit = $measure_unit->getCode();
			
			$number_of_units = $measure_unit->round( $number_of_units );
		}
		
		$this->number_of_units = $number_of_units;
		$this->recalculate();
	}
	
	
	public function getMeasureUnit(): ?MeasureUnit
	{
		return MeasureUnits::get( $this->measure_unit );
	}
	
	public function setMeasureUnit( ?MeasureUnit $measure_unit ): void
	{
		$this->measure_unit = $measure_unit ? $measure_unit->getCode() : '';
	}
	
	
	public function getVatRate() : float
	{
		return $this->vat_rate;
	}
	
	public function getPricePerUnit() : float
	{
		return $this->price_per_unit;
	}
	
	public function getPricePerUnit_WithVat(): float
	{
		return $this->price_per_unit_with_vat;
	}
	
	public function getPricePerUnit_WithoutVat(): float
	{
		return $this->price_per_unit_without_vat;
	}
	
	public function getPricePerUnit_Vat(): float
	{
		return $this->price_per_unit_vat;
	}
	
	
	
	public function getTotalAmount() : float
	{
		return $this->total_amount;
	}
	
	public function getTotalAmount_WithVat(): float
	{
		return $this->total_amount_with_vat;
	}
	
	public function getTotalAmount_WithoutVat(): float
	{
		return $this->total_amount_without_vat;
	}
	
	public function getTotalAmount_Vat(): float
	{
		return $this->total_amount_vat;
	}
	
	
	
	public function getSetDiscountPerUnit(): float
	{
		return $this->set_discount_per_unit;
	}
	
	public function setSetDiscountPerUnit( float $set_discount_per_unit ): void
	{
		$this->set_discount_per_unit = $set_discount_per_unit;
		$this->recalculate();
	}
	
	protected static function getSetItemClassName() : string
	{
		return static::getDataModelDefinition(static::class)->getProperty('set_items')->getValueDataModelClass();
	}
	
	
	public function setupProduct( Pricelist $pricelist, Product_EShopData $product, float $number_of_units ) : void
	{
		$kind = $product->getKind();
		
		if($product->isVirtual()) {
			$this->setType( Entity_AccountingDocument_Item::ITEM_TYPE_VIRTUAL_PRODUCT );
		} else {
			$this->setType( Entity_AccountingDocument_Item::ITEM_TYPE_PRODUCT );
		}
		
		
		$this->setItemId( $product->getId() );
		$this->setTitle( $product->getFullName() );
		$this->setItemCode( $product->getInternalCode() );
		
		$this->setNumberOfUnits( $number_of_units, $product->getKind()?->getMeasureUnit() );
		$this->setupPricePerUnit( $product->getPriceEntity( $pricelist ) );
		
		
		if( $product->isSet() ) {
			$set_discount = $product->getSetDiscountAmount( $pricelist );
			$this->setSetDiscountPerUnit( $set_discount );
			
			$set_item_class_name = static::getSetItemClassName();
			
			foreach( $product->getSetItems() as $set_item ) {
				/**
				 * @var Entity_AccountingDocument_Item_SetItem $item_set_item
				 */
				$item_set_item = new $set_item_class_name();
				$item_set_item->setupBySetItem( $pricelist, $product, $set_item, $number_of_units );
				$this->set_items[] = $item_set_item;
			}
		}
	}
	
	public function setupGift( Pricelist $pricelist, Product_EShopData $product, float $number_of_units ) : void
	{
		$kind = $product->getKind();
		
		if($product->isVirtual()) {
			$this->setType( Entity_AccountingDocument_Item::ITEM_TYPE_VIRTUAL_GIFT );
		} else {
			$this->setType( Entity_AccountingDocument_Item::ITEM_TYPE_GIFT );
		}
		
		
		$this->setItemId( $product->getId() );
		$this->setTitle( $product->getFullName() );
		$this->setItemCode( $product->getInternalCode() );
		
		$this->setNumberOfUnits( $number_of_units, $product->getKind()?->getMeasureUnit() );
		
		
		if( $product->isSet() ) {
			$set_discount = $product->getSetDiscountAmount( $pricelist );
			$this->setSetDiscountPerUnit( $set_discount );
			
			$set_item_class_name = static::getSetItemClassName();
			
			foreach( $product->getSetItems() as $set_item ) {
				/**
				 * @var Entity_AccountingDocument_Item_SetItem $item_set_item
				 */
				$item_set_item = new $set_item_class_name();
				$item_set_item->setupBySetItem( $pricelist, $product, $set_item, $number_of_units );
				$this->set_items[] = $item_set_item;
			}
		}
	}
	
	public function setupDeliveryMethod( Pricelist $pricelist, Delivery_Method_EShopData $delivery_method ) : void
	{
		$this->setType( Entity_AccountingDocument_Item::ITEM_TYPE_DELIVERY );
		$this->setItemId( $delivery_method->getId() );
		$this->setItemCode( $delivery_method->getInternalCode() );
		
		$this->setTitle( $delivery_method->getTitle() );
		$this->setDescription( $delivery_method->getDescriptionShort() );
		
		$this->setNumberOfUnits( 1, null );
		$this->setupPricePerUnit( $delivery_method->getPriceEntity( $pricelist ) );
		
	}
	
	public function setupPaymentMethod( Pricelist $pricelist, Payment_Method_EShopData $payment_method ) : void
	{
		$this->setType( Entity_AccountingDocument_Item::ITEM_TYPE_PAYMENT );
		$this->setItemId( $payment_method->getId() );
		$this->setItemCode( $payment_method->getInternalCode() );
		
		$this->setTitle( $payment_method->getTitle() );
		$this->setDescription( $payment_method->getDescriptionShort() );
		
		$this->setNumberOfUnits( 1, null );
		$this->setupPricePerUnit( $payment_method->getPriceEntity( $pricelist ) );
		
	}
	
	
	public function setupDiscount( Pricelist $pricelist, Discounts_Discount $discount ) : void
	{
		$this->setType( Entity_AccountingDocument_Item::ITEM_TYPE_DISCOUNT );
		$this->setSubType( $discount->getDiscountType() );
		$this->setItemCode( $discount->getDiscountModule() );
		$this->setSubCode( $discount->getDiscountContext() );
		
		$this->setNumberOfUnits( 1, null );
		
		$this->setTitle( $discount->getDescription() );
		
		$discount_price = new class extends Entity_Price {};
		
		$discount_price->setPricelistCode( $pricelist->getCode() );
		$discount_price->setVatRate( $discount->getVatRate() );
		$discount_price->setPrice( $discount->getAmount() );
		
		$this->setupPricePerUnit( $discount_price );
		
	}
	
	
	/**
	 * @return Entity_AccountingDocument_Item_SetItem[]
	 */
	public function getSetItems(): array
	{
		return $this->set_items;
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
		'set_discount_per_unit',
		];
	}
	
	
	
	public function clone() : static
	{
		$new_item = new static();
		
		foreach(static::getCloneProperties() as $k) {
			$new_item->{$k} = $this->{$k};
		}
		
		foreach($this->set_items as $set_item) {
			$new_item->set_items[] = $set_item->clone();
		}
		
		
		return $new_item;
	}
	
	public static function createFrom( Entity_AccountingDocument_Item $source ) : static
	{
		$new_item = new static();
		
		foreach(static::getCloneProperties() as $k) {
			$new_item->{$k} = $source->{$k};
		}
		
		/**
		 * @var Entity_AccountingDocument_Item_SetItem $set_item_class_name
		 */
		$set_item_class_name = static::getSetItemClassName();
		
		foreach($source->set_items as $set_item) {
			$new_item->set_items[] = $set_item_class_name::createFrom( $set_item );
		}
		
		return $new_item;
	}
	
	public static function getItemIcons() : array
	{
		return [
			static::ITEM_TYPE_PRODUCT         => UI::icon( 'box' )->setTitle( Tr::_( 'Product' ) ),
			static::ITEM_TYPE_VIRTUAL_PRODUCT => UI::icon( 'at' )->setTitle( Tr::_( 'Virtual product' ) ),
			static::ITEM_TYPE_SERVICE         => UI::icon( 'handshake-angle' )->setTitle( Tr::_( 'Service' ) ),
			static::ITEM_TYPE_GIFT            => UI::icon( 'hand-holding-heart' )->setTitle( Tr::_( 'Gift' ) ),
			static::ITEM_TYPE_VIRTUAL_GIFT    => UI::icon( 'hand-holding-heart' )->setTitle( Tr::_( 'Virtual gift' ) ),
			static::ITEM_TYPE_PAYMENT         => UI::icon( 'money-bills' )->setTitle( Tr::_( 'Payment' ) ),
			static::ITEM_TYPE_DELIVERY        => UI::icon( 'truck-ramp-box' )->setTitle( Tr::_( 'Delivery' ) ),
			static::ITEM_TYPE_DISCOUNT        => UI::icon( 'arrow-down' )->setTitle( Tr::_( 'Discount' ) ),
		];
		
	}
	
}