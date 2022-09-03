<?php
namespace JetShop;

abstract class Core_ProductListing_Filter_Brands_Brand {

	protected ?ProductListing $listing = null;

	protected ?Brand $brand = null;

	protected bool $is_active = false;

	protected bool $is_disabled = false;

	protected array $product_ids = [];

	protected ?int $_count = null;

	public function __construct( ProductListing $listing, Brand $brand )
	{
		$this->listing = $listing;
		$this->brand = $brand;
	}

	public function getBrand(): Brand
	{
		return $this->brand;
	}

	public function isActive(): bool
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

	public function getProductIds() : array
	{
		return $this->product_ids;
	}

	public function setProductIds( array $product_ids ) : void
	{
		$this->product_ids = $product_ids;
	}

	public function resetCount() : void
	{
		$this->_count = null;
	}

	public function getCount() : int
	{
		if($this->_count===null) {
			$ids = $this->listing->internalGetFilteredProductIds($this->product_ids, 'brands');

			$this->_count = count($ids);
		}

		return $this->_count;
	}

	public function generateURLWithout() : string
	{
		$active = $this->is_active;

		$this->is_active = false;

		$url = $this->listing->generateUrl();

		$this->is_active = $active;

		return $url;
	}

	public function generateURLWith() : string
	{
		$active = $this->is_active;

		$this->is_active = true;

		$url = $this->listing->generateUrl();

		$this->is_active = $active;

		return $url;
	}

}