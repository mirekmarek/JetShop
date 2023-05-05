<?php
namespace JetShop;
use Jet\Form;
use Jet\Form_Field_Checkbox;

use JetApplication\ProductListing_Filter;
use JetApplication\Brand_Filter;
use JetApplication\Brand;
use JetApplication\Product;

abstract class Core_ProductListing_Filter_Brands extends ProductListing_Filter {
	const CACHE_KEY = 'f_brands';

	protected string $key = 'brands';

	/**
	 * @var Brand_Filter[]
	 */
	protected array $brand_filters;
	
	public function initProductListing() : void
	{
		$this->brand_filters = [];
		
		foreach(Brand::getAll() as $brand) {
			if(!$brand->getShopData($this->shop)->isActive()) {
				continue;
			}
			
			$this->brand_filters[$brand->getId()] = new Brand_Filter( $this->listing, $brand );
		}
	}
	
	public function initAutoAppendFilter( array &$target_filter ) : void
	{
		$this->brand_filters = [];
		
		foreach(Brand::getAll() as $brand) {
			$this->brand_filters[$brand->getId()] = new Brand_Filter( $this->listing, $brand );
		}
		
		foreach( $this->brand_filters as $b_id=> $filter ) {
			
			if(isset($target_filter['brands'][$b_id])) {
				$filter->setIsActive(true);
			}
		}
	}
	
	protected function init() : void
	{

	}

	abstract public function getFilterUrlParam() : string;


	public function getAutoAppendProductFilterEditForm( Form $form ) : void
	{
		foreach( $this->brand_filters as $b_id=> $b) {
			
			$field = new Form_Field_Checkbox('/brands/'.$b_id, $b->getBrand()->getShopData()->getName()  );
			$field->setDefaultValue( $b->isActive() );
			
			$form->addField( $field );
		}
	}

	public function catchAutoAppendFilterEditForm( Form $form, array &$target_filter ) : void
	{
		$target_filter['brands'] = [];

		foreach(Brand::getList() as $brand) {
			$b_id = $brand->getId();

			if($form->field('/brands/'.$b_id)->getValue()) {
				$target_filter['brands'][$b_id] = true;
			}
		}
	}
	
	public function getStateData( array &$state_data ) : void
	{
		$active = [];
		foreach( $this->brand_filters as $id=> $filter ) {

			if($filter->isActive()) {
				$active[] = $id;
			}
		}

		$state_data['brands'] = [ 'active'=> $active ];
	}

	public function initByStateData( array $state_data ) : void
	{
		$active = $state_data['brands']['active'];

		foreach( $this->brand_filters as $id=> $filter ) {
			$filter->setIsActive( in_array($id, $active) );
		}
	}

	public function generateUrl( array &$parts ) : void
	{
		$brands = [];
		foreach( $this->brand_filters as $brand ) {
			if( $brand->isActive() ) {
				$brands[] = $brand->getBrand()->getShopData($this->shop)->getUrlParam();
			}
		}

		if($brands) {
			$parts[] = $this->getFilterUrlParam().'_'.implode('+', $brands);
		}
	}

	public function parseFilterUrl( array &$parts ) : void
	{
		$prefix = $this->getFilterUrlParam().'_';

		foreach($parts as $i=>$part) {
			if(stripos($part, $prefix)===0) {
				unset($parts[$i]);

				$brands = explode('_', $part)[1];
				$brands = explode('+', $brands);

				foreach( $this->brand_filters as $brand ) {

					$url_param = $brand->getBrand()->getShopData($this->shop)->getUrlParam();

					if(in_array($url_param, $brands)) {
						$brand->setIsActive(true);
					}
				}

			}
		}
	}

	public function prepareFilter( array $initial_product_ids ) : void
	{
		if(!$initial_product_ids) {
			return;
		}

		$map = $this->listing->cache()->get( static::CACHE_KEY );

		if($map===null) {
			$data = Product::dataFetchAll(select:['id', 'brand_id'], where:['id'=>$initial_product_ids]);

			$map = [];
			foreach($data as $d) {
				$p_id = (int)$d['id'];
				$brand_id = (int)$d['brand_id'];

				if(!isset($map[$brand_id])) {
					$map[$brand_id] = [];
				}

				$map[$brand_id][] = $p_id;
			}

			$this->listing->cache()->set( static::CACHE_KEY, $map );
		}


		foreach($map as $brand_id=>$product_ids) {
			if(isset( $this->brand_filters[$brand_id])) {
				$this->brand_filters[$brand_id]->setProductIds( $product_ids );
			}
		}
	}

	public function filterIsActive() : bool
	{
		foreach( $this->brand_filters as $b) {
			if($b->isActive() ) {
				return true;
			}
		}

		return false;
	}

	public function getFilteredProductIds() : array
	{
		if($this->filtered_product_ids===null) {
			$this->filtered_product_ids = [];

			foreach( $this->brand_filters as $b) {
				if($b->isActive() ) {
					$this->filtered_product_ids = array_merge(
						$this->filtered_product_ids,
						$b->getProductIds()
					);
				}
			}
		}

		return $this->filtered_product_ids;
	}

	/**
	 * @return Brand_Filter[]
	 */
	public function getBrandFilters() : array
	{
		return $this->brand_filters;
	}

	/**
	 * @return Brand_Filter[]
	 */
	public function getActiveBrands() : array
	{
		$brands = [];
		foreach( $this->brand_filters as $brand ) {
			if($brand->isActive()) {
				$brands[$brand->getBrand()->getId()] = $brand;
			}
		}

		return $brands;
	}

	public function disableNonRelevantFilters() : void
	{
		foreach( $this->brand_filters as $brand)
		{
			if(!$brand->getCount()) {
				$brand->setIsDisabled(true);
			}
		}
	}

	public function resetCount() : void
	{
		foreach( $this->brand_filters as $brand)
		{
			$brand->resetCount();
		}
	}

}