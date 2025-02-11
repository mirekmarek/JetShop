<?php
/**
 * @copyright Copyright (c) Miroslav Marek <mirek.marek@web-jet.cz>
 * @license EUPL 1.2  https://eupl.eu/1.2/en/
 * @author Miroslav Marek <mirek.marek@web-jet.cz>
 */
namespace JetShop;



use Jet\Locale;
use Jet\MVC;

use JetApplication\Availabilities;
use JetApplication\Pricelists;
use JetApplication\EShops;
use JetApplication\EShop;


class Core_EShops {

	protected static ?EShop $current_eshop = null;

	/**
	 * @var EShop[]|null
	 */
	protected static ?array $_list = null;
	

	public static function getCurrent() : EShop
	{
		return static::$current_eshop;
	}


	public static function getCurrentKey() : string
	{
		return static::$current_eshop->getKey();
	}

	public static function setCurrent( EShop $eshop ) : void
	{
		static::$current_eshop = $eshop;
		
		Availabilities::setCurrent( $eshop->getDefaultAvailability() );
		Pricelists::setCurrent( $eshop->getDefaultPricelist() );
	}
	
	public static function exists( string $key ) : bool
	{
		return isset(static::getList()[$key]);
	}

	public static function get( string $key ) : EShop|null
	{
		foreach( static::getList() as $eshop ) {
			if($eshop->getKey()==$key) {
				return $eshop;
			}
		}

		return null;
	}


	/**
	 * @return EShop[]
	 */
	public static function getList() : array
	{
		if(static::$_list===null) {
			static::$_list = [];

			foreach(MVC::getBases() as $base) {
				foreach( EShop::init($base) as $key=> $eshop) {
					static::$_list[$key] = $eshop;
				}
			}
		}

		return static::$_list;
	}
	
	public static function isMultiEShopMode() : bool
	{
		return count(static::getList())>1;
	}
	
	public static function isMultilanguageMode() : bool
	{
		return count(static::getAvailableLocales())>1;
	}
	
	/**
	 * @return EShop[]
	 */
	public static function getListSorted() : array
	{
		$current = EShops::getCurrent();
		$_list = static::getList();
		
		$other = [];
		foreach($_list as $sh) {
			if($sh->getKey()!=$current->getKey()) {
				$other[$sh->getKey()]=$sh;
			}
		}
		
		uasort( $other, function( EShop $a, EShop $b ) {
			return strcmp( $a->getName(), $b->getName() );
		});
		
		$list = [];
		$list[$current->getKey()] = $current;
		$list += $other;
		
		return $list;
	}
	
	/**
	 * @return Locale[]
	 */
	public static function getAvailableLocales() : array
	{
		$list = [];
		
		foreach(static::getListSorted() as $eshop) {
			$locale = $eshop->getLocale();
			$locale_str = $locale->toString();
			if(!isset($list[$locale_str])) {
				$list[$locale_str] = $locale;
			}
		}
		
		return $list;
	}
	

	public static function getScope() : array
	{
		$res = [];

		foreach( EShops::getList() as $key=> $eshop) {
			$res[$key] = $eshop->getName();
		}

		return $res;
	}

	public static function determineByBase( string $base_id, Locale $locale ) : EShop|null
	{

		foreach(static::getList() as $eshop) {
			if(
				$eshop->getBaseId()==$base_id &&
				$eshop->getLocale()->toString()==$locale->toString()
			) {
				static::setCurrent( $eshop );

				return $eshop;
			}
		}


		return null;
	}


	public static function determineByCliArg( array $argv ) : EShop|null
	{
		/**
		 * @var EShop $eshop
		 */
		$eshop = null;
		$key = $argv[0] ?? '';

		foreach(static::getList() as $_sh) {
			if(!$eshop) {
				$eshop = $_sh;
			}

			if($_sh->getKey()==$key) {
				$eshop = $_sh;
				break;
			}
		}

		static::setCurrent( $eshop );

		return null;
	}

	public static function getDefault() : EShop|null
	{
		foreach(static::getList() as $eshop) {
			if($eshop->getIsDefault()) {
				return $eshop;
			}
		}

		foreach(static::getList() as $eshop) {
			return $eshop;
		}
		return null;
	}
	
	

}
