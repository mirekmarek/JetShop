<?php
/**
 * @copyright Copyright (c) Miroslav Marek <mirek.marek@web-jet.cz>
 * @license EUPL 1.2  https://eupl.eu/1.2/en/
 * @author Miroslav Marek <mirek.marek@web-jet.cz>
 */
namespace JetShop;


use Jet\BaseObject;
use Jet\Form_Field;
use JetApplication\Availability;
use JetApplication\Pricelist;
use JetApplication\ProductFilter_Storage;

abstract class Core_ProductFilter_Filter_Basic_SubFilter extends BaseObject
{
	
	protected static string $key;
	
	protected Core_ProductFilter_Filter_Basic $filter;
	
	protected string $label = '';
	protected null|bool|int $filter_value = null;
	protected array $filter_result = [];
	
	protected ?array $previous_filter_result;
	
	
	public function __construct()
	{
	}
	
	abstract public function getEditField() : ?Form_Field;
	
	public function setFilter( Core_ProductFilter_Filter_Basic $filter ) : void
	{
		$this->filter = $filter;
	}
	
	public static function getKey(): string
	{
		return static::$key;
	}
	
	public function getLabel(): string
	{
		return $this->label;
	}
	
	public function setLabel( string $label ): void
	{
		$this->label = $label;
	}
	
	
	public function getFilterValue(): null|bool|int
	{
		return $this->filter_value;
	}
	
	public function setFiltervalue( null|bool|int $filter_value ): void
	{
		$this->filter_value = $filter_value;
	}
	
	public function getFilterResult(): array
	{
		return $this->filter_result;
	}
	
	
	public function save( ProductFilter_Storage $storage ) : void
	{
		if($this->filter_value===null) {
			$storage->unsetValue( $this->filter, static::getKey() );
		} else {
			$storage->setValue( $this->filter, static::getKey(), value: $this->filter_value );
		}
	}
	
	public function load( ProductFilter_Storage $storage ) : void
	{
		if(($value = $storage->getValue($this->filter, static::getKey()))!==null) {
			$this->setFiltervalue( $value );
		} else {
			$this->setFiltervalue( null );
		}
	}
	
	public function getIsActive(): bool
	{
		return $this->filter_value!==null;
	}
	
	public function getAvailability(): Availability
	{
		return $this->filter->getProductFilter()->getAvailability();
	}
	
	public function getPricelist(): Pricelist
	{
		return $this->filter->getProductFilter()->getPricelist();
	}
	
	public function setPreviousFilterResult( ?array $previous_filter_result ): void
	{
		$this->previous_filter_result = $previous_filter_result;
	}
	
	
	public function getPreviousFilterResult(): ?array
	{
		return $this->previous_filter_result;
	}
	
	
	abstract public function filter() : void;
	
	abstract public function isForCustomerUI() : bool;
	
	abstract public function itMakeSenseForCustomer() : ?bool;
	
	abstract public function getURLParam() : string;
	
}