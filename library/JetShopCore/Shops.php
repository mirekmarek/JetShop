<?php
namespace JetShop;

use Jet\Locale;
use Jet\Config;
use Jet\Config_Definition;
use Jet\Data_Text;
use Jet\Mvc_Site;
use Jet\Mvc_Site_Interface;

#[Config_Definition(
	name: 'shops'
)]
class Core_Shops extends Config {

	protected static ?Shops_Shop $current_shop = null;

	/**
	 * @var Shops_Shop[]|null
	 */
	#[Config_Definition(
		type : Config::TYPE_SECTIONS,
		section_creator_method_name : '_shopConfigCreator'
	)]
	protected ?array $shops = null;

	/**
	 * @var Shops_Shop[]|null
	 */
	protected static ?array $_list = null;


	public static function getCurrent() : Shops_Shop
	{
		return static::$current_shop;
	}

	public static function getCurrentCode() : string
	{
		return static::$current_shop->getCode();
	}

	public static function setCurrent( string $shop_code, bool $init_system=false ) : void
	{
		static::$current_shop = static::get($shop_code);

		if($init_system) {
			//TODO:
		}
	}

	public static function exists( string $shop_code ) : bool
	{
		return isset(static::getList()[$shop_code]);
	}
	
	public static function get( ?string $shop_code ) : Shops_Shop|null
	{

		if(!$shop_code) {
			return static::$current_shop;
		}

		return static::getList()[$shop_code];
	}

	/**
	 * @return Shops_Shop[]
	 */
	public static function getList() : array
	{
		if(static::$_list===null) {
			static::$_list = [];

			$i = new Shops();
			foreach( $i->shops as $shop ) {
				static::$_list[$shop->getCode()] = $shop;
			}

			//TODO: seradit - napr. vychozi v administraci
		}

		return static::$_list;
	}

	public static function getScope() : array
	{
		$res = [];

		foreach(Shops::getList() as $shop) {
			$res[$shop->getCode()] = $shop->getName();
		}

		return $res;
	}
	
	public function _shopConfigCreator( array $data ) : Shops_Shop
	{
		return new Shops_Shop( $data );
	}

	public static function determineBySite( Mvc_Site_Interface $site, Locale $locale ) : Shops_Shop|null
	{
		foreach( static::getList() as $shop ) {
			if(
				$shop->getSiteId()==$site->getId() &&
				$shop->getLocale()->toString()==$locale->toString()
			) {
				static::setCurrent( $shop->getCode() );

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
		$id = isset($argv[0]) ? $argv[0] : '';

		foreach(static::getList() as $_sh) {
			if(!$shop) {
				$shop = $_sh;
			}

			if($_sh->getCode()==$id) {
				$shop = $_sh;
				break;
			}
		}

		static::setCurrent( $shop->getCode() );

		return null;
	}

	/**
	 * @return Shops_Shop|null
	 */
	public static function getDefault() : Shops_Shop|null
	{
		foreach(static::getList() as $shop) {
			if($shop->isDefault()) {
				return $shop;
			}
		}

		foreach(static::getList() as $shop) {
			return $shop;
		}
		return null;
	}



















	public static function getName( string|null $shop_code=null ) : string
	{
		return static::get($shop_code)->getName();
	}

	public static function getURL( string|null $shop_code=null, array $path_fragments = [], array $GET_params = [] ) : string
	{
		$shop = static::get( $shop_code );

		$site = Mvc_Site::get( $shop->getSiteId() );

		return $site->getHomepage( $shop->getLocale() )->getURL( $path_fragments, $GET_params );
	}



	public static function getCurrencyCode( string|null $shop_code=null ) : string
	{
		return static::get($shop_code)->getCurrencyCode();
	}

	public static function getCurrencySymbolLeft( string|null $shop_code=null ) : string
	{
		return static::get($shop_code)->getCurrencySymbolLeft();
	}

	public static function getCurrencySymbolRight( string|null $shop_code=null ) : string
	{
		return static::get($shop_code)->getCurrencySymbolRight();
	}

	public static function getCurrencyWithVatTxt( string|null $shop_code=null ) : string
	{
		return static::get($shop_code)->getCurrencyWithVatTxt();
	}

	public static function getCurrencyWoVatTxt( string|null $shop_code=null ) : string
	{
		return static::get($shop_code)->getCurrencyWoVatTxt();
	}

	public static function getCurrencyDecimalSeparator( string|null $shop_code=null ) : string
	{
		return static::get($shop_code)->getCurrencyDecimalSeparator();
	}

	public static function getCurrencyThousandsSeparator( string|null $shop_code=null ) : string
	{
		return static::get($shop_code)->getCurrencyThousandsSeparator();
	}

	public static function getCurrencyDecimalPlaces( string|null $shop_code=null ) : int
	{
		return static::get($shop_code)->getCurrencyDecimalPlaces();
	}

	public static function getVatRates( string|null $shop_code=null ) : array
	{
		return static::get($shop_code)->getVatRates();
	}

	public static function getVatRatesScope( string|null $shop_code=null ) : array
	{
		return static::get($shop_code)->getVatRatesScope();
	}

	public static function getDefaultVatRate( string|null $shop_code=null ) : int
	{
		return static::get($shop_code)->getDefaultVatRate();
	}

	public static function getPhoneValidationRegExp( string|null $shop_code=null ) : string
	{
		return static::get($shop_code)->getPhoneValidationRegExp();
	}

	public static function getPhonePrefix( string|null $shop_code=null ) : string
	{
		return static::get($shop_code)->getPhonePrefix();
	}

	public static function getRoundPrecision_WithoutVAT( string|null $shop_code=null ) : int
	{
		return static::get($shop_code)->getRoundPrecision_WithoutVAT();
	}

	public static function getRoundPrecision_VAT( string|null $shop_code=null ) : int
	{
		return static::get($shop_code)->getRoundPrecision_VAT();
	}

	public static function getRoundPrecision_WithVAT( string|null $shop_code=null ) : int|bool
	{
		return static::get($shop_code)->getRoundPrecision_WithVAT();
	}

	public static function getViewDir() : string
	{
		return Mvc_Site::get(static::$current_shop->getSiteId())->getViewsPath();
	}




	public static function generateURLPathPart( string $name, string $type='', int|null $id=null, string|null $shop_code=null ) : string
	{

		//TODO: jinam ...
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
