<?php
namespace JetShop;

use Jet\Data_DateTime;
use Jet\DataModel;
use Jet\DataModel_Definition;
use Jet\Form_Definition;
use Jet\Form_Field;
use Jet\Logger;
use JetApplication\Entity_Simple;
use JetApplication\NumberSeries_Entity_Interface;
use JetApplication\NumberSeries_Entity_Trait;
use JetApplication\Product;
use JetApplication\Shops_Shop;
use JetApplication\WarehouseManagement;
use JetApplication\WarehouseManagement_StockCard;
use JetApplication\WarehouseManagement_StockVerification_Item;
use JetApplication\WarehouseManagement_StockVerification;
use JetApplication\Context_ProvidesContext_Interface;
use JetApplication\Context_ProvidesContext_Trait;
use Jet\Tr;
use JetApplication\WarehouseManagement_Warehouse;

#[DataModel_Definition(
	name: 'whm_stock_verification',
	database_table_name: 'whm_stock_verification',
)]
class Core_WarehouseManagement_StockVerification extends Entity_Simple implements NumberSeries_Entity_Interface, Context_ProvidesContext_Interface
{
	use Context_ProvidesContext_Trait;
	use NumberSeries_Entity_Trait;
	
	public const STATUS_PENDING = 'pending';
	public const STATUS_DONE = 'done';
	public const STATUS_CANCELLED = 'cancelled';
	
	
	#[DataModel_Definition(
		type: DataModel::TYPE_INT,
		is_key: true,
	)]
	protected int $warehouse_id = 0;
	
	protected WarehouseManagement_Warehouse|null $warehouse = null;
	
	#[DataModel_Definition(
		type: DataModel::TYPE_STRING,
		is_key: true,
		max_len: 50,
	)]
	protected string $status = self::STATUS_PENDING;
	
	
	#[DataModel_Definition(
		type: DataModel::TYPE_INT
	)]
	protected int $criteria_supplier_id = 0;
	
	#[DataModel_Definition(
		type: DataModel::TYPE_INT
	)]
	protected int $criteria_kind_of_product_id = 0;
	
	#[DataModel_Definition(
		type: DataModel::TYPE_STRING,
		max_len: 50,
	)]
	protected string $criteria_sector = '';
	
	#[DataModel_Definition(
		type: DataModel::TYPE_STRING,
		max_len: 50,
	)]
	protected string $criteria_rack = '';
	
	#[DataModel_Definition(
		type: DataModel::TYPE_STRING,
		max_len: 50,
	)]
	protected string $criteria_position = '';

	
	
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
	
	/**
	 * @var WarehouseManagement_StockVerification_Item[]
	 */
	#[DataModel_Definition(
		type: DataModel::TYPE_DATA_MODEL,
		data_model_class: WarehouseManagement_StockVerification_Item::class
	)]
	protected array $items = [];
	
	
	public static function getStatusScope(): array
	{
		return [
			WarehouseManagement_StockVerification::STATUS_PENDING   => Tr::_( 'Pending' ),
			WarehouseManagement_StockVerification::STATUS_DONE      => Tr::_( 'Done' ),
			WarehouseManagement_StockVerification::STATUS_CANCELLED => Tr::_( 'Cancelled' )
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
	
	
	
	
	public function getWarehouseId(): int
	{
		return $this->warehouse_id;
	}
	
	public function setWarehouseId( int $warehouse_id ): void
	{
		$this->warehouse_id = $warehouse_id;
		$this->warehouse = null;
	}
	
	public function getWarehouse() : ?WarehouseManagement_Warehouse
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
	
	public function getNumberSeriesEntityShop(): ?Shops_Shop
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
	
	
	/**
	 * @return WarehouseManagement_StockVerification_Item[]
	 */
	public function getItems(): array
	{
		return $this->items;
	}
	
	public function addItem( WarehouseManagement_StockVerification_Item $item ): void
	{
		$this->items[] = $item;
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
	
	public function getCriteriaSupplierId(): int
	{
		return $this->criteria_supplier_id;
	}
	
	public function setCriteriaSupplierId( int $criteria_supplier_id ): void
	{
		$this->criteria_supplier_id = $criteria_supplier_id;
	}
	
	public function getCriteriaKindOfProductId(): int
	{
		return $this->criteria_kind_of_product_id;
	}
	
	public function setCriteriaKindOfProductId( int $criteria_kind_of_product_id ): void
	{
		$this->criteria_kind_of_product_id = $criteria_kind_of_product_id;
	}
	
	public function getCriteriaSector(): string
	{
		return $this->criteria_sector;
	}
	
	public function setCriteriaSector( string $criteria_sector ): void
	{
		$this->criteria_sector = $criteria_sector;
	}
	
	public function getCriteriaRack(): string
	{
		return $this->criteria_rack;
	}
	
	public function setCriteriaRack( string $criteria_rack ): void
	{
		$this->criteria_rack = $criteria_rack;
	}
	
	public function getCriteriaPosition(): string
	{
		return $this->criteria_position;
	}
	
	public function setCriteriaPosition( string $criteria_position ): void
	{
		$this->criteria_position = $criteria_position;
	}
	
	
	
	public function prepareNew( int $warehouse_id ) : void
	{
		$this->setWarehouseId( $warehouse_id );
		$this->setDate( Data_DateTime::now() );
		
		$product_ids = WarehouseManagement_StockCard::dataFetchCol(
			select: ['product_id'],
			where: [
				'warehouse_id' => $warehouse_id,
				'AND',
				'cancelled' => false,
				'AND',
				'in_stock >' => 0.0
			],
			raw_mode: true
		);
		
		if(
			$product_ids &&
			$this->criteria_supplier_id
		) {
			$product_ids = Product::dataFetchCol(['id'], where: [
				'id' => $product_ids,
				'AND',
				'supplier_id' => $this->criteria_supplier_id,
			]);
		}
		
		if(
			$product_ids &&
			$this->criteria_kind_of_product_id
		) {
			$product_ids = Product::dataFetchCol(['id'], where: [
				'id' => $product_ids,
				'AND',
				'kind_id' => $this->criteria_kind_of_product_id,
			]);
		}
		
		if(
			$product_ids &&
			$this->criteria_sector
		) {
			$product_ids = WarehouseManagement_StockCard::dataFetchCol(['product_id'], where: [
				'product_id' => $product_ids,
				'AND',
				'warehouse_id' => $this->warehouse_id,
				'AND',
				'sector' => $this->criteria_sector,
			]);
		}
		
		if(
			$product_ids &&
			$this->criteria_rack
		) {
			$product_ids = WarehouseManagement_StockCard::dataFetchCol(['product_id'], where: [
				'product_id' => $product_ids,
				'AND',
				'warehouse_id' => $this->warehouse_id,
				'AND',
				'rack' => $this->criteria_rack,
			]);
		}
		
		if(
			$product_ids &&
			$this->criteria_position
		) {
			$product_ids = WarehouseManagement_StockCard::dataFetchCol(['product_id'], where: [
				'product_id' => $product_ids,
				'AND',
				'warehouse_id' => $this->warehouse_id,
				'AND',
				'position' => $this->criteria_position,
			]);
		}
		
		if($product_ids) {
			$products = Product::fetchInstances(['id'=>$product_ids]);
			
			foreach($products as $p) {
				if( !$p->isPhysicalProduct() ) {
					continue;
				}
				
				$item = new WarehouseManagement_StockVerification_Item();
				
				/**
				 * @var WarehouseManagement_StockVerification $this
				 */
				$item->setupProduct( $this, $p );
				
				$this->items[$item->getProductId()] = $item;
			}
		}
		
		
	}
	
	
	public function done() : bool
	{
		if( $this->status != static::STATUS_PENDING ) {
			return false;
		}
		
		
		
		/**
		 * @var WarehouseManagement_StockVerification $this
		 */
		WarehouseManagement::manageStockVerification( $this );
		
		$this->status = static::STATUS_DONE;
		$this->save();
		
		Logger::success(
			event: 'whm_verification_done',
			event_message: 'WHM - Stock Verification '.$this->getNumber().' hus been completed',
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
			event: 'whm_verification_cancelled',
			event_message: 'WHM - Stock Verification '.$this->getNumber().' hus been cancelled',
			context_object_id: $this->getId(),
			context_object_name: $this->getNumber(),
			context_object_data: $this
		);
		
		return true;
	}
	
}