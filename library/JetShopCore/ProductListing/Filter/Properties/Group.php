<?php
namespace JetShop;


abstract class Core_ProductListing_Filter_Properties_Group {

	protected ?ProductListing $listing = null;

	protected ?Parametrization_Group $group = null;

	protected bool $is_disabled = false;

	protected bool $is_forced = false;

	public function __construct( ProductListing $listing, Parametrization_Group $group )
	{
		$this->listing = $listing;
		$this->group = $group;
	}

	public function getGroup() : Parametrization_Group
	{
		return $this->group;
	}

	public function isDisabled() : bool
	{
		return $this->is_disabled;
	}

	public function setIsDisabled( bool $is_disabled ) : void
	{
		$this->is_disabled = $is_disabled;
	}


	/**
	 * @return ProductListing_Filter_Properties_Property[]
	 */
	public function getProperties() : array
	{
		return $this->listing->properties()->getProperties( $this->group->getId() );
	}

	public function disableNonRelevantFilters() : void
	{
		$disabled = true;

		foreach($this->getProperties() as $property) {
			if(!$property->isDisabled()) {
				$disabled = false;

				break;
			}
		}

		$this->setIsDisabled($disabled);
	}
}