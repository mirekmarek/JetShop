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
use JetApplication\MoneyRefund;
use JetApplication\MoneyRefund_Status;

abstract class Core_MoneyRefund_Status extends BaseObject {
	public const CODE = null;
	
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
		return $this->title;
	}
	
	public function getPriority(): int
	{
		return $this->priority;
	}
	
	
	
	public static function resolve( MoneyRefund $money_refund ) : bool
	{
		
		return ($money_refund->getStatusCode()==static::getCode());
	}
	
	public function setStatus( MoneyRefund $money_refund ) : void
	{
		$money_refund->setStatusCode(static::getCode());
	}
	
	public static function getStatusQueryWhere() : array
	{
		return [
			'status_code' => static::getCode()
		];
	}
	
	
	/**
	 * @return MoneyRefund_Status[]
	 */
	public static function getList() : array
	{
		if(static::$list===null) {
			static::$list = [];
			
			$path = substr( Autoloader::getScriptPath( MoneyRefund_Status::class ), 0, -4).'/';
			
			$files = IO_Dir::getFilesList( $path, '*.php' );
			
			foreach($files as $file) {
				$class_name = MoneyRefund_Status::class.'_'.basename( $file, '.php' );
				
				static::add( new $class_name() );
			}
			
		}
		
		return static::$list;
	}
	
	public static function get( string $code ) : ?MoneyRefund_Status
	{
		$list = MoneyRefund_Status::getList();
		if(!isset($list[$code])) {
			return null;
		}
		
		return $list[$code];
	}
	
	protected static function add( MoneyRefund_Status $status ) : void
	{
		static::$list[$status->getCode()] = $status;
		
		uasort( static::$list, function( MoneyRefund_Status $a, MoneyRefund_Status $b ) {
			return $a->getPriority() <=> $b->getPriority();
		} );
	}
	
	public static function getScope() : array
	{
		$list = MoneyRefund_Status::getList();
		
		
		$res = [];
		
		foreach($list as $item) {
			$res[$item->getCode()] = $item->getTitle();
		}
		
		return $res;
	}
	
	
}