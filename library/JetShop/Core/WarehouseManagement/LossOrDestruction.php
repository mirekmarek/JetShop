<?php
namespace JetShop;

use Jet\Data_DateTime;
use Jet\DataModel;
use Jet\DataModel_Definition;
use Jet\Form;
use Jet\Form_Definition;
use Jet\Form_Field;
use Jet\Logger;
use JetApplication\Admin_Entity_Simple_Interface;
use JetApplication\Admin_Entity_Simple_Trait;
use JetApplication\Admin_Managers_WarehouseManagementLossOrDestruction;
use JetApplication\Currencies;
use JetApplication\Currency;
use JetApplication\Entity_Simple;
use JetApplication\JetShopEntity_Definition;
use JetApplication\MeasureUnit;
use JetApplication\MeasureUnits;
use JetApplication\NumberSeries_Entity_Interface;
use JetApplication\NumberSeries_Entity_Trait;
use JetApplication\Product;
use JetApplication\EShop;
use JetApplication\WarehouseManagement;
use JetApplication\Context_ProvidesContext_Interface;
use JetApplication\Context_ProvidesContext_Trait;
use JetApplication\WarehouseManagement_LossOrDestruction;
use Jet\Tr;
use JetApplication\WarehouseManagement_Warehouse;

#[DataModel_Definition(
	name: 'whm_loss_or_destruction',
	database_table_name: 'whm_loss_or_destruction',
)]
#[JetShopEntity_Definition(
	admin_manager_interface: Admin_Managers_WarehouseManagementLossOrDestruction::class
)]
class Core_WarehouseManagement_LossOrDestruction extends Entity_Simple implements
	NumberSeries_Entity_Interface,
	Context_ProvidesContext_Interface,
	Admin_Entity_Simple_Interface
{
	use Context_ProvidesContext_Trait;
	use NumberSeries_Entity_Trait;
	use Admin_Entity_Simple_Trait;
	
	public const STATUS_PENDING = 'pending';
	public const STATUS_DONE = 'done';
	public const STATUS_CANCELLED = 'cancelled';
	
	
	#[DataModel_Definition(
		type: DataModel::TYPE_INT,
		is_key: true,
	)]
	protected int $warehouse_id = 0;
	
	protected ?WarehouseManagement_Warehouse $warehouse = null;
	
	#[DataModel_Definition(
		type: DataModel::TYPE_STRING,
		is_key: true,
		max_len: 50,
	)]
	protected string $status = self::STATUS_PENDING;
	
	
	
	#[DataModel_Definition(
		type: DataModel::TYPE_DATE_TIME
	)]
	#[Form_Definition(
		type: Form_Field::TYPE_DATE,
		label: 'Date:'
	)]
	protected ?Data_DateTime $date = null;
	
	#[DataModel_Definition(
		type: DataModel::TYPE_STRING,
		max_len: 65536
	)]
	#[Form_Definition(
		type: Form_Field::TYPE_TEXTAREA,
		label: 'Notes:',
		is_required: false,
		error_messages: [
		]
	)]
	protected string $notes = '';
	
	#[DataModel_Definition(
		type: DataModel::TYPE_INT,
		is_key: true,
	)]
	#[Form_Definition(
		type: Form_Field::TYPE_HIDDEN,
		label: ''
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
	#[Form_Definition(
		type: Form_Field::TYPE_FLOAT,
		label: 'Number of units:',
	)]
	protected float $number_of_units = 1;
	
	#[DataModel_Definition(
		type: DataModel::TYPE_STRING,
		is_key: true,
		max_len: 50,
	)]
	protected string $currency_code = '';
	
	#[DataModel_Definition(
		type: DataModel::TYPE_FLOAT,
	)]
	protected float $price_per_unit = 0.0;
	
	#[DataModel_Definition(
		type: DataModel::TYPE_FLOAT,
	)]
	protected float $total = 0.0;
	
	
	public static function getNumberSeriesEntityIsPerShop() : bool
	{
		return false;
	}
	
	public static function getNumberSeriesEntityTitle() : string
	{
		return 'Warehouse management - loss or destruction';
	}
	
	
	public static function getStatusScope(): array
	{
		return [
			static::STATUS_PENDING   => Tr::_( 'Pending' ),
			static::STATUS_DONE      => Tr::_( 'Done' ),
			static::STATUS_CANCELLED => Tr::_( 'Cancelled' )
		];
	}
	
	
	public function getStatus(): string
	{
		return $this->status;
	}
	
	public function setStatus( string $status ): void
	{
		$this->status = $status;
	}
	
	public function getProductId(): int
	{
		return $this->product_id;
	}
	
	public function setProduct( int $product_id ) : void
	{
		$product = Product::get( $product_id );
		if(!$product) {
			return;
		}
		
		$card = $this->getWarehouse()->getCard( $product_id );
		
		$this->product_id = $product->getId();
		$this->product_ean = $product->getEan();
		$this->product_name = $product->getInternalName();
		$this->product_internal_code = $product->getInternalCode();
		$this->product_supplier_code = $product->getSupplierCode();
		$this->setMeasureUnit( $product->getKind()?->getMeasureUnit() );
		
		$this->currency_code = $card->getCurrency()->getCode();
		$this->price_per_unit = $card->getPricePerUnit();
	}
	
	public function getProductInternalCode(): string
	{
		return $this->product_internal_code;
	}
	
	public function getProductSupplierCode(): string
	{
		return $this->product_supplier_code;
	}
	
	public function getProductEan(): string
	{
		return $this->product_ean;
	}
	
	public function getProductName(): string
	{
		return $this->product_name;
	}
	
	public function getMeasureUnit(): ?MeasureUnit
	{
		return MeasureUnits::get( $this->measure_unit );
	}
	
	public function setMeasureUnit( ?MeasureUnit $measure_unit ): void
	{
		$this->measure_unit = $measure_unit ? $measure_unit->getCode() : '';
	}
	
	public function getNumberOfUnits(): float
	{
		return $this->number_of_units;
	}
	
	public function getCurrencyCode(): string
	{
		return $this->currency_code;
	}
	
	public function getCurrency() : Currency
	{
		return Currencies::get( $this->currency_code );
	}
	
	public function getPricePerUnit(): float
	{
		return $this->price_per_unit;
	}
	
	public function setNumberOfUnits( float $number_of_units ): void
	{
		if($this->getMeasureUnit()) {
			$number_of_units = $this->getMeasureUnit()->round( $number_of_units );
		}
		
		$this->number_of_units = $number_of_units;
	}
	
	public function getTotal(): float
	{
		return $this->total;
	}
	
	public function setTotal( float $total ): void
	{
		$this->total = $total;
	}
	
	
	public function beforeSave() : void
	{
		$this->total = $this->price_per_unit * $this->number_of_units;
	}
	
	
	public function getWarehouseId(): int
	{
		return $this->warehouse_id;
	}
	
	public function setWarehouseId( int $warehouse_id ): void
	{
		$this->warehouse_id = $warehouse_id;
		$this->warehouse = null;
	}
	
	public function getWarehouse() : WarehouseManagement_Warehouse
	{
		if(!$this->warehouse) {
			$this->warehouse = WarehouseManagement_Warehouse::get( $this->warehouse_id );
		}
		
		return $this->warehouse;
	}
	
	
	public function getNumberSeriesEntityData(): ?Data_DateTime
	{
		return $this->created;
	}
	
	public function getNumberSeriesEntityShop(): ?EShop
	{
		return null;
	}
	
	
	public function getDate(): ?Data_DateTime
	{
		return $this->date;
	}
	
	public function setDate( Data_DateTime|string|null $date ): void
	{
		$this->date = Data_DateTime::catchDateTime( $date );
	}
	
	public function getNotes(): string
	{
		return $this->notes;
	}
	
	public function setNotes( string $notes ): void
	{
		$this->notes = $notes;
	}
	
	public function afterAdd() : void
	{
		parent::afterAdd();
		$this->generateNumber();
	}
	
	public function afterUpdate(): void
	{
		parent::afterUpdate();
	}
	
	public function afterDelete(): void
	{
		parent::afterDelete();
	}
	
	
	
	
	
	public function done() : bool
	{
		if( $this->status != static::STATUS_PENDING ) {
			return false;
		}
		
		
		
		/**
		 * @var WarehouseManagement_LossOrDestruction $this
		 */
		WarehouseManagement::manageLossOrDestruction( $this );
		
		$this->status = static::STATUS_DONE;
		$this->save();
		
		Logger::success(
			event: 'whm_loss_or_destruction_done',
			event_message: 'WHM - Loss or destruction '.$this->getNumber().' hus been completed',
			context_object_id: $this->getId(),
			context_object_name: $this->getNumber(),
			context_object_data: $this
		);
		
		return true;
	}
	
	public function cancel() : bool
	{
		if( $this->status != static::STATUS_PENDING ) {
			return false;
		}
		
		$this->status = static::STATUS_CANCELLED;
		$this->save();
		
		Logger::success(
			event: 'whm_loss_or_destruction_cancelled',
			event_message: 'WHM - Loss or destruction '.$this->getNumber().' hus been cancelled',
			context_object_id: $this->getId(),
			context_object_name: $this->getNumber(),
			context_object_data: $this
		);
		
		return true;
	}
	
	
	public function getAdminTitle() : string
	{
		return $this->number;
	}
	
	
	protected function setupForm( Form $form ) : void
	{
		$form->field('product_id')->setFieldValueCatcher( function( string $id ) {
			$this->setProduct( (int)$id );
		} );
	}
	
	protected function catchForm( Form $form ) : bool
	{
		if(!$form->catch()) {
			
			return false;
		}
		
		return true;
	}
	
	
	
	public function setupAddForm( Form $form ) : void
	{
		$this->setupForm( $form );
	}
	
	
	
	public function catchAddForm() : bool
	{
		return $this->catchForm( $this->getAddForm() );
	}
	
	public function setupEditForm( Form $form ) : void
	{
		$this->setupForm( $form );
		
		if($this->getStatus()!=static::STATUS_PENDING) {
			$form->setIsReadonly();
		}
	}
	
	public function catchEditForm() : bool
	{
		return $this->catchForm( $this->getEditForm() );
	}
	
}