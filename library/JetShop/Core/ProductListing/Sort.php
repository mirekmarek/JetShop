<?php
namespace JetShop;

use JetApplication\ProductListing;
use JetApplication\Shops_Shop;
use JetApplication\ProductListing_Sort_Option;

abstract class Core_ProductListing_Sort {
	public const CACHE_KEY = 'sort';

	protected ProductListing $listing;
	protected Shops_Shop $shop;

	/**
	 * @var ProductListing_Sort_Option[]
	 */
	protected array|null $sort_options = null;

	public function __construct( ProductListing $listing )
	{
		$this->listing = $listing;
		$this->shop = $listing->getShop();

		$this->init();

		$has_some_active = false;
		
		foreach($this->sort_options as $so) {
			if( $so->isDefault() ) {
				$so->setIsActive(true);
				$has_some_active = true;
			}
		}

		if(!$has_some_active) {
			foreach($this->sort_options as $so) {
				$so->setIsActive(true);

				break;
			}
		}
	}

	abstract protected function init() : void;

	abstract public function prepareFilter( array $initial_product_ids ) : void;

	abstract public function getSortUrlParam() : string;

	
	public function setSelectedSort( string $option_id ) : void
	{
		if(!isset($this->sort_options[$option_id])) {
			return;
		}

		foreach($this->sort_options as $id=>$option) {
			$option->setIsActive( ($id==$option_id) );
		}
	}

	public function getStateData( array &$state_data ) : void
	{
		$selected = '';

		foreach($this->sort_options as $option) {
			if($option->isActive()) {
				$selected = $option->getId();

				break;
			}
		}

		$state_data['selected_sort'] = $selected;

	}

	public function initByStateData( array $state_data ) : void
	{
		if(isset($state_data['selected_sort'])) {
			$this->setSelectedSort( $state_data['selected_sort'] );
		}
	}

	public function generateUrl( array &$parts ) : void
	{
		foreach($this->sort_options as $sort_option) {
			if( $sort_option->isActive() ) {
				if(!$sort_option->isDefault()) {
					$parts[] = $this->getSortUrlParam().'_'.$sort_option->getUrlParam();
				}

				break;
			}
		}
	}

	public function parseFilterUrl( array &$parts ) : void
	{
		$prefix = $this->getSortUrlParam().'_';

		foreach($parts as $i=>$part) {
			if(stripos($part, $prefix)===0) {
				unset($parts[$i]);

				$sort_param = explode('_', $part)[1];


				foreach( $this->sort_options as $sort_option ) {
					$sort_option->setIsActive( ($sort_option->getUrlParam()==$sort_param) );
				}

			}
		}
	}

	public function sort( array $filtered_product_ids ) : array
	{
		foreach($this->sort_options as $sort_option) {
			if( $sort_option->isActive() ) {
				$sorter = $sort_option->getSorter();

				return $sorter( $filtered_product_ids );
			}
		}

		return $filtered_product_ids;
	}

	/**
	 * @return ProductListing_Sort_Option[]
	 */
	public function getSortOptions() : array
	{
		return $this->sort_options;
	}

}