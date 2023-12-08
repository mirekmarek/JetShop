<?php
/**
 *
 */

namespace JetShop;


use Jet\Tr;

use JetApplication\Delivery_Kind;

abstract class Core_Delivery_Kind {

	public const KIND_E_DELIVERY = 'e-delivery';
	public const KIND_PERSONAL_TAKEOVER = 'personal-takeover';
	public const KIND_DELIVERY = 'delivery';

	protected string $code = '';

	protected string $title = '';

	protected bool $module_is_required = false;

	/**
	 * @return bool
	 */
	public function moduleIsRequired(): bool
	{
		return $this->module_is_required;
	}

	/**
	 * @param bool $module_is_required
	 */
	public function setModuleIsRequired( bool $module_is_required ): void
	{
		$this->module_is_required = $module_is_required;
	}



	/**
	 * @var Delivery_Kind[]|null
	 */
	protected static ?array $list = null;

	public static function get( string $code ) : ?Delivery_Kind
	{
		$list = Delivery_Kind::getList();
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
	 * @return Delivery_Kind[]
	 */
	public static function getList() : array
	{
		if(static::$list===null) {
			static::$list = [];

			$e_delivery = new Delivery_Kind();
			$e_delivery->setCode( Delivery_Kind::KIND_E_DELIVERY );
			$e_delivery->setTitle( Tr::_('e-Delivery') );

			$personal_takeover = new Delivery_Kind();
			$personal_takeover->setCode( Delivery_Kind::KIND_PERSONAL_TAKEOVER );
			$personal_takeover->setTitle( Tr::_('Personal takeover') );
			$personal_takeover->setModuleIsRequired( true );

			$delivery = new Delivery_Kind();
			$delivery->setCode( Delivery_Kind::KIND_DELIVERY );
			$delivery->setTitle( Tr::_('Delivery') );

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

		foreach($list as $item) {
			$res[$item->getCode()] = $item->getTitle();
		}

		return $res;
	}
}