<?php
namespace JetShop;

use JetApplication\Property;
use JetApplication\Property_Options_Option;
use JetApplication\Property_Options_Value;
use JetApplication\Property_Options_Filter;
use JetApplication\ProductListing;

abstract class Core_Property_Options extends Property
{
	protected string $type = Property::PROPERTY_TYPE_OPTIONS;
	
	/**
	 * @var Property_Options_Option[]
	 */
	protected ?array $options = null;


	public function getValueInstance() : Property_Options_Value
	{
		return new Property_Options_Value( $this );
	}
	
	public function initFilter( ProductListing $listing ): void
	{
		$this->filter = new Property_Options_Filter( $listing, $this );
		
		foreach($this->options as $option) {
			$option->initFilter( $listing, $this );
		}
	}
	
	public function filter() : Property_Options_Filter
	{
		/** @noinspection PhpIncompatibleReturnTypeInspection */
		return $this->filter;
	}
	
	/**
	 * @return Property_Options_Option[]
	 */
	public function getOptions() : array
	{
		if($this->options===null) {
			$this->options = Property_Options_Option::getListForProperty( $this->id );
		}
		
		return $this->options;
	}
	

}