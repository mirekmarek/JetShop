<?php
namespace JetShop;

use JetApplication\ProductListing;
use JetApplication\Property_Options;
use JetApplication\Property_Options_Option;



abstract class Core_Property_Options_Option_Filter
{
	protected ProductListing $listing;
	
	protected Property_Options $property;
	
	protected Property_Options_Option $option;
	
	protected array $product_ids = [];
	
	protected bool $is_selected = false;
	
	protected bool $is_disabled = false;
	
	protected int|null $_count = null;
	
	public function __construct(
		ProductListing $listing,
		Property_Options $property,
		Property_Options_Option $option
	) {
		$this->listing = $listing;
		$this->property = $property;
		$this->option = $option;
	}
	
	public function getProperty() : Property_Options
	{
		return $this->property;
	}
	
	public function getOption() : Property_Options_Option
	{
		return $this->option;
	}
	
	public function getProductIds() : array
	{
		return $this->product_ids;
	}
	
	public function setProductIds( array $product_ids ) : void
	{
		$this->product_ids = $product_ids;
	}
	
	public function isSelected() : bool
	{
		return $this->is_selected;
	}
	
	public function setIsSelected( bool $is_selected ) : void
	{
		$this->is_selected = $is_selected;
	}
	
	public function isDisabled() : bool
	{
		return $this->is_disabled;
	}
	
	public function setIsDisabled( bool $is_disabled ) : void
	{
		$this->is_disabled = $is_disabled;
	}
	
	public function resetCount() : void
	{
		$this->_count = null;
	}
	
	public function getCount() : int
	{
		if($this->_count===null)
		{
			$property_id = $this->property->getId();
			$initial_product_ids = $this->product_ids;
			
			
			$ids = $this->listing->properties()->internalGetFilteredProductIdsWithoutProperty(
				$initial_product_ids,
				$property_id
			);
			
			
			$this->_count = count( $ids );
		}
		
		return $this->_count;
	}
	
	public function generateURLWithout() : string
	{
		$active = $this->is_selected;
		
		$this->is_selected = false;
		
		$url = $this->listing->generateUrl();
		
		$this->is_selected = $active;
		
		return $url;
	}
	
	public function generateURLWith() : string
	{
		$active = $this->is_selected;
		
		$this->is_selected = true;
		
		$url = $this->listing->generateUrl();
		
		$this->is_selected = $active;
		
		return $url;
	}
	
}
