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
use Jet\UI_messages;
use JetApplication\EShopEntity_Admin_Interface;
use JetApplication\EShopEntity_Admin_Trait;
use JetApplication\Admin_Managers_WarehouseManagement_TransferBetweenWarehouses;
use JetApplication\Context_ProvidesContext_Interface;
use JetApplication\Context_ProvidesContext_Trait;
use JetApplication\EShopEntity_Basic;
use JetApplication\EShopEntity_Definition;
use JetApplication\EShopEntity_HasNumberSeries_Interface;
use JetApplication\EShop;
use JetApplication\WarehouseManagement_StockCard;
use JetApplication\WarehouseManagement_TransferBetweenWarehouses;
use JetApplication\WarehouseManagement_TransferBetweenWarehouses_Item;
use JetApplication\WarehouseManagement;
use JetApplication\EShopEntity_HasNumberSeries_Trait;
use Jet\Tr;
use Jet\Logger;
use JetApplication\WarehouseManagement_Warehouse;

#[DataModel_Definition(
	name: 'whm_transfer_between_warehouses',
	database_table_name: 'whm_transfer_between_warehouses',
)]
#[EShopEntity_Definition(
	admin_manager_interface: Admin_Managers_WarehouseManagement_TransferBetweenWarehouses::class
)]
class Core_WarehouseManagement_TransferBetweenWarehouses extends EShopEntity_Basic implements
	EShopEntity_HasNumberSeries_Interface,
	Context_ProvidesContext_Interface,
	EShopEntity_Admin_Interface
{
	use Context_ProvidesContext_Trait;
	use EShopEntity_HasNumberSeries_Trait;
	use EShopEntity_Admin_Trait;
	
	public const STATUS_PENDING = 'pending';
	public const STATUS_SENT = 'sent';
	public const STATUS_RECEIVED = 'received';
	public const STATUS_CANCELLED = 'cancelled';
	
	
	#[DataModel_Definition(
		type: DataModel::TYPE_INT,
		is_key: true,
	)]
	protected int $source_warehouse_id = 0;
	
	protected WarehouseManagement_Warehouse|null $source_warehouse = null;
	
	#[DataModel_Definition(
		type: DataModel::TYPE_INT,
		is_key: true,
	)]
	protected int $target_warehouse_id = 0;
	
	protected WarehouseManagement_Warehouse|null $target_warehouse = null;
	
	
	#[DataModel_Definition(
		type: DataModel::TYPE_STRING,
		is_key: true,
		max_len: 50,
	)]
	protected string $status = self::STATUS_PENDING;
	
	
	#[DataModel_Definition(
		type: DataModel::TYPE_DATE_TIME
	)]
	protected ?Data_DateTime $sent_date_time = null;
	
	
	#[DataModel_Definition(
		type: DataModel::TYPE_DATE_TIME
	)]
	protected ?Data_DateTime $receipt_date_time = null;
	
	
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
	 * @var WarehouseManagement_TransferBetweenWarehouses_Item[]
	 */
	#[DataModel_Definition(
		type: DataModel::TYPE_DATA_MODEL,
		data_model_class: WarehouseManagement_TransferBetweenWarehouses_Item::class
	)]
	protected array $items = [];
	
	
	
	public static function getNumberSeriesEntityIsPerShop() : bool
	{
		return false;
	}
	
	public static function getNumberSeriesEntityTitle() : string
	{
		return 'Warehouse management - transfer between warehouses';
	}
	
	public static function getStatusScope(): array
	{
		return [
			static::STATUS_PENDING   => Tr::_( 'Pending' ),
			static::STATUS_SENT      => Tr::_( 'Sent' ),
			static::STATUS_RECEIVED  => Tr::_( 'Received' ),
			static::STATUS_CANCELLED => Tr::_( 'Cancelled' )
		];
	}
	
	
	public function getSourceWarehouseId(): int
	{
		return $this->source_warehouse_id;
	}
	
	public function setSourceWarehouseId( int $source_warehouse_id ): void
	{
		$this->source_warehouse_id = $source_warehouse_id;
		$this->source_warehouse = null;
	}
	
	
	public function getSourceWarehouse(): WarehouseManagement_Warehouse
	{
		if(!$this->source_warehouse) {
			$this->source_warehouse = WarehouseManagement_Warehouse::get( $this->source_warehouse_id );
		}
		return $this->source_warehouse;
	}
	
	
	public function getTargetWarehouseId(): int
	{
		return $this->target_warehouse_id;
	}

	public function setTargetWarehouseId( int $target_warehouse_id ): void
	{
		$this->target_warehouse_id = $target_warehouse_id;
		$this->target_warehouse = null;
	}
	
	public function getTargetWarehouse(): WarehouseManagement_Warehouse
	{
		if(!$this->target_warehouse) {
			$this->target_warehouse = WarehouseManagement_Warehouse::get( $this->target_warehouse_id );
		}
		return $this->target_warehouse;
	}
	
	
	
	
	public function getNumberSeriesEntityShop(): ?EShop
	{
		return null;
	}
	
	
	
	public function getStatus(): string
	{
		return $this->status;
	}
	
	public function setStatus( string $status ): void
	{
		$this->status = $status;
	}
	
	public function getSentDateTime(): ?Data_DateTime
	{
		return $this->sent_date_time;
	}
	
	public function setSentDateTime(  Data_DateTime|string|null  $sent_date_time ): void
	{
		$this->sent_date_time = Data_DateTime::catchDateTime( $sent_date_time );
	}
	
	public function getReceiptDateTime(): ?Data_DateTime
	{
		return $this->receipt_date_time;
	}
	
	public function setReceiptDateTime( Data_DateTime|string|null $receipt_date_time ): void
	{
		$this->receipt_date_time = Data_DateTime::catchDateTime( $receipt_date_time );
	}
	
	public function getNotes(): string
	{
		return $this->notes;
	}
	
	public function setNotes( string $notes ): void
	{
		$this->notes = $notes;
	}
	
	public function prepareNew() : void
	{
		$cards = WarehouseManagement_StockCard::getCardsByWarehouse( $this->source_warehouse_id );
		foreach($cards as $card) {
			if($card->getInStock()<=0) {
				continue;
			}
			
			$item = new WarehouseManagement_TransferBetweenWarehouses_Item();
			
			/**
			 * @var WarehouseManagement_TransferBetweenWarehouses $this
			 */
			$item->setupCard( $this, $card );
			
			$this->items[$item->getProductId()] = $item;
			
		}
	}
	
	
	
	/**
	 * @return WarehouseManagement_TransferBetweenWarehouses_Item[]
	 */
	public function getItems(): array
	{
		return $this->items;
	}
	
	public function addItem( WarehouseManagement_TransferBetweenWarehouses_Item $item ): void
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
	
	
	public function sent() : bool
	{
		if( $this->status != static::STATUS_PENDING ) {
			return false;
		}
		
		
		foreach($this->items as $i=>$item) {
			if(!$item->getNumberOfUnits()) {
				$item->delete();
				unset( $this->items[$i] );
			}
		}
		
		/**
		 * @var WarehouseManagement_TransferBetweenWarehouses $this
		 */
		WarehouseManagement::manageTransferBetweenWarehousesSent( $this );
		
		$this->status = static::STATUS_SENT;
		$this->sent_date_time = Data_DateTime::now();
		$this->save();
		
		Logger::success(
			event: 'whm_transfer_sent',
			event_message: 'WHM - Transfer Of Goods '.$this->getNumber().' hus been completed',
			context_object_id: $this->getId(),
			context_object_name: $this->getNumber(),
			context_object_data: $this
		);
		
		return true;
	}
	
	public function received() : bool
	{
		if( $this->status != static::STATUS_SENT ) {
			return false;
		}
		
		/**
		 * @var WarehouseManagement_TransferBetweenWarehouses $this
		 */
		WarehouseManagement::manageTransferBetweenWarehousesReceived( $this );
		
		$this->status = static::STATUS_RECEIVED;
		$this->receipt_date_time = Data_DateTime::now();
		$this->save();
		
		Logger::success(
			event: 'whm_transfer_received',
			event_message: 'WHM - Transfer Of Goods '.$this->getNumber().' hus been received',
			context_object_id: $this->getId(),
			context_object_name: $this->getNumber(),
			context_object_data: $this
		);
		
		return true;
		
	}
	
	public function cancel() : bool
	{
		if( $this->status == static::STATUS_RECEIVED ) {
			return false;
		}
		
		/**
		 * @var WarehouseManagement_TransferBetweenWarehouses $this
		 */
		WarehouseManagement::manageTransferBetweenWarehousesCanceled( $this );
		
		$this->status = static::STATUS_CANCELLED;
		$this->save();
		
		Logger::success(
			event: 'whm_rog_cancelled',
			event_message: 'WHM - Transfer Of Goods '.$this->getNumber().' hus been cancelled',
			context_object_id: $this->getId(),
			context_object_name: $this->getNumber(),
			context_object_data: $this
		);
		
		return true;
	}
	
	/**
	 * @param int $warehouse_id
	 * @return static[]
	 */
	public static function getSentFromWarehouse( int $warehouse_id ) : array
	{
		$orders = WarehouseManagement_TransferBetweenWarehouses::fetch([''=>[
			'status' => WarehouseManagement_TransferBetweenWarehouses::STATUS_SENT,
			'AND',
			'target_warehouse_id' => $warehouse_id
		]]);
		
		return $orders;
	}
	
	/**
	 * @param int $warehouse_id
	 * @return static[]
	 */
	public static function getSentToWarehouse( int $warehouse_id ) : array
	{
		$orders = WarehouseManagement_TransferBetweenWarehouses::fetch([''=>[
			'status' => WarehouseManagement_TransferBetweenWarehouses::STATUS_SENT,
			'AND',
			'source_warehouse_id' => $warehouse_id
		]]);
		
		return $orders;
	}
	
	public function getAdminTitle() : string
	{
		return $this->number;
	}
	
	protected function setupForm( Form $form ) : void
	{
		$source_wh = $this->getSourceWarehouse();
		
		foreach($this->items as $p_id=>$item) {
			$qty = new Form_Field_Float( '/item_'.$p_id.'/qty', '' );
			$qty->setMaxValue( $source_wh->getCard( $item->getProductId() )->getInStock() );
			$qty->setDefaultValue( $item->getNumberOfUnits() );
			$qty->setFieldValueCatcher( function( float $v ) use ($item) : void {
				$item->setNumberOfUnits( $v );
			} );
			$form->addField( $qty );
			
		}
		
	}
	
	protected function catchForm( Form $form ) : bool
	{
		if(!$form->catch()) {
			return false;
		}
		
		$everything_zero = true;
		foreach($this->getItems() as $item) {
			if($item->getNumberOfUnits()>0) {
				$everything_zero = false;
				break;
			}
		}
		
		if($everything_zero) {
			$form->setCommonMessage(
				UI_messages::createDanger(Tr::_('Please specify at least one item'))
			);
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
		
		foreach($this->items as $p_id=>$item) {
			if( $this->getStatus()==static::STATUS_SENT ) {
				$form->field('/item_'.$p_id.'/qty')->setIsReadonly(true);
			}
			
			$sector = new Form_Field_Input( '/item_'.$p_id.'/sector', '' );
			$sector->setDefaultValue( $item->getTargetSector() );
			$sector->setFieldValueCatcher( function( string $v ) use ($item) : void {
				$item->setTargetSector( $v );
			} );
			$form->addField( $sector );
			
			
			$rack = new Form_Field_Input( '/item_'.$p_id.'/rack', '' );
			$rack->setDefaultValue( $item->getTargetRack() );
			$rack->setFieldValueCatcher( function( string $v ) use ($item) : void {
				$item->setTargetRack( $v );
			} );
			$form->addField( $rack );
			
			$position = new Form_Field_Input( '/item_'.$p_id.'/position', '' );
			$position->setDefaultValue( $item->getTargetPosition() );
			$position->setFieldValueCatcher( function( string $v ) use ($item) : void {
				$item->setTargetPosition( $v );
			} );
			$form->addField( $position );
			
		}
		
		
		if(
			$this->getStatus()==static::STATUS_RECEIVED ||
			$this->getStatus()==static::STATUS_CANCELLED
		) {
			$form->setIsReadonly();
		}
	}
	
	public function catchEditForm() : bool
	{
		return $this->catchForm( $this->getEditForm() );
	}
	
}