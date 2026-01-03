<?php
/**
 * @copyright Copyright (c) Miroslav Marek <mirek.marek@web-jet.cz>
 * @license EUPL 1.2  https://eupl.eu/1.2/en/
 * @author Miroslav Marek <mirek.marek@web-jet.cz>
 */
namespace JetApplicationModule\Admin\Stats\Orders;


use Jet\BaseObject;
use JetApplication\Currency;
use JetApplication\EShops;

abstract class Source extends BaseObject
{
	protected static ?array $sources = null;
	
	protected string $key;
	protected string $title;
	protected Currency $output_currency;
	
	public function getKey(): string
	{
		return $this->key;
	}
	
	public function getTitle() : string
	{
		return $this->title;
	}
	
	public function getOutputCurrency(): Currency
	{
		return $this->output_currency;
	}
	
	
	
	abstract public function getWhere() : array;
	
	protected static function addSource( Source $source ) : void
	{
		static::$sources[$source->getKey()] = $source;
	}
	
	/**
	 * @return array<string,static>
	 */
	public static function getList() : array
	{
		if(static::$sources===null) {
			static::$sources = [];
			
			static::addSource( new Source_All() );
			
			foreach(EShops::getList() as $eshop) {
				static::addSource( new Source_CommonEShop($eshop) );
			}
			
		}
		
		return static::$sources;
	}
}