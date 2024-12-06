<?php
namespace JetShop;

use Jet\Form_Definition_Trait;
use Jet\IO_File;
use JetApplication\EShopConfig;
use JetApplication\MeasureUnit;


abstract class Core_MeasureUnits
{
	use Form_Definition_Trait;
	
	/**
	 * @var MeasureUnit[]
	 */
	protected static ?array $list = null;
	
	public static function getCfgFilePath() : string
	{
		return EShopConfig::getRootDir().'measure_units.php';
	}
	
	public static function loadCfg() : void
	{
		static::$list = [];
		
		if( IO_File::exists(static::getCfgFilePath()) ) {
			$cfg = require static::getCfgFilePath();
			
			foreach($cfg as $item) {
				static::add( (new MeasureUnit( $item )) );
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
	
	
	public static function add( MeasureUnit $unit ) : void
	{
		static::$list[$unit->getCode()] = $unit;
	}
	
	public static function remove( string $code ) : void
	{
		if(isset(static::$list[$code])) {
			unset( static::$list[$code] );
		}
	}
	
	public static function get( string $code ) : ?MeasureUnit
	{
		static::getList();
		
		return static::$list[$code]??null;
	}
	
	public static function getScope() : array
	{
		static::getList();
		$res = [];
		foreach(static::$list as $unit) {
			$res[$unit->getCode()] = $unit->getName();
		}
		
		return $res;
	}
	
	/**
	 * @return MeasureUnit[]
	 */
	public static function getList() : array
	{
		if(static::$list===null) {
			static::loadCfg();
		}
		
		return static::$list;
	}
}