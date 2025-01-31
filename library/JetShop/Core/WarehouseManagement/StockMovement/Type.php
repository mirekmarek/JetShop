<?php
/**
 * @copyright Copyright (c) Miroslav Marek <mirek.marek@web-jet.cz>
 * @license EUPL 1.2  https://eupl.eu/1.2/en/
 * @author Miroslav Marek <mirek.marek@web-jet.cz>
 */
namespace JetShop;



use Jet\Tr;

use JetApplication\WarehouseManagement_StockMovement_Type;

abstract class Core_WarehouseManagement_StockMovement_Type
{
	protected const IN = 'in';
	protected const OUT = 'out';
	protected const BLOCKING = 'blocking';
	
	protected const TRANSFER_IN = 'transfer_in';
	protected const TRANSFER_OUT = 'transfer_out';
	protected const TRANSFER_BLOCKING = 'transfer_blocking';
	
	protected const LOSS_OR_DESTRUCTION = 'loss_or_destruction';


	protected string $code = '';
	
	protected string $title = '';

	protected bool $blocked_add = false;
	
	protected bool $blocked_subtract = false;
	
	protected bool $in_stock_add = false;
	
	protected bool $in_stock_subtract = false;
	
	protected bool $cal_use_price_per_unit = true;
	
	
	/**
	 * @var WarehouseManagement_StockMovement_Type[]|null
	 */
	protected static ?array $list = null;
	
	public function getCode(): string
	{
		return $this->code;
	}

	public function setCode( string $code ): void
	{
		$this->code = $code;
	}

	public function getTitle(): string
	{
		return $this->title;
	}
	
	protected function setTitle( string $title ): void
	{
		$this->title = Tr::_($title, dictionary: Tr::COMMON_DICTIONARY );
	}

	public function getBlockedAdd(): bool
	{
		return $this->blocked_add;
	}
	
	public function getBlockedSubtract(): bool
	{
		return $this->blocked_subtract;
	}

	public function getInStockAdd(): bool
	{
		return $this->in_stock_add;
	}

	public function getInStockSubtract(): bool
	{
		return $this->in_stock_subtract;
	}
	
	public function getCalUsePricePerUnit(): bool
	{
		return $this->cal_use_price_per_unit;
	}
	
	public function setCalUsePricePerUnit( bool $cal_use_price_per_unit ): void
	{
		$this->cal_use_price_per_unit = $cal_use_price_per_unit;
	}
	
	
	
	public static function get( string $code ) : static
	{
		static::getList();
		
		return static::$list[$code];
	}
	
	protected static function addType( WarehouseManagement_StockMovement_Type $type ) : void
	{
		static::$list[$type->getCode()] = $type;
	}

	public static function getList() : array
	{
		if(static::$list===null) {
			static::$list = [];
			
			$IN = new static();
			$IN->setCode(static::IN);
			$IN->setTitle( Tr::_('Receipt Of Goods') );
			$IN->in_stock_add = true;
			static::addType( $IN );

			
			$OUT = new static();
			$OUT->setCode(static::OUT);
			$OUT->setTitle( 'Issue of goods' );
			$OUT->in_stock_subtract = true;
			static::addType( $OUT );

			$TRANSFER_IN = new static();
			$TRANSFER_IN->setCode(static::TRANSFER_IN);
			$TRANSFER_IN->setTitle( 'Transfer - Receipt Of Goods' );
			$TRANSFER_IN->in_stock_add = true;
			static::addType( $TRANSFER_IN );

			$TRANSFER_OUT = new static();
			$TRANSFER_OUT->setCode(static::TRANSFER_OUT);
			$TRANSFER_OUT->setTitle( 'Transfer - Issue of goods' );
			$TRANSFER_OUT->in_stock_subtract = true;
			static::addType( $TRANSFER_OUT );


			$BLOCKING = new static();
			$BLOCKING->setCode(static::BLOCKING);
			$BLOCKING->setTitle( 'Blocking' );
			$BLOCKING->blocked_add = true;
			$BLOCKING->cal_use_price_per_unit = false;
			static::addType( $BLOCKING );


			$TRANSFER_BLOCKING = new static();
			$TRANSFER_BLOCKING->setCode(static::TRANSFER_BLOCKING);
			$TRANSFER_BLOCKING->setTitle( 'Transfer - Blocking' );
			$TRANSFER_BLOCKING->blocked_add = true;
			static::addType( $TRANSFER_BLOCKING );
			
			
			
			$LOSS_OR_DESTRUCTION = new static();
			$LOSS_OR_DESTRUCTION->setCode(static::LOSS_OR_DESTRUCTION);
			$LOSS_OR_DESTRUCTION->setTitle( 'Loss or destruction' );
			$LOSS_OR_DESTRUCTION->in_stock_subtract = true;
			static::addType( $LOSS_OR_DESTRUCTION );
			
		}
		
		return static::$list;
	}
	
	public function __toString() : string
	{
		return $this->code;
	}
	
	
	public static function In() : static
	{
		return static::get( static::IN );
	}
	
	public static function Out() : static
	{
		return static::get( static::OUT );
	}
	
	public static function TransferIn() : static
	{
		return static::get( static::TRANSFER_IN );
	}
	
	public static function TransferOut() : static
	{
		return static::get( static::TRANSFER_OUT );
	}
	
	public static function Blocking() : static
	{
		return static::get( static::BLOCKING );
	}
	
	
	public static function TransferBlocking() : static
	{
		return static::get( static::TRANSFER_BLOCKING );
	}
	
	public static function LossOrDestruction() : static
	{
		return static::get( static::LOSS_OR_DESTRUCTION );
	}

}