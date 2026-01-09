<?php
/**
 * @copyright Copyright (c) Miroslav Marek <mirek.marek@web-jet.cz>
 * @license EUPL 1.2  https://eupl.eu/1.2/en/
 * @author Miroslav Marek <mirek.marek@web-jet.cz>
 */
namespace JetShop;


use Jet\Autoloader;
use Jet\BaseObject;
use Jet\IO_Dir;
use JetApplication\EShopEntity_HasStatus_Interface;
use JetApplication\EShopEntity_Status_PossibleFutureStatus;
use JetApplication\EShopEntity_VirtualStatus;
use Closure;

abstract class Core_EShopEntity_VirtualStatus extends BaseObject {
	public const CODE = null;
	
	protected static string $base_status_class;
	
	protected static ?array $list = null;
	
	
	
	public static function getCode(): string
	{
		return static::CODE;
	}
	
	abstract public function getTitle(): string;
	
	abstract public static function handle(
		EShopEntity_HasStatus_Interface $item,
		bool $handle_event=true,
		array $params=[],
		?Closure $event_setup=null
	) : void;
	
	
	/**
	 * @return static[]
	 */
	public static function getList() : array
	{
		if(static::$list===null) {
			static::$list = [];
			
			$path = substr( Autoloader::getScriptPath( static::$base_status_class ), 0, -4).'/';
			
			$files = IO_Dir::getFilesList( $path, '*.php' );
			
			foreach($files as $file) {
				$class_name = static::$base_status_class.'_'.basename( $file, '.php' );
				
				static::add( new $class_name() );
			}
			
		}
		
		return static::$list;
	}
	
	public static function get( string $code='' ) : ?static
	{
		if(!$code) {
			$code = static::getCode();
		}
		
		$list = static::getList();
		if(!isset($list[$code])) {
			return null;
		}
		
		return $list[$code];
	}
	
	protected static function add( EShopEntity_VirtualStatus $status ) : void
	{
		static::$list[$status->getCode()] = $status;
	}
	
	abstract public static function getAsPossibleFutureStatus() : ?EShopEntity_Status_PossibleFutureStatus;
	
}