<?php
namespace JetShop;

use Jet\Autoloader;
use Jet\BaseObject;
use Jet\IO_Dir;
use JetApplication\Complaint;
use JetApplication\Complaint_DispatchStatus;

abstract class Core_Complaint_DispatchStatus extends BaseObject {
	public const CODE = null;
	
	protected string $title;
	protected int $priority;
	
	
	protected static array $flags_map = [

		'ready_for_dispatch' => null,
		'dispatch_started' => null,
		
		'dispatched' => null,
		'delivered' => null,
		'returned' => null,
	];
	
	protected static ?array $list = null;
	
	
	
	public function getCode(): string
	{
		return static::CODE;
	}
	
	public function getTitle(): string
	{
		return $this->title;
	}
	
	public function getPriority(): int
	{
		return $this->priority;
	}
	
	
	
	public static function resolve( Complaint $Complaint ) : bool
	{
		$Complaint_flags = $Complaint->getDispatchFlags();
		
		foreach(static::$flags_map as $flag => $value ) {
			if($value===null) {
				continue;
			}
			
			if($value!=$Complaint_flags[$flag]) {
				return false;
			}
		}
		
		return true;
	}
	
	public function setStatus( Complaint $Complaint ) : void
	{
		$flags = [];
		
		foreach(static::$flags_map as $flag => $value ) {
			if($value===null) {
				continue;
			}
			
			$flags[$flag] = $value;
		}
		
		$Complaint->setDispatchFlags( $flags );
	}
	
	public static function getStatusQueryWhere() : array
	{
		$where = [];
		foreach(static::$flags_map as $flag => $value ) {
			if($value===null) {
				continue;
			}
			
			if($where) {
				$where[] = 'AND';
			}
			
			$where[$flag] = $value;
		}
		
		return $where;
	}
	
	
	/**
	 * @return Complaint_DispatchStatus[]
	 */
	public static function getList() : array
	{
		if(static::$list===null) {
			static::$list = [];
			
			$path = substr( Autoloader::getScriptPath( Complaint_DispatchStatus::class ), 0, -4).'/';
			
			$files = IO_Dir::getFilesList( $path, '*.php' );
			
			foreach($files as $file) {
				$class_name = Complaint_DispatchStatus::class.'_'.basename( $file, '.php' );
				
				static::add( new $class_name() );
			}
			
		}
		
		return static::$list;
	}
	
	public static function get( string $code ) : ?Complaint_DispatchStatus
	{
		$list = Complaint_DispatchStatus::getList();
		if(!isset($list[$code])) {
			return null;
		}
		
		return $list[$code];
	}
	
	protected static function add( Complaint_DispatchStatus $status ) : void
	{
		static::$list[$status->getCode()] = $status;
		
		uasort( static::$list, function( Complaint_DispatchStatus $a, Complaint_DispatchStatus $b ) {
			return $a->getPriority() <=> $b->getPriority();
		} );
	}
	
	public static function getScope() : array
	{
		$list = Complaint_DispatchStatus::getList();
		
		
		$res = [];
		
		foreach($list as $item) {
			$res[$item->getCode()] = $item->getTitle();
		}
		
		return $res;
	}
	
	
}