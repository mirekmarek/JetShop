<?php
namespace JetShop;

use Jet\Autoloader;
use Jet\BaseObject;
use Jet\IO_Dir;
use JetApplication\Complaint;
use JetApplication\Complaint_Status;

abstract class Core_Complaint_Status extends BaseObject {
	public const CODE = null;
	
	protected string $title;
	protected int $priority;
	
	
	protected static array $flags_map = [
		'completed' => null,
		'cancelled' => null,
		
		'clarification_required' => null,
		'being_processed' => null,
		
		'rejected' => null,
		
		'accepted' => null,
		
		'money_refund' => null,
		'sent_for_repair' => null,
		'repaired' => null,
		'send_new_products' => null,
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
		$Complaint_flags = $Complaint->getFlags();
		
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
		
		$Complaint->setFlags( $flags );
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
	 * @return Complaint_Status[]
	 */
	public static function getList() : array
	{
		if(static::$list===null) {
			static::$list = [];
			
			$path = substr( Autoloader::getScriptPath( Complaint_Status::class ), 0, -4).'/';
			
			$files = IO_Dir::getFilesList( $path, '*.php' );
			
			foreach($files as $file) {
				$class_name = Complaint_Status::class.'_'.basename( $file, '.php' );
				
				static::add( new $class_name() );
			}
			
		}
		
		return static::$list;
	}
	
	public static function get( string $code ) : ?Complaint_Status
	{
		$list = Complaint_Status::getList();
		if(!isset($list[$code])) {
			return null;
		}
		
		return $list[$code];
	}
	
	protected static function add( Complaint_Status $status ) : void
	{
		static::$list[$status->getCode()] = $status;
		
		uasort( static::$list, function( Complaint_Status $a, Complaint_Status $b ) {
			return $a->getPriority() <=> $b->getPriority();
		} );
	}
	
	public static function getScope() : array
	{
		$list = Complaint_Status::getList();
		
		
		$res = [];
		
		foreach($list as $item) {
			$res[$item->getCode()] = $item->getTitle();
		}
		
		return $res;
	}
	
	
}