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
use Jet\Tr;
use JetApplication\Admin_Managers;
use JetApplication\EShopEntity_Basic;
use JetApplication\EShopEntity_Event;
use JetApplication\EShopEntity_HasStatus_Interface;
use JetApplication\EShopEntity_Status;
use JetApplication\EShopEntity_Status_PossibleFutureStatus;

abstract class Core_EShopEntity_Status extends BaseObject {
	public const CODE = null;
	
	protected static string $base_status_class;
	
	protected string $title;
	protected int $priority;
	
	
	protected static array $flags_map = [
	];
	
	protected static ?array $list = null;
	
	public static function getCode(): string
	{
		return static::CODE;
	}
	
	public function getTitle(): string
	{
		return Tr::_( $this->title, dictionary: Tr::COMMON_DICTIONARY );
	}
	
	public function getPriority(): int
	{
		return $this->priority;
	}
	
	public static function getFlagsMap(): array
	{
		return static::$flags_map;
	}
	
	
	
	
	public static function resolve( EShopEntity_HasStatus_Interface $item ) : bool
	{
		$flags = $item->getFlags();
		
		foreach(static::$flags_map as $flag => $value ) {
			if($value===null) {
				continue;
			}
			
			if($value!=$flags[$flag]) {
				return false;
			}
		}
		
		return true;
	}
	
	public static function getStatusQueryWhere() : array
	{
		return [
			'status' => static::getCode()
		];
	}
	
	public function showAdmin() : string
	{
		/**
		 * @var EShopEntity_Status $this
		 */
		return Admin_Managers::EntityEdit()->renderShowStatus( $this );
	}
	
	abstract public function getShowAdminCSSClass() : string;
	
	public function getShowAdminCSSStyle() : string
	{
		return '';
	}
	
	
	
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
	
	protected static function add( EShopEntity_Status $status ) : void
	{
		static::$list[$status->getCode()] = $status;
		
		uasort( static::$list, function( EShopEntity_Status $a, EShopEntity_Status $b ) {
			return $a->getPriority() <=> $b->getPriority();
		} );
	}
	
	public static function getScope() : array
	{
		$list = static::getList();
		
		
		$res = [];
		
		foreach($list as $item) {
			$res[$item->getCode()] = $item->getTitle();
		}
		
		return $res;
	}
	
	
	public function setupObjectAfterStatusUpdated( EShopEntity_Basic $item, array $params ) : void
	{
	
	}
	
	
	abstract public function createEvent( EShopEntity_Basic $item, EShopEntity_Status $previouse_status ): ?EShopEntity_Event;
	
	/**
	 * @return EShopEntity_Status_PossibleFutureStatus[]
	 */
	abstract public function getPossibleFutureStatuses() : array;
	
}