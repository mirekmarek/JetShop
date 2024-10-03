<?php
namespace JetShop;

use Jet\IO_File;
use Jet\SysConf_Path;
use JetApplication\Availabilities_Availability;


abstract class Core_Availabilities {
	
	/**
	 * @var Availabilities_Availability[]|null
	 */
	protected static ?array $_list = null;
	protected static ?Availabilities_Availability $current = null;
	
	public static function getCfgFilePath() : string
	{
		return SysConf_Path::getConfig().'shop/availabilities.php';
	}
	
	public static function loadCfg() : void
	{
		static::$_list = [];
		
		$cfg = require static::getCfgFilePath();
		
		foreach($cfg as $item) {
			static::addAvailability( (new Availabilities_Availability( $item )) );
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
	
	
	public static function getCurrent() : Availabilities_Availability
	{
		return static::$current;
	}
	
	public static function setCurrent( Availabilities_Availability $shop ) : void
	{
		static::$current = $shop;
	}
	
	public static function exists( string $key ) : bool
	{
		return isset(static::getList()[$key]);
	}
	
	public static function get( string $code ) : Availabilities_Availability|null
	{
		return static::getList()[$code]??null;
	}
	
	public static function addAvailability( Availabilities_Availability $availability ) : void
	{
		static::$_list[$availability->getCode()] = $availability;
	}
	
	public static function removeAvailability( Availabilities_Availability $availability ) : void
	{
		unset(static::$_list[$availability->getCode()]);
	}
	
	/**
	 * @return Availabilities_Availability[]
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
