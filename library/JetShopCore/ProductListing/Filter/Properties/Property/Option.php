<?php
namespace JetShop;


abstract class Core_ProductListing_Filter_Properties_Property_Option
{
	protected ProductListing_Filter_Properties_Property_Options|null $filter_property = null;

	protected Property_Options_Option|null $option = null;

	protected array $product_ids = [];

	protected bool $is_active = false;

	protected bool $is_forced = false;

	protected bool $is_disabled = false;

	protected int|null $_count = null;

	public function __construct(
		ProductListing_Filter_Properties_Property_Options $filter_property,
		Property_Options_Option $option
	) {
		$this->filter_property = $filter_property;
		$this->option = $option;
	}

	public function getFilterProperty() : ProductListing_Filter_Properties_Property_Options
	{
		return $this->filter_property;
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

	public function isActive() : bool
	{
		return $this->is_active;
	}

	public function setIsActive( bool $is_active ) : void
	{
		$this->is_active = $is_active;
	}

	public function isForced() : bool
	{
		return $this->is_forced;
	}

	public function setIsForced( bool $is_forced ) : void
	{
		$this->is_forced = $is_forced;
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
			$property_id = $this->filter_property->getProperty()->getId();
			$initial_product_ids = $this->product_ids;


			$ids = $this->filter_property->getListing()->params()->internalGetFilteredProductIdsWithoutProperty(
				$initial_product_ids,
				$property_id
			);


			$this->_count = count( $ids );
		}

		return $this->_count;
	}

	public function generateURLWithout() : string
	{
		$active = $this->is_active;

		$this->is_active = false;

		$url = $this->filter_property->getListing()->generateUrl();

		$this->is_active = $active;

		return $url;
	}

	public function generateURLWith() : string
	{
		$active = $this->is_active;

		$this->is_active = true;

		$url = $this->filter_property->getListing()->generateUrl();

		$this->is_active = $active;

		return $url;
	}

}