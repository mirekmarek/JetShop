<?php
namespace JetShop;

use Jet\Form;

abstract class Core_ProductListing_Filter_Abstract {

	protected string $key = '';

	protected ProductListing $listing;
	protected Shops_Shop $shop;

	protected array|null $filtered_product_ids = null;

	public function __construct( ProductListing $listing )
	{
		$this->listing = $listing;
		$this->shop = $listing->getShop();

		$this->init();
	}

	abstract protected function init() : void;

	public function resetFilter() : void
	{
		$this->filtered_product_ids = null;
	}

	abstract public function resetCount() : void;

	public function getKey() : string
	{
		return $this->key;
	}

	abstract public function getTargetFilterEditForm(  Form $form, array &$target_filter ) : void;

	abstract public function catchTargetFilterEditForm(  Form $form, array &$target_filter ) : void;

	abstract public function initByTargetFilter( array &$target_filter ) : void;

	abstract public function getStateData( array &$state_data ) : void;

	abstract public function initByStateData( array $state_data ) : void;

	abstract public function parseFilterUrl( array &$parts ) : void;

	abstract public function generateCategoryTargetUrl( array &$parts ) : void;

	abstract public function generateUrl( array &$parts ) : void;

	abstract public function prepareFilter( array $initial_product_ids ) : void;

	abstract public function filterIsActive() : bool;

	abstract public function getFilteredProductIds() : array;

	abstract public function disableNonRelevantFilters() : void;
}