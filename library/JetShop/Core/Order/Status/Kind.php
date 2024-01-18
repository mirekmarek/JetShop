<?php
/**
 *
 */

namespace JetShop;


use Jet\Tr;

use JetApplication\Admin_Managers;
use JetApplication\Order_Status_Kind;

abstract class Core_Order_Status_Kind {

	public const KIND_NEW = 'new';
	public const KIND_WAITING_FOR_PAYMENT = 'waiting_for_payment';
	public const KIND_PAID = 'paid';
	public const KIND_BEING_PROCESSED = 'being_processed';
	public const KIND_IS_DISPATCHED = 'is_dispatched';
	public const KIND_CANCELLED = 'cancelled';
	public const KIND_RETURNED = 'returned';


	protected string $code = '';

	protected string $title = '';


	/**
	 * @var Order_Status_Kind[]|null
	 */
	protected static ?array $list = null;

	public static function get( string $code ) : ?Order_Status_Kind
	{
		$list = Order_Status_Kind::getList();
		if(!isset($list[$code])) {
			return null;
		}

		return $list[$code];
	}

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
	 * @return Order_Status_Kind[]
	 */
	public static function getList() : array
	{
		if(static::$list===null) {
			static::$list = [];

			$new = new Order_Status_Kind();
			$new->setCode( Order_Status_Kind::KIND_NEW );
			$new->setTitle( Tr::_('New order') );

			
			$dictionary = Admin_Managers::Order()->getModuleManifest()->getName();

			$waiting_for_payment = new Order_Status_Kind();
			$waiting_for_payment->setCode( Order_Status_Kind::KIND_WAITING_FOR_PAYMENT );
			$waiting_for_payment->setTitle( Tr::_('Waiting for payment', dictionary: $dictionary) );
			
			$paid = new Order_Status_Kind();
			$paid->setCode( Order_Status_Kind::KIND_PAID );
			$paid->setTitle( Tr::_('Paid') );

			
			$being_processed = new Order_Status_Kind();
			$being_processed->setCode( Order_Status_Kind::KIND_BEING_PROCESSED );
			$being_processed->setTitle( Tr::_('Order is being processed', dictionary: $dictionary) );

			$is_dispatched = new Order_Status_Kind();
			$is_dispatched->setCode( Order_Status_Kind::KIND_IS_DISPATCHED );
			$is_dispatched->setTitle( Tr::_('Order is dispatched', dictionary: $dictionary) );

			$cancelled = new Order_Status_Kind();
			$cancelled->setCode( Order_Status_Kind::KIND_CANCELLED );
			$cancelled->setTitle( Tr::_('Cancelled', dictionary: $dictionary) );

			$returned = new Order_Status_Kind();
			$returned->setCode( Order_Status_Kind::KIND_RETURNED );
			$returned->setTitle( Tr::_('Returned', dictionary: $dictionary) );

			static::$list[$new->getCode()] = $new;
			static::$list[$waiting_for_payment->getCode()] = $waiting_for_payment;
			static::$list[$paid->getCode()] = $paid;
			static::$list[$being_processed->getCode()] = $being_processed;
			static::$list[$is_dispatched->getCode()] = $is_dispatched;
			static::$list[$cancelled->getCode()] = $cancelled;
			static::$list[$returned->getCode()] = $returned;
		}

		return static::$list;
	}

	public static function getScope() : array
	{
		
		$list = Order_Status_Kind::getList();


		$res = [];

		foreach($list as $item) {
			$res[$item->getCode()] = $item->getTitle();
		}

		return $res;
	}
}