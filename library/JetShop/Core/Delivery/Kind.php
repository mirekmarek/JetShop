<?php
/**
 * @copyright Copyright (c) Miroslav Marek <mirek.marek@web-jet.cz>
 * @license EUPL 1.2  https://eupl.eu/1.2/en/
 * @author Miroslav Marek <mirek.marek@web-jet.cz>
 */
namespace JetShop;



use Jet\Autoloader;
use Jet\IO_Dir;
use Jet\Tr;

use JetApplication\Delivery_Kind;

abstract class Core_Delivery_Kind {

	protected string $title;
	protected int $priority;
	
	protected bool $module_is_required = false;
	
	protected bool $is_edelivery = true;
	protected bool $is_personal_takeover = false;
	protected bool $is_personal_takeover_internal = false;
	
	
	public static function getCode() : string
	{
		return static::CODE;
	}
	
	public function moduleIsRequired(): bool
	{
		return $this->module_is_required;
	}
	
	public function isPersonalTakeover(): bool
	{
		return $this->is_personal_takeover;
	}
	
	public function isPersonalTakeoverInternal(): bool
	{
		return $this->is_personal_takeover_internal;
	}
	
	public function isPersonalTakeoverExternal(): bool
	{
		return
			$this->is_personal_takeover &&
			!$this->is_personal_takeover_internal;
	}
	
	
	
	public function isEDelivery(): bool
	{
		return $this->is_edelivery;
	}
	
	public function getPriority(): int
	{
		return $this->priority;
	}

	


	/**
	 * @var Delivery_Kind[]|null
	 */
	protected static ?array $list = null;

	public static function get( string $code ) : ?Delivery_Kind
	{
		return Delivery_Kind::getList()[$code]??null;
	}


	/**
	 * @return string
	 */
	public function getTitle(): string
	{
		return Tr::_( $this->title, dictionary: Tr::COMMON_DICTIONARY );
	}

	/**
	 * @return Delivery_Kind[]
	 */
	public static function getList() : array
	{
		if(static::$list===null) {
			static::$list = [];
			
			$path = substr( Autoloader::getScriptPath( Delivery_Kind::class ), 0, -4).'/';
			
			$files = IO_Dir::getFilesList( $path, '*.php' );
			
			foreach($files as $file) {
				$class_name = Delivery_Kind::class.'_'.basename( $file, '.php' );
				
				static::add( new $class_name() );
			}
		}

		return static::$list;
	}
	
	public static function add( Delivery_Kind $kind ) : void
	{
		static::$list[$kind::getCode()] = $kind;
		
		uasort( static::$list, function( Delivery_Kind $a, Delivery_Kind $b ) {
			return $a->getPriority() <=> $b->getPriority();
		} );
	}

	public static function getScope() : array
	{
		$list = Delivery_Kind::getList();


		$res = [];

		foreach($list as $item) {
			$res[$item::getCode()] = $item->getTitle();
		}

		return $res;
	}
}