<?php
namespace JetShop;

use Jet\IO_File;
use JetApplication\Availability;
use JetApplication\EShopConfig;


abstract class Core_Availabilities {
	
	/**
	 * @var Availability[]|null
	 */
	protected static ?array $_list = null;
	protected static ?Availability $current = null;
	
	public static function getCfgFilePath() : string
	{
		return EShopConfig::getRootDir().'availabilities.php';
	}
	
	public static function loadCfg() : void
	{
		static::$_list = [];
		
		if(IO_File::exists(static::getCfgFilePath())) {
			$cfg = require static::getCfgFilePath();
			
			foreach($cfg as $item) {
				static::addAvailability( (new Availability( $item )) );
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
	
	
	public static function getCurrent() : Availability
	{
		return static::$current;
	}
	
	public static function setCurrent( Availability $eshop ) : void
	{
		static::$current = $eshop;
	}
	
	public static function exists( string $key ) : bool
	{
		return isset(static::getList()[$key]);
	}
	
	public static function get( string $code ) : Availability|null
	{
		return static::getList()[$code]??null;
	}
	
	public static function addAvailability( Availability $availability ) : void
	{
		static::$_list[$availability->getCode()] = $availability;
	}
	
	public static function removeAvailability( Availability $availability ) : void
	{
		unset(static::$_list[$availability->getCode()]);
	}
	
	/**
	 * @return Availability[]
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
	
}
