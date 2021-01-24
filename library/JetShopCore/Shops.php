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


	public static function getCurrentId() : string
	{
		return static::$current_shop->getId();
	}

	public static function setCurrent( string $shop_id, bool $init_system=false ) : void
	{
		static::$current_shop = static::get($shop_id);

		if($init_system) {
			//TODO:
		}
	}

	public static function exists( string $shop_id ) : bool
	{
		return isset(static::getList()[$shop_id]);
	}
	
	public static function get( ?string $shop_id ) : Shops_Shop|null
	{

		if(!$shop_id) {
			return static::$current_shop;
		}

		return static::getList()[$shop_id];
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
				static::$_list[$shop->getId()] = $shop;
			}

			//TODO: seradit - napr. vychozi v administraci
		}

		return static::$_list;

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
				static::setCurrent( $shop->getId() );

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

			if($_sh->getId()==$id) {
				$shop = $_sh;
				break;
			}
		}

		static::setCurrent( $shop->getId() );

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



















	public static function getName( string|null $shop_id=null ) : string
	{
		return static::get($shop_id)->getName();
	}

	public static function getURL( string|null $shop_id=null, array $path_fragments = [], array $GET_params = [] ) : string
	{
		$shop = static::get( $shop_id );

		$site = Mvc_Site::get( $shop->getSiteId() );

		return $site->getHomepage( $shop->getLocale() )->getURL( $path_fragments, $GET_params );
	}



	public static function getCurrencyCode( string|null $shop_id=null ) : string
	{
		return static::get($shop_id)->getCurrencyCode();
	}

	public static function getCurrencySymbolLeft( string|null $shop_id=null ) : string
	{
		return static::get($shop_id)->getCurrencySymbolLeft();
	}

	public static function getCurrencySymbolRight( string|null $shop_id=null ) : string
	{
		return static::get($shop_id)->getCurrencySymbolRight();
	}

	public static function getCurrencyWithVatTxt( string|null $shop_id=null ) : string
	{
		return static::get($shop_id)->getCurrencyWithVatTxt();
	}

	public static function getCurrencyWoVatTxt( string|null $shop_id=null ) : string
	{
		return static::get($shop_id)->getCurrencyWoVatTxt();
	}

	public static function getCurrencyDecimalSeparator( string|null $shop_id=null ) : string
	{
		return static::get($shop_id)->getCurrencyDecimalSeparator();
	}

	public static function getCurrencyThousandsSeparator( string|null $shop_id=null ) : string
	{
		return static::get($shop_id)->getCurrencyThousandsSeparator();
	}

	public static function getCurrencyDecimalPlaces( string|null $shop_id=null ) : int
	{
		return static::get($shop_id)->getCurrencyDecimalPlaces();
	}

	public static function getVatRates( string|null $shop_id=null ) : array
	{
		return static::get($shop_id)->getVatRates();
	}

	public static function getVatRatesScope( string|null $shop_id=null ) : array
	{
		return static::get($shop_id)->getVatRatesScope();
	}

	public static function getDefaultVatRate( string|null $shop_id=null ) : int
	{
		return static::get($shop_id)->getDefaultVatRate();
	}

	public static function getPhoneValidationRegExp( string|null $shop_id=null ) : string
	{
		return static::get($shop_id)->getPhoneValidationRegExp();
	}

	public static function getPhonePrefix( string|null $shop_id=null ) : string
	{
		return static::get($shop_id)->getPhonePrefix();
	}

	public static function getRoundPrecision_WithoutVAT( string|null $shop_id=null ) : int
	{
		return static::get($shop_id)->getRoundPrecision_WithoutVAT();
	}

	public static function getRoundPrecision_VAT( string|null $shop_id=null ) : int
	{
		return static::get($shop_id)->getRoundPrecision_VAT();
	}

	public static function getRoundPrecision_WithVAT( string|null $shop_id=null ) : int|bool
	{
		return static::get($shop_id)->getRoundPrecision_WithVAT();
	}





	public static function generateURLPathPart( string $name, string $type='', int|null $id=null, string|null $shop_id=null ) : string
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
