<?php
/**
 *
 */

namespace JetShop;


use Jet\Tr;

abstract class Core_Services_Kind {

	protected string $code = '';

	protected string $title = '';


	/**
	 * @var Payment_Kind[]|null
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
	 * @return Services_Kind[]
	 */
	public static function getList() : array
	{
		if(static::$list===null) {
			static::$list = [];

			$delivery_service = new Services_Kind();
			$delivery_service->setCode('delivery_service');
			$delivery_service->setTitle( Tr::_('Delivery service', [], Services_Service::getManageModuleName()) );

			$payment_service = new Services_Kind();
			$payment_service->setCode('payment_service');
			$payment_service->setTitle( Tr::_('Payment service', [], Services_Service::getManageModuleName()) );

			$other_service = new Services_Kind();
			$other_service->setCode('other_service');
			$other_service->setTitle( Tr::_('Other service', [], Services_Service::getManageModuleName()) );

			
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

		foreach($list as $kind) {
			$res[$kind->getCode()] = $kind->getTitle();
		}

		return $res;
	}
}