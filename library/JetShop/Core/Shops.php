<?php
namespace JetShop;

use Jet\Http_Headers;
use Jet\Http_Request;
use Jet\Locale;
use Jet\Data_Text;
use Jet\MVC;
use Jet\Session;

use JetApplication\Shops;
use JetApplication\Shops_Shop;


class Core_Shops {

	protected static ?Shops_Shop $current_shop = null;

	/**
	 * @var Shops_Shop[]|null
	 */
	protected static ?array $_list = null;
	
	const CURR_SHOP_SESSION = 'current_shop';
	const CURR_SHOP_SESSION_KEY = 'key';
	const CURR_SHOP_GET_PARAM = 'select_shop';

	public static function handleCurrentAdminShop() : void
	{
		$all_shops = array_keys(Shops::getList());
		$default_shop = Shops::getDefault();
		
		$session = new Session( Shops::CURR_SHOP_SESSION );
		$current_shop_key = $session->getValue(Shops::CURR_SHOP_SESSION_KEY, '');
		if(!in_array($current_shop_key, $all_shops)) {
			$current_shop_key = $default_shop->getKey();
			$session->setValue(Shops::CURR_SHOP_SESSION_KEY, $current_shop_key);
		}
		
		
		$GET = Http_Request::GET();
		if($GET->exists(Shops::CURR_SHOP_GET_PARAM)) {
			$current_shop_key = $GET->getString(
				key:Shops::CURR_SHOP_GET_PARAM,
				default_value: $default_shop->getKey(),
				valid_values: $all_shops
			);
			
			$session->setValue(Shops::CURR_SHOP_SESSION_KEY, $current_shop_key);
			
			Http_Headers::reload(unset_GET_params: [Shops::CURR_SHOP_GET_PARAM]);
		}
		
		Shops::setCurrent( Shops::get($current_shop_key) );
	}

	public static function getCurrent() : Shops_Shop
	{
		return static::$current_shop;
	}


	public static function getCurrentKey() : string
	{
		return static::$current_shop->getKey();
	}

	public static function setCurrent( Shops_Shop $shop, bool $init_system=false ) : void
	{
		static::$current_shop = $shop;

		/** @noinspection PhpStatementHasEmptyBodyInspection */
		if($init_system) {
			//TODO:
		}
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

	/**
	 * @return Shops_Shop|null
	 */
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











	public static function getName( ?Shops_Shop $shop = null ) : string
	{
		return ($shop ?:static::$current_shop)->getShopName();
	}

	public static function getURL( ?Shops_Shop $shop = null, array $path_fragments = [], array $GET_params = [] ) : string
	{
		$shop = ($shop ?:static::$current_shop);

		$base = MVC::getBase( $shop->getBaseId() );

		return $base->getHomepage( $shop->getLocale() )->getURL( $path_fragments, $GET_params );
	}

	
	public static function getCurrencySymbolLeft( ?Shops_Shop $shop = null ) : string
	{
		return ($shop ?:static::$current_shop)->getCurrencySymbolLeft();
	}

	public static function getCurrencySymbolRight( ?Shops_Shop $shop = null ) : string
	{
		return ($shop ?:static::$current_shop)->getCurrencySymbolRight();
	}

	public static function getCurrencyWithVatTxt( ?Shops_Shop $shop = null ) : string
	{
		return ($shop ?:static::$current_shop)->getCurrencyWithVatTxt();
	}

	public static function getCurrencyWoVatTxt( ?Shops_Shop $shop = null ) : string
	{
		return ($shop ?:static::$current_shop)->getCurrencyWoVatTxt();
	}

	public static function getCurrencyDecimalSeparator( ?Shops_Shop $shop = null ) : string
	{
		return ($shop ?:static::$current_shop)->getCurrencyDecimalSeparator();
	}

	public static function getCurrencyThousandsSeparator( ?Shops_Shop $shop = null ) : string
	{
		return ($shop ?:static::$current_shop)->getCurrencyThousandsSeparator();
	}

	public static function getCurrencyDecimalPlaces( ?Shops_Shop $shop = null ) : int
	{
		return ($shop ?:static::$current_shop)->getCurrencyDecimalPlaces();
	}

	public static function getVatRates( ?Shops_Shop $shop = null ) : array
	{
		return ($shop ?:static::$current_shop)->getVatRates();
	}

	public static function getVatRatesScope( ?Shops_Shop $shop = null ) : array
	{
		return ($shop ?:static::$current_shop)->getVatRatesScope();
	}

	public static function getDefaultVatRate( ?Shops_Shop $shop = null ) : int
	{
		return ($shop ?:static::$current_shop)->getDefaultVatRate();
	}

	public static function getPhoneValidationRegExp( ?Shops_Shop $shop = null ) : string
	{
		return ($shop ?:static::$current_shop)->getPhoneValidationRegExp();
	}

	public static function getPhonePrefix( ?Shops_Shop $shop = null ) : string
	{
		return ($shop ?:static::$current_shop)->getPhonePrefix();
	}

	public static function getRoundPrecision_WithoutVAT( ?Shops_Shop $shop = null) : int
	{
		return ($shop ?:static::$current_shop)->getRoundPrecision_WithoutVAT();
	}

	public static function getRoundPrecision_VAT( ?Shops_Shop $shop = null) : int
	{
		return ($shop ?:static::$current_shop)->getRoundPrecision_VAT();
	}

	public static function getRoundPrecision_WithVAT( ?Shops_Shop $shop = null) : int|bool
	{
		return ($shop ?:static::$current_shop)->getRoundPrecision_WithVAT();
	}

	public static function getViewDir( ?Shops_Shop $shop = null ) : string
	{
		return MVC::getBase(($shop ?:static::$current_shop)->getBaseId())->getViewsPath();
	}




	public static function generateURLPathPart( string $name, string $type='', string|int|null $id=null, ?Shops_Shop $shop=null ) : string
	{

		//TODO: move somewhere else ...
		$name = Data_Text::removeAccents( $name );

		$name = strtolower($name);
		$name = preg_replace('/([^0-9a-zA-Z ])+/', '', $name);
		$name = preg_replace( '/([[:blank:]])+/', '-', $name);


		$min_len = 2;

		$parts = explode('-', $name);
		$valid_parts = array();
		foreach( $parts as $value ) {

			if (strlen($value) > $min_len) {
				$valid_parts[] = $value;
			}
		}

		$name = count($valid_parts) > 1 ? implode('-', $valid_parts) : $name;

		if($type) {
			return $name.'-'.$type.'-'.$id;
		} else {
			return $name;
		}


	}


}
