<?php
namespace JetShop;

use Jet\IO_File;
use Jet\SysConf_Path;
use JetApplication\Currencies;
use JetApplication\Pricelists_Pricelist;


abstract class Core_Pricelists {
	
	/**
	 * @var Pricelists_Pricelist[]|null
	 */
	protected static ?array $_list = null;
	protected static ?Pricelists_Pricelist $current = null;
	
	public static function getCfgFilePath() : string
	{
		return SysConf_Path::getConfig().'shop/pricelists.php';
	}
	
	public static function loadCfg() : void
	{
		static::$_list = [];
		
		$cfg = require static::getCfgFilePath();
		
		foreach($cfg as $item) {
			static::addPricelist( (new Pricelists_Pricelist( $item )) );
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
	
	/**
	 * @return Pricelists_Pricelist
	 */
	public static function getCurrent() : Pricelists_Pricelist
	{
		return static::$current;
	}

	public static function setCurrent( Pricelists_Pricelist $pricelist ) : void
	{
		static::$current = $pricelist;
		
		Currencies::setCurrent( $pricelist->getCurrency() );
		
	}

	public static function exists( string $code ) : bool
	{
		return isset(static::getList()[$code]);
	}

	public static function get( string $code ) : Pricelists_Pricelist|null
	{
		return static::getList()[$code]??null;
	}


	/**
	 * @return Pricelists_Pricelist[]
	 */
	public static function getList() : array
	{
		if(static::$_list===null) {
			static::loadCfg();
		}
		
		return static::$_list;
	}
	
	public static function getScope() : array
	{
		$scope = [];
		
		foreach(static::getList() as $pl) {
			$scope[$pl->getCode()] = $pl->getName();
		}
		
		return $scope;
	}
	
	public static function addPricelist( Pricelists_Pricelist $pricelist ) : void
	{
		static::$_list[$pricelist->getCode()] = $pricelist;
	}
	
	public static function removePricelist( Pricelists_Pricelist $pricelist ) : void
	{
		unset(static::$_list[$pricelist->getCode()]);
	}
	
}
