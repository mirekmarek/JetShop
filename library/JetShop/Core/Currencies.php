<?php
namespace JetShop;

use Jet\IO_File;
use Jet\SysConf_Path;
use JetApplication\Currencies_Currency;


abstract class Core_Currencies {
	
	/**
	 * @var Currencies_Currency[]|null
	 */
	protected static ?array $_list = null;
	protected static ?Currencies_Currency $current = null;
	
	
	public static function getCfgFilePath() : string
	{
		return SysConf_Path::getConfig().'shop/currencies.php';
	}
	
	public static function loadCfg() : void
	{
		static::$_list = [];
		
		$cfg = require static::getCfgFilePath();
		
		foreach($cfg as $item) {
			static::addCurrency( (new Currencies_Currency( $item )) );
		}
	}
	
	public static function saveCfg() : void
	{
		$cfg = [];
		
		foreach( static::getList() as $item ) {
			$cfg[] = $item->toArray();
		}
		
		IO_File::writeDataAsPhp(
			static::getCfgFilePath(),
			$cfg
		);
	}

	
	public static function getCurrent() : Currencies_Currency
	{
		return static::$current;
	}
	
	public static function setCurrent( Currencies_Currency $currency ) : void
	{
		static::$current = $currency;
	}
	
	public static function exists( string $key ) : bool
	{
		return isset(static::getList()[$key]);
	}
	
	public static function get( string $code ) : Currencies_Currency|null
	{
		return static::getList()[$code]??null;
	}
	
	public static function calcExchange( Currencies_Currency $from_currency, Currencies_Currency $to_currency, float $price ) : float
	{
		if($from_currency->getCode()==$to_currency->getCode()) {
			return $price;
		}
		
		return $price * $from_currency->getExchangeRate( $to_currency );
	}
	
	public static function getExchangeRate( Currencies_Currency $from_currency, Currencies_Currency $to_currency ) : float
	{
		if($from_currency->getCode()==$to_currency->getCode()) {
			return 1.0;
		}
		
		return $from_currency->getExchangeRate( $to_currency );
	}
	
	
	/**
	 * @return Currencies_Currency[]
	 */
	public static function getList() : array
	{
		if(static::$_list===null) {
			static::loadCfg();
		}
		
		return static::$_list;
	}
	
	public static function addCurrency( Currencies_Currency $pricelist ) : void
	{
		static::$_list[$pricelist->getCode()] = $pricelist;
	}
	
	
	public static function removeCurrency( Currencies_Currency $pricelist ) : void
	{
		unset( static::$_list[$pricelist->getCode()] );
	}
	
	public static function getScope() : array
	{
		$scope = [];
		
		foreach( static::getList() as $currency ) {
			$scope[ $currency->getCode() ] = $currency->getCode();
		}
		
		return $scope;
	}
}
