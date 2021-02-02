<?php
/**
 *
 */

namespace JetShop;


use Jet\Tr;

abstract class Core_Delivery_Kind {

	protected string $code = '';

	protected string $title = '';


	/**
	 * @var Delivery_Kind[]|null
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
	 * @return Core_Delivery_Kind[]
	 */
	public static function getList() : array
	{
		if(static::$list===null) {
			static::$list = [];

			$e_delivery = new Delivery_Kind();
			$e_delivery->setCode('e-delivery');
			$e_delivery->setTitle( Tr::_('e-Delivery', [], Delivery_Method::getManageModuleName()) );

			$personal_takeover = new Delivery_Kind();
			$personal_takeover->setCode('personal-takeover');
			$personal_takeover->setTitle( Tr::_('Personal takeover', [], Delivery_Method::getManageModuleName()) );

			$personal_takeover = new Delivery_Kind();
			$personal_takeover->setCode('personal-takeover');
			$personal_takeover->setTitle( Tr::_('Personal takeover', [], Delivery_Method::getManageModuleName()) );

			$delivery = new Delivery_Kind();
			$delivery->setCode('delivery');
			$delivery->setTitle( Tr::_('Delivery', [], Delivery_Method::getManageModuleName()) );

			static::$list[$delivery->getCode()] = $delivery;
			static::$list[$personal_takeover->getCode()] = $personal_takeover;
			static::$list[$e_delivery->getCode()] = $e_delivery;
		}

		return static::$list;
	}

	public static function getScope() : array
	{
		$list = Delivery_Kind::getList();


		$res = [];

		foreach($list as $kind) {
			$res[$kind->getCode()] = $kind->getTitle();
		}

		return $res;
	}
}