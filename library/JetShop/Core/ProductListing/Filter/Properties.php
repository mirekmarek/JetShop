<?php
namespace JetShop;
use Jet\Form;

use JetApplication\ProductListing_Filter;
use JetApplication\Property_Filter;
use JetApplication\Product_Parameter;
use JetApplication\ProductListing_Filter_Properties;


abstract class Core_ProductListing_Filter_Properties extends ProductListing_Filter {
	public const  CACHE_KEY = 'f_param';

	protected string $key = 'properties';

	/**
	 * @var Property_Filter[]
	 */
	protected array $property_filters;

	protected array|null $filtered_product_ids = null;

	protected function init() : void
	{
	}
	
	
	public function initProductListing() : void
	{
		$this->property_filters = [];
		
		if(
			($category = $this->listing->getCategory()) &&
			($kind = $category->getKindOfProduct())
		) {
			foreach($kind->getFilterGroups() as $g ) {
				$group = $g->getGroup();
				if(
					!$group->isActive() ||
					!$group->getShopData()->isActive()
				) {
					continue;
				}
				
				foreach($g->getProperties() as $p) {
					$property = $p->getProperty();
					
					if(
						!$property->isActive() ||
						!$property->getShopData()->isActive()
					) {
						continue;
					}
					
                    $property->initFilter( $this->listing );
					
					$this->property_filters[$property->getId()] = $property->filter();
				}
				
			}
		}
	}
	
	public function initAutoAppendFilter( array &$target_filter ) : void
	{
		$this->property_filters = [];
		
		if(
			($category = $this->listing->getCategory()) &&
			($kind = $category->getKindOfProduct())
		) {
			foreach( $kind->getAllPropertyIds() as $property) {
				$property->initFilter( $this->listing );
				
				$this->property_filters[$property->getId()] = $property->filter();
			}
		}
		
		foreach( $this->property_filters as $property) {
			$property->initAutoAppendFilter( $target_filter );
		}
	}
	
	

	public function getAutoAppendProductFilterEditForm( Form $form ) : void
	{
		foreach( $this->property_filters as $property) {
			$property->getAutoAppendProductFilterEditForm( $form );
		}
	}

	public function catchAutoAppendFilterEditForm( Form $form, &$target_filter ) : void
	{
		$target_filter['properties'] = [];
		
		foreach( $this->property_filters as $property) {
			$property->catchAutoAppendFilterEditForm( $form, $target_filter );
		}
	}

	
	
	
	
	public function getStateData( &$state_data ) : void
	{
		$properties = [];
		foreach( $this->property_filters as $id=> $filter ) {

			$properties[$id] = $filter->getStateData();
		}

		$state_data['properties'] = $properties;
	}

	public function initByStateData( array $state_data ) : void
	{
		$properties = $state_data['properties'];

		foreach( $this->property_filters as $id=> $filter ) {
			if(
				!isset($properties[$id]) ||
				!is_array($properties[$id])
			) {
				continue;
			}

			$filter->initByStateData( $properties[$id] );
		}
	}
	
	public function generateUrl( array &$parts ) : void
	{
		foreach( $this->property_filters as $property ) {
			if( ($part = $property->generateUrlPart()) ) {
				$parts[] = $part;
			}
		}
	}

	public function parseFilterUrl( array &$parts ) : void
	{
		foreach( $this->property_filters as $property ) {
			$property->parseFilterUrl( $parts );
		}
	}

	public function prepareFilter( array $initial_product_ids ) : void
	{

		$data_map = [];

		if($initial_product_ids) {
			$data_map = $this->listing->cache()->get( static::CACHE_KEY );

			if($data_map===null) {
				$data = Product_Parameter::dataFetchAll(
					select: [
						'product_id',
						'property_id',
						'value'
					],
					where: [
						'product_id' => $initial_product_ids
					]
				);

				$data_map = [];

				foreach($data as $d) {
					$property_id = (int)$d['property_id'];
					$product_id = (int)$d['product_id'];
					$value = $d['value'];

					if(!$value) {
						continue;
					}

					if(!isset($data_map[$property_id])) {
						$data_map[$property_id] = [];
					}

					if(!isset($data_map[$property_id][$product_id])) {
						$data_map[$property_id][$product_id] = [];
					}

					$data_map[$property_id][$product_id][] = $value;
				}

				$this->listing->cache()->set( static::CACHE_KEY, $data_map );
			}
		}

		foreach( $this->property_filters as $property_id=> $prop_filter) {
			if(isset($data_map[$property_id])) {
				$prop_filter->prepareFilter( $data_map[$property_id] );
			}
		}


	}

	public function filterIsActive() : bool
	{
		foreach( $this->property_filters as $p) {
			if( $p->isActive() ) {
				return true;
			}
		}

		return false;
	}

	public function getFilteredProductIds() : array
	{
		if($this->filtered_product_ids===null) {
			$id_map = [];

			foreach( $this->property_filters as $property) {
				if( $property->isActive() ) {
					$id_map[] = $property->getFilteredProductIds();
				}
			}

			$this->filtered_product_ids = $this->listing->idMapIntersect( $id_map );

		}

		return $this->filtered_product_ids;
	}
	
	/**
	 * @param int $group_id
	 *
	 * @return Property_Filter[]
	 */
	public function getPropertyFilters( int $group_id=0 ) : array
	{
		return $this->property_filters;
	}


	/**
	 * @param int $group_id
	 *
	 * @return Property_Filter[]
	 */
	public function getActiveProperties( int $group_id=0 ) : array
	{

		$properties = [];

		foreach( $this->property_filters as $id=> $property) {
			if($property->isActive()) {
				$properties[$id] = $property;
			}
		}

		return $properties;
	}

	public function internalGetFilteredProductIdsWithoutProperty( array $initial_product_ids, int $property_id ) : array
	{
		return $this->listing->internalGetFilteredProductIds(
			$initial_product_ids,
			'properties',
			function( ProductListing_Filter_Properties $filter, &$id_map ) use ($property_id) {
				foreach( $filter->getPropertyFilters() as $filter_property) {

					if($filter_property->getProperty()->getId()==$property_id) {
						continue;
					}

					if( $filter_property->isActive() ) {
						$id_map[] = $filter_property->getFilteredProductIds();
					}
				}

			}
		);

	}

	public function disableNonRelevantFilters() : void
	{
		foreach( $this->property_filters as $property) {
			$property->disableNonRelevantFilters();
		}

	}

	public function resetCount() : void
	{
		foreach( $this->property_filters as $property)
		{
			$property->resetCount();
		}
	}

}