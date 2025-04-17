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
use Jet\Form;
use Jet\Form_Definition;
use Jet\Form_Field;
use Jet\Form_Field_Float;
use Jet\Form_Field_Input;
use JetApplication\Admin_Managers_WarehouseManagement_StockVerification;
use JetApplication\Context_ProvidesContext_Interface;
use JetApplication\Context_ProvidesContext_Trait;
use JetApplication\Product;
use JetApplication\EShopEntity_Admin_Interface;
use JetApplication\EShopEntity_Admin_Trait;
use JetApplication\EShopEntity_Basic;
use JetApplication\EShopEntity_Event;
use JetApplication\EShopEntity_HasEvents_Interface;
use JetApplication\EShopEntity_HasEvents_Trait;
use JetApplication\EShopEntity_HasGet_Interface;
use JetApplication\EShopEntity_HasGet_Trait;
use JetApplication\EShopEntity_Definition;
use JetApplication\EShopEntity_HasNumberSeries_Interface;
use JetApplication\EShopEntity_HasNumberSeries_Trait;
use JetApplication\EShopEntity_HasStatus_Interface;
use JetApplication\EShopEntity_HasStatus_Trait;
use JetApplication\EShops;
use JetApplication\EShop;
use JetApplication\WarehouseManagement_StockCard;
use JetApplication\WarehouseManagement_StockVerification_Event;
use JetApplication\WarehouseManagement_StockVerification_Item;
use JetApplication\WarehouseManagement_StockVerification;
use JetApplication\WarehouseManagement_StockVerification_Status;
use JetApplication\WarehouseManagement_StockVerification_Status_Cancelled;
use JetApplication\WarehouseManagement_StockVerification_Status_Done;
use JetApplication\WarehouseManagement_StockVerification_Status_Pending;
use JetApplication\WarehouseManagement_Warehouse;

#[DataModel_Definition(
	name: 'whm_stock_verification',
	database_table_name: 'whm_stock_verification',
)]
#[EShopEntity_Definition(
	entity_name_readable: 'Warehouse management - stock verification',
	admin_manager_interface: Admin_Managers_WarehouseManagement_StockVerification::class
)]
abstract class Core_WarehouseManagement_StockVerification extends EShopEntity_Basic implements
	EShopEntity_HasNumberSeries_Interface,
	Context_ProvidesContext_Interface,
	EShopEntity_Admin_Interface,
	EShopEntity_HasGet_Interface,
	EShopEntity_HasEvents_Interface,
	EShopEntity_HasStatus_Interface
{
	use Context_ProvidesContext_Trait;
	use EShopEntity_HasNumberSeries_Trait;
	use EShopEntity_Admin_Trait;
	use EShopEntity_HasGet_Trait;
	use EShopEntity_HasStatus_Trait;
	use EShopEntity_HasEvents_Trait;
	
	public static array $flags = [];
	
	#[DataModel_Definition(
		type: DataModel::TYPE_INT,
		is_key: true,
	)]
	protected int $warehouse_id = 0;
	
	protected WarehouseManagement_Warehouse|null $warehouse = null;
	
	
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
	
	
	
	public static function getNumberSeriesEntityIsPerShop() : bool
	{
		return false;
	}
	
	public static function getNumberSeriesEntityTitle() : string
	{
		return 'Warehouse management - stock verification';
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
		$this->setStatus( WarehouseManagement_StockVerification_Status_Pending::get() );
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
		$this->setStatus( WarehouseManagement_StockVerification_Status_Done::get() );
		return true;
	}
	
	public function cancel() : bool
	{
		$this->setStatus( WarehouseManagement_StockVerification_Status_Cancelled::get() );
		return true;
	}
	
	public function getAdminTitle() : string
	{
		return $this->number;
	}
	
	
	protected function setupForm( Form $form ) : void
	{
		foreach($this->items as $p_id=>$item) {
			$qty_reality = new Form_Field_Float( '/item_'.$p_id.'/qty_reality', '' );
			$qty_reality->setDefaultValue( $item->getNumberOfUnitsReality() );
			$qty_reality->setFieldValueCatcher( function( float $v ) use ($item) : void {
				$item->setNumberOfUnitsReality( $v );
			} );
			$form->addField( $qty_reality );
			
			
			$sector = new Form_Field_Input( '/item_'.$p_id.'/sector', '' );
			$sector->setDefaultValue( $item->getSector() );
			$sector->setFieldValueCatcher( function( string $v ) use ($item) : void {
				$item->setSector( $v );
			} );
			$form->addField( $sector );
			
			
			$rack = new Form_Field_Input( '/item_'.$p_id.'/rack', '' );
			$rack->setDefaultValue( $item->getRack() );
			$rack->setFieldValueCatcher( function( string $v ) use ($item) : void {
				$item->setRack( $v );
			} );
			$form->addField( $rack );
			
			
			$position = new Form_Field_Input( '/item_'.$p_id.'/position', '' );
			$position->setDefaultValue( $item->getPosition() );
			$position->setFieldValueCatcher( function( string $v ) use ($item) : void {
				$item->setPosition( $v );
			} );
			$form->addField( $position );
		}
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
		
		
		if( !$this->getStatus()->editable() ) {
			$form->setIsReadonly();
		}
	}
	
	public function catchEditForm() : bool
	{
		return $this->catchForm( $this->getEditForm() );
	}
	
	public static function getStatusList(): array
	{
		return WarehouseManagement_StockVerification_Status::getList();
	}
	
	public function createEvent( EShopEntity_Event|WarehouseManagement_StockVerification_Event $event ): EShopEntity_Event
	{
		$event->init( EShops::getDefault() );
		$event->setVerification( $this );
		return $event;
	}
	
	public function getHistory(): array
	{
		return WarehouseManagement_StockVerification_Event::getEventsList( $this->getId() );
	}
	
}