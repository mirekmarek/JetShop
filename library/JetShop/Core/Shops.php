<?php
namespace JetShop;


use Jet\Locale;
use Jet\MVC;

use JetApplication\Availabilities;
use JetApplication\Pricelists;
use JetApplication\Shops;
use JetApplication\Shops_Shop;


class Core_Shops {

	protected static ?Shops_Shop $current_shop = null;

	/**
	 * @var Shops_Shop[]|null
	 */
	protected static ?array $_list = null;
	

	public static function getCurrent() : Shops_Shop
	{
		return static::$current_shop;
	}


	public static function getCurrentKey() : string
	{
		return static::$current_shop->getKey();
	}

	public static function setCurrent( Shops_Shop $shop ) : void
	{
		static::$current_shop = $shop;
		
		Availabilities::setCurrent( $shop->getDefaultAvailability() );
		Pricelists::setCurrent( $shop->getDefaultPricelist() );
	}

	public static function exists( string $key ) : bool
	{
		return isset(static::getList()[$key]);
	}

	public static function get( string $key ) : Shops_Shop|null
	{
		foreach( static::getList() as $shop ) {
			if($shop->getKey()==$key) {
				return $shop;
			}
		}

		return null;
	}


	/**
	 * @return Shops_Shop[]
	 */
	public static function getList() : array
	{
		if(static::$_list===null) {
			static::$_list = [];

			foreach(MVC::getBases() as $base) {
				foreach(Shops_Shop::init($base) as $key=>$shop) {
					static::$_list[$key] = $shop;
				}
			}
		}

		return static::$_list;
	}
	
	public static function isMultiShopMode() : bool
	{
		return count(static::getList())>1;
	}
	
	/**
	 * @return Shops_Shop[]
	 */
	public static function getListSorted() : array
	{
		$current = Shops::getCurrent();
		$_list = static::getList();
		
		$other = [];
		foreach($_list as $sh) {
			if($sh->getKey()!=$current->getKey()) {
				$other[$sh->getKey()]=$sh;
			}
		}
		
		uasort( $other, function( Shops_Shop $a, Shops_Shop $b ) {
			return strcmp( $a->getShopName(), $b->getShopName() );
		});
		
		$list = [];
		$list[$current->getKey()] = $current;
		$list += $other;
		
		return $list;
	}
	

	public static function getScope() : array
	{
		$res = [];

		foreach(Shops::getList() as $key=>$shop) {
			$res[$key] = $shop->getShopName();
		}

		return $res;
	}

	public static function determineByBase( string $base_id, Locale $locale ) : Shops_Shop|null
	{

		foreach(static::getList() as $shop) {
			if(
				$shop->getBaseId()==$base_id &&
				$shop->getLocale()->toString()==$locale->toString()
			) {
				static::setCurrent( $shop );

				return $shop;
			}
		}


		return null;
	}


	public static function determineByCliArg( array $argv ) : Shops_Shop|null
	{
		/**
		 * @var Shops_Shop $shop
		 */
		$shop = null;
		$key = $argv[0] ?? '';

		foreach(static::getList() as $_sh) {
			if(!$shop) {
				$shop = $_sh;
			}

			if($_sh->getKey()==$key) {
				$shop = $_sh;
				break;
			}
		}

		static::setCurrent( $shop );

		return null;
	}

	public static function getDefault() : Shops_Shop|null
	{
		foreach(static::getList() as $shop) {
			if($shop->getIsDefaultShop()) {
				return $shop;
			}
		}

		foreach(static::getList() as $shop) {
			return $shop;
		}
		return null;
	}
	
	

}
