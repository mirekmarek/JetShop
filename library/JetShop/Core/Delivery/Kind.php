<?php
/**
 * @copyright Copyright (c) Miroslav Marek <mirek.marek@web-jet.cz>
 * @license EUPL 1.2  https://eupl.eu/1.2/en/
 * @author Miroslav Marek <mirek.marek@web-jet.cz>
 */
namespace JetShop;



use Jet\Tr;

use JetApplication\Delivery_Kind;

abstract class Core_Delivery_Kind {

	public const E_DELIVERY = 'e-delivery';
	public const PERSONAL_TAKEOVER_EXTERNAL = 'personal-takeover-external';
	public const PERSONAL_TAKEOVER_INTERNAL = 'personal-takeover-internal';
	public const DELIVERY = 'delivery';

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


			$personal_takeover_external = new Delivery_Kind();
			$personal_takeover_external->setCode( Delivery_Kind::PERSONAL_TAKEOVER_EXTERNAL );
			$personal_takeover_external->setTitle( Tr::_('Personal takeover - external') );
			$personal_takeover_external->setModuleIsRequired( true );
			static::add( $personal_takeover_external );
			
			
			$personal_takeover_internal = new Delivery_Kind();
			$personal_takeover_internal->setCode( Delivery_Kind::PERSONAL_TAKEOVER_INTERNAL );
			$personal_takeover_internal->setTitle( Tr::_('Personal takeover - internal') );
			$personal_takeover_internal->setModuleIsRequired( true );
			static::add( $personal_takeover_internal );
			
			
			$delivery = new Delivery_Kind();
			$delivery->setCode( Delivery_Kind::DELIVERY );
			$delivery->setTitle( Tr::_('Delivery') );
			static::add( $delivery );
			
			
			$e_delivery = new Delivery_Kind();
			$e_delivery->setCode( Delivery_Kind::E_DELIVERY );
			$e_delivery->setTitle( Tr::_('e-Delivery') );
			static::add( $e_delivery );
		}

		return static::$list;
	}
	
	public static function add( Delivery_Kind $kind ) : void
	{
		static::$list[$kind->getCode()] = $kind;
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