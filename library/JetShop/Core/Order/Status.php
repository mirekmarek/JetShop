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
use JetApplication\Order;
use JetApplication\Order_Status;

abstract class Core_Order_Status extends BaseObject {
	public const CODE = null;
	
	protected string $title;
	protected int $priority;
	
	
	protected static array $flags_map = [
		'cancelled' => null,
		'payment_required' => null,
		'paid' => null,
		'all_items_available' => null,
		'ready_for_dispatch' => null,
		'dispatch_started' => null,
		'dispatched' => null,
		'delivered' => null,
		'returned' => null,
	];
	
	protected static ?array $list = null;
	
	
	
	public static function getCode(): string
	{
		return static::CODE;
	}
	
	public function getTitle( bool $translate=true ): string
	{
		if( $translate ) {
			return Tr::_( $this->title, dictionary: 'Order.Status' );
		}
		
		return $this->title;
	}
	
	public function getPriority(): int
	{
		return $this->priority;
	}
	
	

	public static function resolve( Order $order ) : bool
	{
		$order_flags = $order->getFlags();
		
		foreach(static::$flags_map as $flag => $value ) {
			if($value===null) {
				continue;
			}
			
			if(
				$flag=='paid' &&
				$value===true &&
				!$order_flags['payment_required']
			) {
				continue;
			}
			
			if($value!=$order_flags[$flag]) {
				return false;
			}
		}
		
		return true;
	}
	
	public function setStatus( Order $order ) : void
	{
		$flags = [];
		
		foreach(static::$flags_map as $flag => $value ) {
			if($value===null) {
				continue;
			}
			
			$flags[$flag] = $value;
		}
		
		$order->setFlags( $flags );
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
			
			if(
				$flag=='paid' &&
				$value===true
			) {
				$where[] = [
					'payment_required' => false,
					'OR',
					'paid' => true
				];
				
				continue;
			}
			
			$where[$flag] = $value;
		}
		
		return $where;
	}
	
	
	/**
	 * @return Order_Status[]
	 */
	public static function getList() : array
	{
		if(static::$list===null) {
			static::$list = [];
			
			$path = substr( Autoloader::getScriptPath( Order_Status::class ), 0, -4).'/';
			
			$files = IO_Dir::getFilesList( $path, '*.php' );
			
			foreach($files as $file) {
				$class_name = Order_Status::class.'_'.basename( $file, '.php' );
				
				static::add( new $class_name() );
			}
			
		}
		
		return static::$list;
	}
	
	public static function get( string $code ) : ?Order_Status
	{
		$list = Order_Status::getList();
		if(!isset($list[$code])) {
			return null;
		}
		
		return $list[$code];
	}
	
	protected static function add( Order_Status $status ) : void
	{
		static::$list[$status->getCode()] = $status;
		
		uasort( static::$list, function( Order_Status $a, Order_Status $b ) {
			return $a->getPriority() <=> $b->getPriority();
		} );
	}
	
	public static function getScope() : array
	{
		$list = Order_Status::getList();
		
		
		$res = [];
		
		foreach($list as $item) {
			$res[$item->getCode()] = $item->getTitle();
		}
		
		return $res;
	}
	
	
}