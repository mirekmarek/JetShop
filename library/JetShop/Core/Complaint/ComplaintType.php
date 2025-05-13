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
use Jet\Locale;
use Jet\Tr;
use JetApplication\Complaint_ComplaintType;

abstract class Core_Complaint_ComplaintType extends BaseObject {
	public const CODE = null;
	
	protected static string $base_status_class= Complaint_ComplaintType::class;
	
	protected string $title;
	protected int $priority;
	
	protected static ?array $list = null;
	
	public static function getCode(): string
	{
		return static::CODE;
	}
	
	public function getTitle( ?Locale $locale=null ): string
	{
		$locale = $locale??Locale::getCurrentLocale();
		return Tr::_( $this->title, dictionary: Tr::COMMON_DICTIONARY, locale: $locale );
	}
	
	public function getPriority(): int
	{
		return $this->priority;
	}
	
	public function showAdmin() : string
	{
		return $this->getTitle();
	}
	
	public function getShowAdminCSSClass() : string
	{
		return '';
	}
	
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
	
	public static function get( string $code ) : ?static
	{
		return static::getList()[$code]??null;
	}
	
	protected static function add( Complaint_ComplaintType $status ) : void
	{
		static::$list[$status->getCode()] = $status;
		
		uasort( static::$list, function( Complaint_ComplaintType $a, Complaint_ComplaintType $b ) {
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
	
	
}