<?php
/**
 *
 */

namespace JetShop;


use Jet\Tr;

abstract class Core_WarehouseManagement_Item_Event_Type
{
	const RECEIPT_OF_GOODS = 'receipt_of_goods';
	/*
	const OUT = 'out';
	const TRANSFER_IN = 'transfer_in';
	const TRANSFER_OUT = 'transfer_out';
	const BLOCKING = 'blocking';
	const UNBLOCKING = 'unblocking';
	const TRANSFER_BLOCKING = 'transfer_blocking';
	const TRANSFER_UNBLOCKING = 'transfer_unblocking';
	*/

	protected string $code = '';

	protected string $title = '';

	protected string $main_type = '';

	protected bool $blocked_add = false;

	protected bool $blocked_subtract = false;

	protected bool $in_stock_add = false;

	protected bool $in_stock_subtract = false;


	/**
	 * @var WarehouseManagement_Item_Event_Type[]|null
	 */
	protected static ?array $list = null;


	/**
	 * @return string
	 */
	public function getCode(): string
	{
		return $this->code;
	}

	/**
	 * @param string $code
	 */
	public function setCode( string $code ): void
	{
		$this->code = $code;
	}

	/**
	 * @return string
	 */
	public function getTitle(): string
	{
		return $this->title;
	}

	/**
	 * @param string $title
	 */
	public function setTitle( string $title ): void
	{
		$this->title = $title;
	}

	/**
	 * @return string
	 */
	public function getMainType(): string
	{
		return $this->main_type;
	}

	/**
	 * @param string $main_type
	 */
	public function setMainType( string $main_type ): void
	{
		$this->main_type = $main_type;
	}

	/**
	 * @return bool
	 */
	public function isBlockedAdd(): bool
	{
		return $this->blocked_add;
	}

	/**
	 * @return bool
	 */
	public function isBlockedSubtract(): bool
	{
		return $this->blocked_subtract;
	}

	/**
	 * @return bool
	 */
	public function isInStockAdd(): bool
	{
		return $this->in_stock_add;
	}

	/**
	 * @return bool
	 */
	public function isInStockSubtract(): bool
	{
		return $this->in_stock_subtract;
	}






	/**
	 * @return WarehouseManagement_Item_Event_Type[]
	 */
	public static function getList() : array
	{
		if(static::$list===null) {
			static::$list = [];

			$RECEIPT_OF_GOODS = new WarehouseManagement_Item_Event_Type();
			$RECEIPT_OF_GOODS->setCode(static::RECEIPT_OF_GOODS);
			$RECEIPT_OF_GOODS->setTitle( Tr::_('Receipt Of Goods', [], WarehouseManagement::getOrderManageModuleName()) );
			$RECEIPT_OF_GOODS->in_stock_add = true;
			static::$list[$RECEIPT_OF_GOODS->getCode()] = $RECEIPT_OF_GOODS;

			/*
			$OUT = new WarehouseManagement_Item_Event_Type();
			$OUT->setCode(static::OUT);
			$OUT->setTitle( Tr::_('Issue of goods', [], WarehouseManagement::getOrderManageModuleName()) );
			$OUT->in_stock_subtract = true;
			static::$list[$OUT->getCode()] = $OUT;

			$TRANSFER_IN = new WarehouseManagement_Item_Event_Type();
			$TRANSFER_IN->setCode(static::TRANSFER_IN);
			$TRANSFER_IN->setTitle( Tr::_('Transfer - Receipt Of Goods', [], WarehouseManagement::getOrderManageModuleName()) );
			$TRANSFER_IN->in_stock_add = true;
			static::$list[$TRANSFER_IN->getCode()] = $TRANSFER_IN;

			$TRANSFER_OUT = new WarehouseManagement_Item_Event_Type();
			$TRANSFER_OUT->setCode(static::TRANSFER_OUT);
			$TRANSFER_OUT->setTitle( Tr::_('Transfer - Issue of goods', [], WarehouseManagement::getOrderManageModuleName()) );
			$TRANSFER_OUT->in_stock_subtract = true;
			static::$list[$TRANSFER_OUT->getCode()] = $TRANSFER_OUT;


			$BLOCKING = new WarehouseManagement_Item_Event_Type();
			$BLOCKING->setCode(static::BLOCKING);
			$BLOCKING->setTitle( Tr::_('Blocking', [], WarehouseManagement::getOrderManageModuleName()) );
			$BLOCKING->blocked_add = true;
			static::$list[$BLOCKING->getCode()] = $BLOCKING;


			$UNBLOCKING = new WarehouseManagement_Item_Event_Type();
			$UNBLOCKING->setCode(static::UNBLOCKING);
			$UNBLOCKING->setTitle( Tr::_('Unblocking', [], WarehouseManagement::getOrderManageModuleName()) );
			$UNBLOCKING->blocked_subtract = true;
			static::$list[$UNBLOCKING->getCode()] = $UNBLOCKING;

			$TRANSFER_BLOCKING = new WarehouseManagement_Item_Event_Type();
			$TRANSFER_BLOCKING->setCode(static::TRANSFER_BLOCKING);
			$TRANSFER_BLOCKING->setTitle( Tr::_('Transfer - Blocking', [], WarehouseManagement::getOrderManageModuleName()) );
			$TRANSFER_BLOCKING->blocked_add = true;
			static::$list[$BLOCKING->getCode()] = $TRANSFER_BLOCKING;


			$TRANSFER_UNBLOCKING = new WarehouseManagement_Item_Event_Type();
			$TRANSFER_UNBLOCKING->setCode(static::TRANSFER_UNBLOCKING);
			$TRANSFER_UNBLOCKING->setTitle( Tr::_('Transfer - Unblocking', [], WarehouseManagement::getOrderManageModuleName()) );
			$TRANSFER_UNBLOCKING->blocked_subtract = true;
			static::$list[$TRANSFER_UNBLOCKING->getCode()] = $TRANSFER_UNBLOCKING;
			*/

		}

		return static::$list;
	}

}