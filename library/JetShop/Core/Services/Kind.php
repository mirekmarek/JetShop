<?php
/**
 *
 */

namespace JetShop;


use Jet\Tr;

use JetApplication\Payment_Kind;
use JetApplication\Services_Kind;

abstract class Core_Services_Kind {

	public const KIND_DELIVERY = 'delivery';
	public const KIND_PAYMENT = 'payment';
	public const KIND_OTHER = 'other';


	protected string $code = '';

	protected string $title = '';


	/**
	 * @var Payment_Kind[]|null
	 */
	protected static ?array $list = null;

	public static function get( string $code ) : ?Services_Kind
	{
		$list = Services_Kind::getList();
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
	 * @return Services_Kind[]
	 */
	public static function getList() : array
	{
		if(static::$list===null) {
			static::$list = [];
			
			$delivery_service = new Services_Kind();
			$delivery_service->setCode( Services_Kind::KIND_DELIVERY );
			$delivery_service->setTitle( Tr::_('Delivery service') );

			$payment_service = new Services_Kind();
			$payment_service->setCode( Services_Kind::KIND_PAYMENT );
			$payment_service->setTitle( Tr::_('Payment service') );

			$other_service = new Services_Kind();
			$other_service->setCode( Services_Kind::KIND_OTHER );
			$other_service->setTitle( Tr::_('Other service') );

			
			static::$list[$delivery_service->getCode()] = $delivery_service;
			static::$list[$payment_service->getCode()] = $payment_service;
			static::$list[$other_service->getCode()] = $other_service;
		}

		return static::$list;
	}

	public static function getScope() : array
	{
		$list = Services_Kind::getList();


		$res = [];

		foreach($list as $item) {
			$res[$item->getCode()] = $item->getTitle();
		}

		return $res;
	}
}