<?php
/**
 * @copyright Copyright (c) Miroslav Marek <mirek.marek@web-jet.cz>
 * @license EUPL 1.2  https://eupl.eu/1.2/en/
 * @author Miroslav Marek <mirek.marek@web-jet.cz>
 */
namespace JetShop;


use Jet\IO_File;
use JetApplication\Currency;
use JetApplication\EShopConfig;


abstract class Core_Currencies {
	
	/**
	 * @var Currency[]|null
	 */
	protected static ?array $_list = null;
	protected static ?Currency $current = null;
	
	
	public static function getCfgFilePath() : string
	{
		return EShopConfig::getRootDir().'currencies.php';
	}
	
	public static function loadCfg() : void
	{
		static::$_list = [];
		
		if(IO_File::exists(static::getCfgFilePath())) {
			$cfg = require static::getCfgFilePath();
			
			foreach($cfg as $item) {
				static::addCurrency( (new Currency( $item )) );
			}
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

	
	public static function getCurrent() : Currency
	{
		return static::$current;
	}
	
	public static function setCurrent( Currency $currency ) : void
	{
		static::$current = $currency;
	}
	
	public static function exists( string $key ) : bool
	{
		return isset(static::getList()[$key]);
	}
	
	public static function get( string $code ) : Currency|null
	{
		return static::getList()[$code]??null;
	}
	
	public static function calcExchange( Currency $from_currency, Currency $to_currency, float $price ) : float
	{
		if($from_currency->getCode()==$to_currency->getCode()) {
			return $price;
		}
		
		return $price * $from_currency->getExchangeRate( $to_currency );
	}
	
	public static function getExchangeRate( Currency $from_currency, Currency $to_currency ) : float
	{
		if($from_currency->getCode()==$to_currency->getCode()) {
			return 1.0;
		}
		
		return $from_currency->getExchangeRate( $to_currency );
	}
	
	
	/**
	 * @return Currency[]
	 */
	public static function getList() : array
	{
		if(static::$_list===null) {
			static::loadCfg();
		}
		
		return static::$_list;
	}
	
	public static function addCurrency( Currency $currency ) : void
	{
		static::$_list[$currency->getCode()] = $currency;
	}
	
	
	public static function removeCurrency( Currency $currency ) : void
	{
		unset( static::$_list[$currency->getCode()] );
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
