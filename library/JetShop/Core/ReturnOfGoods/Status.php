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
use JetApplication\ReturnOfGoods;
use JetApplication\ReturnOfGoods_Status;

abstract class Core_ReturnOfGoods_Status extends BaseObject {
	public const CODE = null;
	
	protected string $title;
	protected int $priority;
	
	
	protected static array $flags_map = [
		'cancelled' => null,
		
		'completed' => null,
		'clarification_required' => null,
		'being_processed' => null,
		
		'rejected' => null,
		
		'accepted' => null,
		
		'money_refund' => null,
	];
	
	protected static ?array $list = null;
	
	
	
	public static function getCode(): string
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
	
	
	
	public static function resolve( ReturnOfGoods $return_of_goods ) : bool
	{
		$return_of_goods_flags = $return_of_goods->getFlags();
		
		foreach(static::$flags_map as $flag => $value ) {
			if($value===null) {
				continue;
			}
			
			if($value!=$return_of_goods_flags[$flag]) {
				return false;
			}
		}
		
		return true;
	}
	
	public function setStatus( ReturnOfGoods $return_of_goods ) : void
	{
		$flags = [];
		
		foreach(static::$flags_map as $flag => $value ) {
			if($value===null) {
				continue;
			}
			
			$flags[$flag] = $value;
		}
		
		$return_of_goods->setFlags( $flags );
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
	 * @return ReturnOfGoods_Status[]
	 */
	public static function getList() : array
	{
		if(static::$list===null) {
			static::$list = [];
			
			$path = substr( Autoloader::getScriptPath( ReturnOfGoods_Status::class ), 0, -4).'/';
			
			$files = IO_Dir::getFilesList( $path, '*.php' );
			
			foreach($files as $file) {
				$class_name = ReturnOfGoods_Status::class.'_'.basename( $file, '.php' );
				
				static::add( new $class_name() );
			}
			
		}
		
		return static::$list;
	}
	
	public static function get( string $code ) : ?ReturnOfGoods_Status
	{
		$list = ReturnOfGoods_Status::getList();
		if(!isset($list[$code])) {
			return null;
		}
		
		return $list[$code];
	}
	
	protected static function add( ReturnOfGoods_Status $status ) : void
	{
		static::$list[$status->getCode()] = $status;
		
		uasort( static::$list, function( ReturnOfGoods_Status $a, ReturnOfGoods_Status $b ) {
			return $a->getPriority() <=> $b->getPriority();
		} );
	}
	
	public static function getScope() : array
	{
		$list = ReturnOfGoods_Status::getList();
		
		
		$res = [];
		
		foreach($list as $item) {
			$res[$item->getCode()] = $item->getTitle();
		}
		
		return $res;
	}
	
	
}