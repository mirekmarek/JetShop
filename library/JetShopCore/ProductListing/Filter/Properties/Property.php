<?php
namespace JetShop;

use Jet\Form;

abstract class Core_ProductListing_Filter_Properties_Property {

	protected ?ProductListing $listing = null;

	protected ?Parametrization_Property $property = null;

	protected bool $is_active = false;

	protected bool $is_disabled = false;

	protected bool $is_forced = false;

	public function __construct( ProductListing $listing, Parametrization_Property $property )
	{
		$this->listing = $listing;
		$this->property = $property;
	}

	public function getProperty() : Parametrization_Property
	{
		return $this->property;
	}

	public function isActive() : bool
	{
		return $this->is_active;
	}

	public function setIsActive( bool $is_active ) : void
	{
		$this->is_active = $is_active;
	}

	public function isDisabled() : bool
	{
		return $this->is_disabled;
	}

	public function setIsDisabled( bool $is_disabled ) : void
	{
		$this->is_disabled = $is_disabled;
	}

	public function isForced() : bool
	{
		return $this->is_forced;
	}

	public function setIsForced( bool $is_forced ) : void
	{
		$this->is_forced = $is_forced;
	}

	public function getListing() : ProductListing
	{
		return $this->listing;
	}

	abstract public function getTargetFilterEditForm(  Form $form, array $target_filter ) : void;

	abstract public function catchTargetFilterEditForm( Form $form, array &$target_filter ) : void;

	abstract public function initByTargetFilter( array $target_filter ) : void;

	abstract public function getStateData() : array;

	abstract public function initByStateData( array $state_data ) : void;

	abstract public function generateCategoryTargetUrlPart() : string;

	abstract public function generateUrlPart() : string;

	abstract public function parseFilterUrl( array &$parts ) : void;

	abstract public function prepareFilter( array $data_map ) : void;

	abstract public function generateFilterUrlPart() : string;

	abstract public function getFilteredProductIds() : array;

	abstract public function renderFilterItem() : string;

	abstract public function renderSelectedFilterItem() : string;

	abstract public function disableNonRelevantFilters() : void;

	abstract public function resetCount() : void;

}