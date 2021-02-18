<?php
namespace JetShop;
use Jet\Form;
use Jet\Form_Field_Checkbox;

abstract class Core_ProductListing_Filter_Brands extends ProductListing_Filter_Abstract {
	const CACHE_KEY = 'f_brands';

	protected string $key = 'brands';

	/**
	 * @var ProductListing_Filter_Brands_Brand[]
	 */
	protected array $brands = [];

	protected function init() : void
	{
		$this->brands = [];

		foreach(Brand::getAll() as $brand) {
			if(!$brand->getShopData($this->shop_code)->isActive()) {
				continue;
			}

			$this->brands[$brand->getId()] = new ProductListing_Filter_Brands_Brand( $this->listing, $brand );
		}

	}

	abstract public function getFilterUrlParam() : string;


	public function getTargetFilterEditForm( Form $form, array &$target_filter ) : void
	{
		foreach(Brand::getList() as $brand) {
			$b_id = $brand->getId();

			$form->addField( new Form_Field_Checkbox('/brands/'.$b_id, $brand->getShopData()->getName(), !empty($target_filter['brands'][$b_id])  ) );
		}
	}

	public function catchTargetFilterEditForm( Form $form, array &$target_filter ) : void
	{
		$target_filter['brands'] = [];

		foreach(Brand::getList() as $brand) {
			$b_id = $brand->getId();

			if($form->field('/brands/'.$b_id)->getValue()) {
				$target_filter['brands'][$b_id] = true;
			}
		}
	}

	public function initByTargetFilter( array &$target_filter ) : void
	{
		foreach( $this->brands as $b_id=>$filter ) {

			if(isset($target_filter['brands'][$b_id])) {
				$filter->setIsForced(true);
			}
		}
	}

	public function getStateData( array &$state_data ) : void
	{
		$active = [];
		foreach( $this->brands as $id=>$filter ) {

			if($filter->isActive()) {
				$active[] = $id;
			}
		}

		$state_data['brands'] = [ 'active'=> $active ];
	}

	public function initByStateData( array $state_data ) : void
	{
		$active = $state_data['brands']['active'];

		foreach( $this->brands as $id=>$filter ) {
			$filter->setIsActive( in_array($id, $active) );
		}
	}

	public function generateCategoryTargetUrl( array &$parts ) : void
	{
		$brands = [];
		foreach( $this->brands as $brand ) {
			if( $brand->isForced() ) {
				$brands[] = $brand->getBrand()->getShopData($this->shop_code)->getUrlParam();
			}
		}

		if($brands) {
			$parts[] = $this->getFilterUrlParam().'_'.implode('+', $brands);
		}
	}

	public function generateUrl( array &$parts ) : void
	{
		$brands = [];
		foreach( $this->brands as $brand ) {
			if( $brand->isActive() ) {
				$brands[] = $brand->getBrand()->getShopData($this->shop_code)->getUrlParam();
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

				foreach( $this->brands as $brand ) {

					$url_param = $brand->getBrand()->getShopData($this->shop_code)->getUrlParam();

					if(in_array($url_param, $brands)) {
						if(!$brand->isForced()) {
							$brand->setIsActive(true);
						}
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
			$data = Product::fetchData(['id', 'brand_id'], ['id'=>$initial_product_ids]);

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
			if(isset($this->brands[$brand_id])) {
				$this->brands[$brand_id]->setProductIds( $product_ids );
			}
		}
	}

	public function filterIsActive() : bool
	{
		foreach($this->brands as $b) {
			if($b->isActive() || $b->isForced() ) {
				return true;
			}
		}

		return false;
	}

	public function getFilteredProductIds() : array
	{
		if($this->filtered_product_ids===null) {
			$this->filtered_product_ids = [];

			foreach($this->brands as $b) {
				if($b->isActive() || $b->isForced() ) {
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
	 * @return ProductListing_Filter_Brands_Brand[]
	 */
	public function getBrands() : array
	{
		return $this->brands;
	}

	/**
	 * @return ProductListing_Filter_Brands_Brand[]
	 */
	public function getActiveBrands() : array
	{
		$brands = [];
		foreach( $this->brands as $brand ) {
			if($brand->isActive()) {
				$brands[$brand->getBrand()->getId()] = $brand;
			}
		}

		return $brands;
	}

	public function disableNonRelevantFilters() : void
	{
		foreach($this->brands as $brand)
		{
			if(!$brand->getCount()) {
				$brand->setIsDisabled(true);
			}
		}
	}

	public function resetCount() : void
	{
		foreach($this->brands as $brand)
		{
			$brand->resetCount();
		}
	}

}