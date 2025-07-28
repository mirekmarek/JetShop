<?php
/**
 * @copyright Copyright (c) Miroslav Marek <mirek.marek@web-jet.cz>
 * @license EUPL 1.2  https://eupl.eu/1.2/en/
 * @author Miroslav Marek <mirek.marek@web-jet.cz>
 */
namespace JetShop;


use Jet\IO_File;
use JetApplication\Currencies;
use JetApplication\EShopConfig;
use JetApplication\Pricelist;


abstract class Core_Pricelists {
	
	/**
	 * @var Pricelist[]|null
	 */
	protected static ?array $_list = null;
	protected static ?Pricelist $current = null;
	
	public static function getCfgFilePath() : string
	{
		return EShopConfig::getRootDir().'pricelists.php';
	}
	
	public static function loadCfg() : void
	{
		static::$_list = [];
		
		if(IO_File::exists(static::getCfgFilePath())) {
			$cfg = require static::getCfgFilePath();
			
			foreach($cfg as $item) {
				static::addPricelist( (new Pricelist( $item )) );
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
	
	/**
	 * @return Pricelist
	 */
	public static function getCurrent() : Pricelist
	{
		return static::$current;
	}

	public static function setCurrent( Pricelist $pricelist ) : void
	{
		static::$current = $pricelist;
		
		Currencies::setCurrent( $pricelist->getCurrency() );
		
	}

	public static function exists( string $code ) : bool
	{
		return isset(static::getList()[$code]);
	}

	public static function get( string $code ) : Pricelist|null
	{
		return static::getList()[$code]??null;
	}


	/**
	 * @return Pricelist[]
	 */
	public static function getList() : array
	{
		if(static::$_list===null) {
			static::loadCfg();
			
			uasort( static::$_list, function( Pricelist $a, Pricelist $b ) {
				return strcmp( $a->getName(), $b->getName() );
			} );
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
	
	public static function addPricelist( Pricelist $pricelist ) : void
	{
		static::$_list[$pricelist->getCode()] = $pricelist;
	}
	
	public static function removePricelist( Pricelist $pricelist ) : void
	{
		unset(static::$_list[$pricelist->getCode()]);
	}
	
}
