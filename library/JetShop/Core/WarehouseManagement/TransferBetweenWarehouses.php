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
use Jet\Tr;
use JetApplication\Admin_Managers_WarehouseManagement_TransferBetweenWarehouses;
use JetApplication\Context_ProvidesContext_Interface;
use JetApplication\Context_ProvidesContext_Trait;
use JetApplication\EShop;
use JetApplication\EShops;
use JetApplication\EShopEntity_Admin_Interface;
use JetApplication\EShopEntity_Admin_Trait;
use JetApplication\EShopEntity_Basic;
use JetApplication\EShopEntity_Definition;
use JetApplication\EShopEntity_Event;
use JetApplication\EShopEntity_HasEvents_Interface;
use JetApplication\EShopEntity_HasGet_Interface;
use JetApplication\EShopEntity_HasGet_Trait;
use JetApplication\EShopEntity_HasNumberSeries_Interface;
use JetApplication\EShopEntity_HasStatus_Interface;
use JetApplication\EShopEntity_HasStatus_Trait;
use JetApplication\EShopEntity_HasNumberSeries_Trait;
use JetApplication\WarehouseManagement_StockCard;
use JetApplication\WarehouseManagement_TransferBetweenWarehouses;
use JetApplication\WarehouseManagement_TransferBetweenWarehouses_Event;
use JetApplication\WarehouseManagement_TransferBetweenWarehouses_Item;
use JetApplication\WarehouseManagement_TransferBetweenWarehouses_Status;
use JetApplication\WarehouseManagement_TransferBetweenWarehouses_Status_Cancelled;
use JetApplication\WarehouseManagement_TransferBetweenWarehouses_Status_Pending;
use JetApplication\WarehouseManagement_TransferBetweenWarehouses_Status_Received;
use JetApplication\WarehouseManagement_TransferBetweenWarehouses_Status_Sent;
use JetApplication\WarehouseManagement_Warehouse;

#[DataModel_Definition(
	name: 'whm_transfer_between_warehouses',
	database_table_name: 'whm_transfer_between_warehouses',
)]
#[EShopEntity_Definition(
	entity_name_readable: 'Warehouse management - Transfer between warehouses',
	admin_manager_interface: Admin_Managers_WarehouseManagement_TransferBetweenWarehouses::class
)]
abstract class Core_WarehouseManagement_TransferBetweenWarehouses extends EShopEntity_Basic implements
	EShopEntity_HasNumberSeries_Interface,
	EShopEntity_HasGet_Interface,
	Context_ProvidesContext_Interface,
	EShopEntity_Admin_Interface,
	EShopEntity_HasStatus_Interface,
	EShopEntity_HasEvents_Interface
{
	use Context_ProvidesContext_Trait;
	use EShopEntity_HasNumberSeries_Trait;
	use EShopEntity_HasGet_Trait;
	use EShopEntity_Admin_Trait;
	use EShopEntity_HasStatus_Trait;

	
	protected static array $flags = [
	];
	
	
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
		type: DataModel::TYPE_DATE_TIME
	)]
	protected ?Data_DateTime $sent_date_time = null;
	
	
	#[DataModel_Definition(
		type: DataModel::TYPE_DATE_TIME
	)]
	protected ?Data_DateTime $receipt_date_time = null;
	
	
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
	
	public function getSentDateTime(): ?Data_DateTime
	{
		return $this->sent_date_time;
	}
	
	public function setSentDateTime( ?Data_DateTime $sent_date_time ): void
	{
		$this->sent_date_time = $sent_date_time;
	}
	
	public function getReceiptDateTime(): ?Data_DateTime
	{
		return $this->receipt_date_time;
	}
	
	public function setReceiptDateTime( ?Data_DateTime $receipt_date_time ): void
	{
		$this->receipt_date_time = $receipt_date_time;
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
		$this->setStatus( WarehouseManagement_TransferBetweenWarehouses_Status_Pending::get() );
	}
	
	public function afterUpdate(): void
	{
		parent::afterUpdate();
	}
	
	public function cleanupItems() : void
	{
		foreach($this->items as $i=>$item) {
			if(!$item->getNumberOfUnits()) {
				$item->delete();
				unset( $this->items[$i] );
			}
		}
		
	}
	
	public function sent() : bool
	{
		$this->setStatus( WarehouseManagement_TransferBetweenWarehouses_Status_Sent::get() );
		
		return true;
	}
	
	public function received() : bool
	{
		$this->setStatus( WarehouseManagement_TransferBetweenWarehouses_Status_Received::get() );
		
		return true;
		
	}
	
	public function cancel() : bool
	{
		$this->setStatus( WarehouseManagement_TransferBetweenWarehouses_Status_Cancelled::get() );
		
		return true;
	}
	
	/**
	 * @param int $warehouse_id
	 * @return static[]
	 */
	public static function getSentFromWarehouse( int $warehouse_id ) : array
	{
		$orders = WarehouseManagement_TransferBetweenWarehouses::fetch([''=>[
			'status' => WarehouseManagement_TransferBetweenWarehouses_Status_Pending::CODE,
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
			'status' => WarehouseManagement_TransferBetweenWarehouses_Status_Sent::CODE,
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
			if( $this->getStatus()->sent() ) {
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
		return WarehouseManagement_TransferBetweenWarehouses_Status::getList();
	}
	
	public function createEvent( EShopEntity_Event|WarehouseManagement_TransferBetweenWarehouses_Event $event ): EShopEntity_Event
	{
		$event->init( EShops::getDefault() );
		$event->setTransfare( $this );

		return $event;
	}
	
	public function getHistory(): array
	{
		return WarehouseManagement_TransferBetweenWarehouses_Event::getEventsList( $this->getId() );
	}
}