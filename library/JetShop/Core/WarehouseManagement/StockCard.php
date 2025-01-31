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
use Jet\Logger;
use JetApplication\EShopEntity_Admin_Interface;
use JetApplication\EShopEntity_Admin_Trait;
use JetApplication\Admin_Managers_WarehouseManagement_Overview;
use JetApplication\Currencies;
use JetApplication\Currency;
use JetApplication\EShopEntity_Basic;
use JetApplication\EShopEntity_HasGet_Interface;
use JetApplication\EShopEntity_HasGet_Trait;
use JetApplication\EShopEntity_Definition;
use JetApplication\Product;
use JetApplication\Supplier_GoodsOrder;
use JetApplication\WarehouseManagement_StockCard;
use JetApplication\WarehouseManagement_StockMovement;
use JetApplication\WarehouseManagement_TransferBetweenWarehouses;
use JetApplication\WarehouseManagement_Warehouse;

/**
 *
 */
#[DataModel_Definition(
	name: 'warehouse_stock_card',
	database_table_name: 'whm_stock_cards',
)]
#[EShopEntity_Definition(
	admin_manager_interface: Admin_Managers_WarehouseManagement_Overview::class
)]
abstract class Core_WarehouseManagement_StockCard extends EShopEntity_Basic implements
	EShopEntity_Admin_Interface,
	EShopEntity_HasGet_Interface
{
	use EShopEntity_HasGet_Trait;
	use EShopEntity_Admin_Trait;
	
	#[DataModel_Definition(
		type: DataModel::TYPE_INT,
		is_key: true,
	)]
	protected string $warehouse_id = '';
	
	protected ?WarehouseManagement_Warehouse $warehouse = null;
	
	#[DataModel_Definition(
		type: DataModel::TYPE_INT,
		is_key: true,
	)]
	protected int $product_id = 0;

	
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
		type: DataModel::TYPE_STRING,
		max_len: 50,
	)]
	protected string $currency_code = '';
	
	#[DataModel_Definition(
		type: DataModel::TYPE_FLOAT,
	)]
	protected float $price_per_unit = 0;
	
	
	#[DataModel_Definition(
		type: DataModel::TYPE_FLOAT
	)]
	protected float $in_stock = 0;
	
	#[DataModel_Definition(
		type: DataModel::TYPE_FLOAT
	)]
	protected float $blocked = 0;
	
	#[DataModel_Definition(
		type: DataModel::TYPE_FLOAT
	)]
	protected float $available = 0;
	
	#[DataModel_Definition(
		type: DataModel::TYPE_FLOAT
	)]
	protected float $total_in = 0;
	
	#[DataModel_Definition(
		type: DataModel::TYPE_FLOAT
	)]
	protected float $total_out = 0;
	
	#[DataModel_Definition(
		type: DataModel::TYPE_BOOL
	)]
	protected bool $cancelled = false;
	
	
	/**
	 * @param int $product_id
	 * @return static[]
	 */
	public static function getCardsByProduct(int $product_id ) : array
	{
		return static::fetch( [
			'this' => [
				'product_id' => $product_id
			]
		]);
	}
	
	
	/**
	 * @param int $warehouse_id
	 * @return static[]
	 */
	public static function getCardsByWarehouse(int $warehouse_id ) : array
	{
		return static::fetch( [
			'this' => [
				'warehouse_id' => $warehouse_id
			]
		]);
	}
	
	
	/**
	 * @return static[]
	 */
	public static function getList() : iterable
	{
		$where = [];
		
		$list = static::fetchInstances( $where );
		
		return $list;
	}
	
	public function setProductId( int $value ) : void
	{
		$this->product_id = (string)$value;
	}
	
	public function getProductId() : int
	{
		return $this->product_id;
	}

	public function getWarehouseId(): string
	{
		return $this->warehouse_id;
	}

	public function setWarehouseId( string $warehouse_id ): void
	{
		$this->warehouse_id = $warehouse_id;
		$this->warehouse = null;
	}
	
	public function getWarehouse() : WarehouseManagement_Warehouse
	{
		if( !$this->warehouse ) {
			$this->warehouse = WarehouseManagement_Warehouse::get( $this->warehouse_id );
		}
		
		return $this->warehouse;
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
	
	public function getPricePerUnit(): float
	{
		return $this->price_per_unit;
	}
	
	public function setPricePerUnit( float $price_per_unit, ?Currency $currency=null ): void
	{
		$wh_currency = $this->getWarehouse()->getCurrency();
		if(!$currency) {
			$currency = $wh_currency;
		}
		
		$this->currency_code = $wh_currency->getCode();
		
		if($wh_currency->getCode()==$currency->getCode()) {
			$this->price_per_unit = $price_per_unit;
		} else {
			$this->price_per_unit = Currencies::calcExchange( $currency, $wh_currency, $price_per_unit );
		}
		
	}
	
	public function getCurrency(): ?Currency
	{
		if(!$this->currency_code) {
			return null;
		}
		return Currencies::get( $this->currency_code );
	}
	

	
	public function getInStock(): float
	{
		return $this->in_stock;
	}
	
	public function setInStock( float $in_stock ): void
	{
		$this->in_stock = $in_stock;
	}
	
	public function getBlocked(): float
	{
		return $this->blocked;
	}
	
	public function setBlocked( float $blocked ): void
	{
		$this->blocked = $blocked;
	}
	
	public function getAvailable(): float
	{
		return $this->available;
	}
	
	public function setAvailable( float $available ): void
	{
		$this->available = $available;
	}
	
	public function getTotalIn(): float
	{
		return $this->total_in;
	}
	
	public function setTotalIn( float $total_in ): void
	{
		$this->total_in = $total_in;
	}
	
	public function getTotalOut(): float
	{
		return $this->total_out;
	}
	
	public function setTotalOut( float $total_out ): void
	{
		$this->total_out = $total_out;
	}
	
	public function getCancelled(): bool
	{
		return $this->cancelled;
	}
	
	public function setCancelled( bool $cancelled ): void
	{
		$this->cancelled = $cancelled;
	}
	
	
	
	public function cancel() : void
	{
		if($this->in_stock<=0 && !$this->cancelled) {
			$this->cancelled = true;
			$this->save();
			
			Logger::info(
				event: 'stock_card_cancelled',
				event_message: 'Stock card '.$this->getAdminTitle().' cancelled',
				context_object_id: $this->getId(),
				context_object_name: $this->getAdminTitle(),
				context_object_data: $this
			);
			
		}
	}
	
	public function reactivate() : void
	{
		if( $this->cancelled ) {
			$this->cancelled = false;
			$this->save();
			
			Logger::info(
				event: 'stock_card_reactivated',
				event_message: 'Stock card '.$this->getAdminTitle().' reactivated',
				context_object_id: $this->getId(),
				context_object_name: $this->getAdminTitle(),
				context_object_data: $this
			);
			
		}
	}

	
	
	public function recalculate() : void
	{
		$this->in_stock = 0;
		
		$this->blocked = 0;
		$this->available = 0;
		
		$this->total_in = 0;
		$this->total_out = 0;
		
		$moves = WarehouseManagement_StockMovement::fetch( [
			'this' => [
				'warehouse_id' => $this->warehouse_id,
				'AND',
				'product_id' => $this->product_id
			]
		],
			order_by: ['+date_time']
		);
		
		foreach($moves as $movement) {
			if($movement->getCancelled()) {
				continue;
			}
			
			$this->calc( $movement );
		}

		$this->available = $this->in_stock;
		if($this->blocked>0) {
			$this->available -= $this->blocked;
		}
		
		
		$this->save();
		
		Logger::info(
			event: 'stock_card_recalculated',
			event_message: 'Stock card '.$this->getAdminTitle().' recalculated',
			context_object_id: $this->getId(),
			context_object_name: $this->getAdminTitle(),
			context_object_data: $this
		);
		
	}
	
	public function recalculateTillDateTime( Data_DateTime $date_time ) : void
	{

		$ts = $date_time->getTimestamp();
		
		$this->in_stock = 0;
		
		$this->blocked = 0;
		$this->available = 0;
		
		$this->total_in = 0;
		$this->total_out = 0;
		
		$moves = WarehouseManagement_StockMovement::fetch( [
			'this' => [
				'warehouse_id' => $this->warehouse_id,
				'AND',
				'product_id' => $this->product_id
				]
			],
			order_by: ['+date_time']
		);
		
		foreach($moves as $movement) {
			
			if($movement->getDateTime()->getTimestamp() > $ts ) {
				break;
			}
			
			if(
				$movement->getCancelled() &&
				$movement->getCancelledDateTime()->getTimestamp() <= $ts
			) {
				continue;
			}
			
			$this->calc( $movement );
		}
		
		$this->available = $this->in_stock;
		if($this->blocked>0) {
			$this->available -= $this->blocked;
		}
		
	}
	
	
	public function calc( WarehouseManagement_StockMovement $movement ) : void
	{
		$type = $movement->getType();
		
		if($type->getBlockedAdd()) {
			$this->blocked += $movement->getNumberOfUnits();
		}
		if($type->getBlockedSubtract()) {
			$this->blocked -= $movement->getNumberOfUnits();
		}
		
		if($type->getInStockAdd()) {
			$this->in_stock += $movement->getNumberOfUnits();
			$this->total_in += $movement->getNumberOfUnits();
		}
		
		if($type->getInStockSubtract()) {
			$this->in_stock -= $movement->getNumberOfUnits();
			$this->total_out += $movement->getNumberOfUnits();
		}
		
		if($this->blocked>0) {
			$this->available = $this->in_stock-$this->blocked;
		} else {
			$this->available = $this->in_stock;
		}
		
	}
	
	public function calcReverse( WarehouseManagement_StockMovement $movement ) : void
	{
		$type = $movement->getType();
		
		if($type->getBlockedAdd()) {
			$this->blocked -= $movement->getNumberOfUnits();
		}
		if($type->getBlockedSubtract()) {
			$this->blocked += $movement->getNumberOfUnits();
		}
		
		if($type->getInStockAdd()) {
			$this->in_stock -= $movement->getNumberOfUnits();
			$this->total_in -= $movement->getNumberOfUnits();
		}
		
		if($type->getInStockSubtract()) {
			$this->in_stock += $movement->getNumberOfUnits();
			$this->total_out -= $movement->getNumberOfUnits();
		}
		
		if($this->blocked>0) {
			$this->available = $this->in_stock-$this->blocked;
		} else {
			$this->available = $this->in_stock;
		}
	}
	
	/**
	 * @return WarehouseManagement_StockMovement[]
	 */
	public function getMovements() : array
	{
		/**
		 * @var WarehouseManagement_StockCard $this
		 */
		return WarehouseManagement_StockMovement::getByCard( $this );
	}
	
	
	public function getNumberOfOrderedSupplier() : float
	{
		$orders = Supplier_GoodsOrder::getSentForWarehouse( $this->warehouse_id );
		
		$res = 0.0;
		
		foreach( $orders as $order ) {
			foreach($order->getItems() as $item) {
				if($item->getProductId()==$this->product_id) {
					$res += $item->getUnitsOrdered();
				}
			}
		}
		
		return $res;
	}
	
	public function getNumberOnTheWayFromAnotherWarehouse() : float
	{
		$transfers = WarehouseManagement_TransferBetweenWarehouses::getSentFromWarehouse( $this->warehouse_id );

		$res = 0.0;
		
		foreach( $transfers as $transfer ) {
			foreach($transfer->getItems() as $item) {
				if($item->getProductId()==$this->product_id) {
					$res += $item->getNumberOfUnits();
				}
			}
		}
		
		return $res;
	}
	
	public function getNumberOnTheWayToAnotherWarehouse() : float
	{
		$transfers = WarehouseManagement_TransferBetweenWarehouses::getSentToWarehouse( $this->warehouse_id );
		
		$res = 0.0;
		
		foreach( $transfers as $transfer ) {
			foreach($transfer->getItems() as $item) {
				if($item->getProductId()==$this->product_id) {
					$res += $item->getNumberOfUnits();
				}
			}
		}
		
		return $res;
	}
	
	/**
	 * @return Supplier_GoodsOrder[]
	 */
	public function getRelevantOrdersFromSupplier() : array
	{
		$_orders = Supplier_GoodsOrder::getSentForWarehouse( $this->warehouse_id );
		
		$orders = [];
		
		foreach( $_orders as $order ) {
			foreach($order->getItems() as $item) {
				if($item->getProductId()==$this->product_id) {
					$orders[] = $order;
				}
			}
		}
		
		return $orders;
	}
	
	/**
	 * @return WarehouseManagement_TransferBetweenWarehouses[]
	 */
	public function getRelevantTransfersToAnotherWarehouse() : array
	{
		$_transfers = WarehouseManagement_TransferBetweenWarehouses::getSentToWarehouse( $this->warehouse_id );
		
		$transfers = [];
		
		foreach( $_transfers as $transfer ) {
			foreach($transfer->getItems() as $item) {
				if($item->getProductId()==$this->product_id) {
					$transfers[] = $transfer;
				}
			}
		}
		
		return $transfers;
		
	}
	
	
	/**
	 * @return WarehouseManagement_TransferBetweenWarehouses[]
	 */
	public function getRelevantTransfersFromAnotherWarehouse() : array
	{
		$_transfers = WarehouseManagement_TransferBetweenWarehouses::getSentFromWarehouse( $this->warehouse_id );
		
		$transfers = [];
		
		foreach( $_transfers as $transfer ) {
			foreach($transfer->getItems() as $item) {
				if($item->getProductId()==$this->product_id) {
					$transfers[] = $transfer;
				}
			}
		}
		
		return $transfers;
		
	}
	
	
	public function getAdminTitle() : string
	{
		$warehouse = $this->getWarehouse();
		$product = Product::get( $this->product_id );
		
		return $warehouse->getInternalName() .' / '.$product->getAdminTitle();
	}
	
	
	public function isEditable(): bool
	{
		return false;
	}
	
	public function setEditable( bool $editable ): void
	{
	}
	
	public function getAddForm(): Form
	{
		return new Form('', []);
	}
	
	public function catchAddForm(): bool
	{
		return false;
	}
	
	public function getEditForm(): Form
	{
		return new Form('', []);
	}
	
	public function catchEditForm(): bool
	{
		return false;
	}
	
}
