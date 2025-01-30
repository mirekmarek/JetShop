<?php
/**
 * 
 */

namespace JetShop;

use Jet\Data_DateTime;
use Jet\DataModel;
use Jet\DataModel_Definition;

use JetApplication\Context;
use JetApplication\Context_HasContext_Interface;
use JetApplication\Context_HasContext_Trait;
use JetApplication\Currencies;
use JetApplication\Currency;
use JetApplication\EShopEntity_Basic;
use JetApplication\MeasureUnit;
use JetApplication\MeasureUnits;
use JetApplication\WarehouseManagement_StockCard;
use JetApplication\WarehouseManagement_StockMovement;
use JetApplication\WarehouseManagement_StockMovement_Type;
use JetApplication\WarehouseManagement_Warehouse;

#[DataModel_Definition(
	name: 'warehouse_stock_movement',
	database_table_name: 'whm_stock_movements',
)]
class Core_WarehouseManagement_StockMovement extends EShopEntity_Basic implements Context_HasContext_Interface
{
	use Context_HasContext_Trait;
	
	#[DataModel_Definition(
		type: DataModel::TYPE_INT,
		is_key: true,
	)]
	protected int $warehouse_id = 0;
	
	protected ?WarehouseManagement_Warehouse $warehouse = null;
	
	#[DataModel_Definition(
		type: DataModel::TYPE_BOOL,
		is_key: true,
	)]
	protected bool $cancelled = false;
	
	#[DataModel_Definition(
		type: DataModel::TYPE_DATE_TIME,
		is_key: true,
	)]
	protected ?Data_DateTime $cancelled_date_time = null;
	
	#[DataModel_Definition(
		type: DataModel::TYPE_STRING,
		is_key: true,
		max_len: 255,
	)]
	protected string $type = '';

	#[DataModel_Definition(
		type: DataModel::TYPE_INT,
		is_key: true,
	)]
	protected int $product_id = 0;
	
	#[DataModel_Definition(
		type: DataModel::TYPE_DATE_TIME,
	)]
	protected ?Data_DateTime $date_time = null;
	
	#[DataModel_Definition(
		type: DataModel::TYPE_FLOAT,
	)]
	protected float $number_of_units = 0.0;
	
	#[DataModel_Definition(
		type: DataModel::TYPE_STRING,
		max_len: 64,
	)]
	protected string $measure_unit = '';
	
	
	
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
		type: DataModel::TYPE_STRING,
		is_key: true,
		max_len: 50,
	)]
	protected string $entry_currency_code = '';
	
	
	#[DataModel_Definition(
		type: DataModel::TYPE_FLOAT
	)]
	protected float $entry_currency_exchange_rate = 1.0;
	
	#[DataModel_Definition(
		type: DataModel::TYPE_FLOAT,
	)]
	protected float $entry_price_per_unit = 0.0;
	
	
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
	
	/**
	 * @param Context $context
	 * @param ?string $type
	 *
	 * @return static[]
	 */
	public static function getByContext( Context $context, ?string $type=null ) : array
	{
		$where = $context->getWhere();
		
		if($type) {
			$where[] = 'AND';
			$where['type'] = $type;
		}
		
		return static::fetch( [
			'this' => $where
		],
			order_by: ['-date_time']);
	}
	
	
	/**
	 * @param int $product_id
	 * @return static[]
	 */
	public static function getByProduct(int $product_id ) : array
	{
		return static::fetch( [
			'this' => [
				'product_id' => $product_id
			]
		],
			order_by: ['-date_time']);
	}
	
	
	/**
	 * @param int $warehouse_id
	 * @return static[]
	 */
	public static function getByWarehouse(int $warehouse_id ) : array
	{
		return static::fetch( [
			'this' => [
				'warehouse_id' => $warehouse_id
			]
		],
			order_by: ['-date_time']);
	}
	
	/**
	 * @param int $warehouse_id
	 * @param int $product_id
	 * @return static[]
	 */
	public static function getByProductAndWarehouse(int $warehouse_id, int $product_id ) : array
	{
		return static::fetch( [
			'this' => [
				'warehouse_id' => $warehouse_id,
				'AND',
				'product_id' => $product_id
			]
		],
			order_by: ['-date_time']
		);
	}
	
	/**
	 * @param WarehouseManagement_StockCard $card
	 *
	 * @return static[]
	 */
	public static function getByCard( WarehouseManagement_StockCard $card ) : array
	{
		return static::getByProductAndWarehouse(
			warehouse_id:  $card->getWarehouseId(),
			product_id: $card->getProductId()
		);
	}
	
	public static function getBlockingCount(  WarehouseManagement_StockCard $card, Context $exclude_context, Data_DateTime $before_date ) : float
	{
		$movements = static::fetch( [
			'this' => [
				'warehouse_id' => $card->getWarehouseId(),
				'AND',
				'product_id' => $card->getProductId(),
				'AND',
				'cancelled' => false,
				'AND',
				'type' => WarehouseManagement_StockMovement_Type::Blocking()->getCode(),
				'AND',
				'date_time <=' => $before_date
				]
			],
			order_by: ['-date_time']
		);
		
		$blocking = 0.0;
		foreach($movements as $mv) {
			if(
				$mv->getContextType()==$exclude_context->getContextType() &&
				$mv->getContextId()==$exclude_context->getContextId()
			) {
				continue;
			}
			
			$blocking += $mv->getNumberOfUnits();
		}
		
		return $blocking;
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

	public function getWarehouseId(): int
	{
		return $this->warehouse_id;
	}
	
	public function getWarehouse() : WarehouseManagement_Warehouse
	{
		if(!$this->warehouse) {
			$this->warehouse = WarehouseManagement_Warehouse::get( $this->warehouse_id );
		}
		
		return $this->warehouse;
	}
	
	public function setWarehouse( WarehouseManagement_Warehouse $warehouse ): void
	{
		$this->warehouse_id = $warehouse->getId();
		$this->warehouse = $warehouse;
	}
	
	public function getCancelled(): bool
	{
		return $this->cancelled;
	}
	
	public function setCancelled( bool $cancelled ): void
	{
		$this->cancelled = $cancelled;
	}
	
	public function getCancelledDateTime(): ?Data_DateTime
	{
		return $this->cancelled_date_time;
	}
	
	public function setCancelledDateTime( Data_DateTime|null|string $cancelled_date_time ): void
	{
		$this->cancelled_date_time = Data_DateTime::catchDateTime( $cancelled_date_time );
	}
	
	public function cancel() : void
	{
		if(!$this->cancelled) {
			/**
			 * @var WarehouseManagement_StockMovement $this
			 */
			
			$this->cancelled = true;
			$this->cancelled_date_time = Data_DateTime::now();
			$this->save();
			
			
			$wh_card = $this->getWarehouse()->getCard( $this->getProductId() );
			$wh_card->calcReverse( $this );
			$wh_card->save();
			
		}
	}
	
	
	

	public function getType(): WarehouseManagement_StockMovement_Type
	{
		return WarehouseManagement_StockMovement_Type::get($this->type);
	}

	public function setType( WarehouseManagement_StockMovement_Type $type ): void
	{
		$this->type = $type->getCode();
	}

	public function getDateTime(): ?Data_DateTime
	{
		return $this->date_time;
	}

	public function setDateTime( Data_DateTime|null|string $date_time ): void
	{
		$this->date_time = Data_DateTime::catchDateTime( $date_time );
	}

	public function getNumberOfUnits(): float
	{
		return $this->number_of_units;
	}

	public function setNumberOfUnits( float $number_of_units, ?MeasureUnit $measure_unit ): void
	{
		if($measure_unit) {
			$number_of_units = $measure_unit->round( $number_of_units );
			$this->measure_unit = $measure_unit->getCode();
		}
		
		$this->number_of_units = $number_of_units;
	}
	
	public function getMeasureUnit(): ?MeasureUnit
	{
		return MeasureUnits::get( $this->measure_unit );
	}
	
	
	public function getPricePerUnit(): float
	{
		return $this->price_per_unit;
	}
	
	public function getEntryPricePerUnit(): float
	{
		return $this->entry_price_per_unit;
	}
	
	public function setEntryPricePerUnit( float $entry_price_per_unit ): void
	{
		$this->entry_price_per_unit = $entry_price_per_unit;
	}
	
	public function getCurrencyCode(): string
	{
		return $this->currency_code;
	}
	
	public function setCurrencyCode( string $currency_code ): void
	{
		$this->currency_code = $currency_code;
	}
	
	public function getCurrency(): ?Currency
	{
		if(!$this->currency_code) {
			return null;
		}
		return Currencies::get( $this->currency_code );
	}
	
	
	public function setEntryCurrencyCode( string $entry_currency_code ): void
	{
		$this->entry_currency_code = $entry_currency_code;
	}
	
	public function getEntryCurrencyCode(): string
	{
		return $this->entry_currency_code;
	}
	
	
	public function getEntryCurrency() : ?Currency
	{
		if(!$this->entry_currency_code) {
			return null;
		}
		return Currencies::get( $this->entry_currency_code );
	}
	
	public function getEntryCurrencyIsDifferent() : bool
	{
		return $this->currency_code!=$this->entry_currency_code;
	}
	
	

	public function getContextType(): string
	{
		return $this->context_type;
	}
	
	public function getContextNumber(): string
	{
		return $this->context_number;
	}
	
	public function getContextId(): int
	{
		return $this->context_id;
	}

	
	
	public function getEntryCurrencyExchangeRate(): float
	{
		return $this->entry_currency_exchange_rate;
	}
	
	public function setPricePerUnit( Currency $entry_currency, float $entry_price_per_unit ): void
	{
		$wh_currency = $this->getWarehouse()->getCurrency();
		
		$this->currency_code = $wh_currency->getCode();
		$this->entry_currency_code = $entry_currency->getCode();
		
		if( $wh_currency->getCode()==$entry_currency->getCode() ) {
			$this->price_per_unit = $entry_price_per_unit;
			
			$this->entry_price_per_unit = $entry_price_per_unit;
			$this->entry_currency_exchange_rate = 1.0;
		} else {
			$this->price_per_unit = Currencies::calcExchange( $entry_currency, $wh_currency, $entry_price_per_unit );
			
			$this->entry_price_per_unit = $entry_price_per_unit;
			$this->entry_currency_exchange_rate = Currencies::getExchangeRate( $entry_currency, $wh_currency );
		}
		
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
	
}
