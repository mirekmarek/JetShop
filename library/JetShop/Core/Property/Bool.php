<?php
namespace JetShop;

use JetApplication\ProductListing;
use JetApplication\Property;
use JetApplication\Property_Bool_Value;
use JetApplication\Property_Bool_Filter;

abstract class Core_Property_Bool extends Property
{
	protected string $type = Property::PROPERTY_TYPE_BOOL;


	public function getValueInstance() : Property_Bool_Value
	{
		return new Property_Bool_Value( $this );
	}
	
	public function initFilter( ProductListing $listing ): void
	{
		$this->filter = new Property_Bool_Filter( $listing, $this );
	}
	
	public function filter() : Property_Bool_Filter
	{
		/** @noinspection PhpIncompatibleReturnTypeInspection */
		return $this->filter;
	}

}