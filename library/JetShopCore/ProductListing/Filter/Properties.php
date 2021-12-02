<?php
namespace JetShop;
use Jet\Form;

abstract class Core_ProductListing_Filter_Properties extends ProductListing_Filter_Abstract {
	const CACHE_KEY = 'f_param';

	protected string $key = 'properties';

	/**
	 * @var ProductListing_Filter_Properties_Group[]
	 */
	protected array $groups = [];

	/**
	 * @var ProductListing_Filter_Properties_Property[]
	 */
	protected array $properties = [];

	protected array|null $filtered_product_ids = null;

	protected function init() : void
	{
		$category = $this->listing->getCategory();
		if(!$category) {
			return;
		}

		$this->properties = [];

		$group_ids = [];

		foreach($category->getParametrizationProperties() as $property) {
			if(
				!$property->getIsFilterable() ||
				!$property->getShopData($this->shop)->isActive()
			) {
				continue;
			}

			$prop_filter = $property->getFilterInstance( $this->listing );

			$this->properties[$property->getId()] = $prop_filter;

			$group_ids[$property->getGroupId()] = $property->getGroupId();
		}


		$this->groups = [];

		foreach($category->getParametrizationGroups() as $group) {
			$g_id = $group->getId();
			if(!isset($group_ids[$g_id])) {
				continue;
			}

			$this->groups[$g_id] = new ProductListing_Filter_Properties_Group( $this->listing, $group );

		}
	}

	public function getTargetFilterEditForm( Form $form, array &$target_filter ) : void
	{
		foreach($this->properties as $property) {
			$property->getTargetFilterEditForm( $form, $target_filter );
		}
	}

	public function catchTargetFilterEditForm( Form $form, &$target_filter ) : void
	{
		$target_filter['properties'] = [];

		foreach($this->properties as $property) {
			$property->catchTargetFilterEditForm( $form, $target_filter );
		}
	}

	public function initByTargetFilter( array &$target_filter ) : void
	{
		foreach($this->properties as $prop_filter) {
			$prop_filter->initByTargetFilter( $target_filter );
		}
	}

	public function getStateData( &$state_data ) : void
	{
		$properties = [];
		foreach( $this->properties as $id=>$filter ) {

			$properties[$id] = $filter->getStateData();
		}

		$state_data['properties'] = $properties;
	}

	public function initByStateData( array $state_data ) : void
	{
		$properties = $state_data['properties'];

		foreach( $this->properties as $id=>$filter ) {
			if(
				!isset($properties[$id]) ||
				!is_array($properties[$id])
			) {
				continue;
			}

			$filter->initByStateData( $properties[$id] );
		}
	}

	public function generateCategoryTargetUrl( array &$parts ) : void
	{
		foreach( $this->properties as $property ) {
			if( ($part = $property->generateCategoryTargetUrlPart()) ) {
				$parts[] = $part;
			}
		}
	}

	public function generateUrl( array &$parts ) : void
	{
		foreach( $this->properties as $property ) {
			if( ($part = $property->generateUrlPart()) ) {
				$parts[] = $part;
			}
		}
	}

	public function parseFilterUrl( array &$parts ) : void
	{
		foreach( $this->properties as $property ) {
			$property->parseFilterUrl( $parts );
		}
	}

	public function prepareFilter( array $initial_product_ids ) : void
	{

		$data_map = [];

		if($initial_product_ids) {
			$data_map = $this->listing->cache()->get( static::CACHE_KEY );

			if($data_map===null) {
				$data = Product_ParametrizationValue::fetchData(
					[
						'product_id',
						'property_id',
						'value'
					],
					[
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

		foreach($this->properties as $property_id=>$prop_filter) {
			if(isset($data_map[$property_id])) {
				$prop_filter->prepareFilter( $data_map[$property_id] );
			}
		}


	}

	public function filterIsActive() : bool
	{
		foreach($this->properties as $p) {
			if(
				$p->isActive() ||
				$p->isForced()
			) {
				return true;
			}
		}

		return false;
	}

	public function getFilteredProductIds() : array
	{
		if($this->filtered_product_ids===null) {
			$id_map = [];

			foreach($this->properties as $property) {
				if($property->isActive() || $property->isForced()) {
					$id_map[] = $property->getFilteredProductIds();
				}
			}

			$this->filtered_product_ids = $this->listing->idMapIntersect( $id_map );

		}

		return $this->filtered_product_ids;
	}

	/**
	 * @return ProductListing_Filter_Properties_Group[]
	 */
	public function getGroups() : array
	{
		return $this->groups;
	}

	/**
	 * @param int $group_id
	 *
	 * @return ProductListing_Filter_Properties_Property[]
	 */
	public function getProperties( int $group_id=0 ) : array
	{
		if(!$group_id) {
			return $this->properties;
		}

		$properties = [];

		foreach($this->properties as $id=>$property) {
			if($property->getProperty()->getGroupId()==$group_id) {
				$properties[$id] = $property;
			}
		}

		return $properties;
	}


	/**
	 * @param int $group_id
	 *
	 * @return ProductListing_Filter_Properties_Property[]
	 */
	public function getActiveProperties( int $group_id=0 ) : array
	{

		$properties = [];

		foreach($this->properties as $id=>$property) {
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
				foreach($filter->getProperties() as $filter_property) {

					if($filter_property->getProperty()->getId()==$property_id) {
						continue;
					}

					if(
						$filter_property->isActive() ||
						$filter_property->isForced()
					) {
						$id_map[] = $filter_property->getFilteredProductIds();
					}
				}

			}
		);

	}

	public function disableNonRelevantFilters() : void
	{
		foreach($this->properties as $property) {
			$property->disableNonRelevantFilters();
		}

		foreach($this->groups as $group) {
			$group->disableNonRelevantFilters();
		}
	}

	public function resetCount() : void
	{
		foreach($this->properties as $property)
		{
			$property->resetCount();
		}
	}

}