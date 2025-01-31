<?php
/**
 * @copyright Copyright (c) Miroslav Marek <mirek.marek@web-jet.cz>
 * @license EUPL 1.2  https://eupl.eu/1.2/en/
 * @author Miroslav Marek <mirek.marek@web-jet.cz>
 */
namespace JetShop;


use JetApplication\Product_EShopData;
use JetApplication\ProductFilter_Filter;
use JetApplication\ProductFilter_Filter_Brands_Brand;
use JetApplication\ProductFilter_Storage;


abstract class Core_ProductFilter_Filter_Brands extends ProductFilter_Filter
{
	/**
	 * @var ProductFilter_Filter_Brands_Brand[]
	 */
	protected array $brands = [];
	
	public function getKey(): string
	{
		return 'brands';
	}
	
	public function initBrands( array $brand_ids ): void
	{
		
		foreach( $brand_ids as $brand_id ) {
			if(!isset($this->brands[$brand_id])) {
				$this->brands[$brand_id] = new ProductFilter_Filter_Brands_Brand( $this, $brand_id );
			}
		}
	}
	
	public function setSelectedBrands( array $selected_brand_ids ): void
	{
		if( $selected_brand_ids ) {
			$this->is_active = true;
			foreach( $selected_brand_ids as $brand_id ) {
				if( !isset( $this->brands[$brand_id] ) ) {
					$this->brands[$brand_id] = new ProductFilter_Filter_Brands_Brand( $this, $brand_id );
				}
				
				$this->brands[$brand_id]->setSelected( true );
			}
		}
	}
	
	public function getBrandSelected( int $brand_id ) : bool
	{
		if(!isset($this->brands[$brand_id])) {
			return false;
		}
		
		return $this->brands[$brand_id]->isSelected();
	}
	
	public function getSelectedBrandIds() : array
	{
		$res = [];
		
		foreach($this->brands as $brand_id=>$brand) {
			if($brand->isSelected()) {
				$res[] = $brand_id;
			}
		}
		
		return $res;
	}
	
	public function selectBrand(int $brand_id) : void
	{
		if(!isset($this->brands[$brand_id])) {
			$this->brands[$brand_id] = new ProductFilter_Filter_Brands_Brand( $this, $brand_id );
		}
		
		$this->brands[$brand_id]->setSelected( true );
		$this->is_active = true;
		
	}

	public function unselectBrand(int $brand_id) : void
	{
		if(isset($this->brands[$brand_id])) {
			$this->brands[$brand_id]->setSelected( false );
		}
		
		$this->is_active = false;
		foreach( $this->brands as $brand ) {
			if( $brand->isSelected() ) {
				$this->is_active = true;
			}
		}
	}
	
	
	public function filter(): void
	{

		$where = $this->product_filter->getEshop()->getWhere();
		
		if( $this->previous_filter_result !== null ) {
			$where[] = 'AND';
			$where['entity_id'] = $this->previous_filter_result;
		}
		
		$where[] = 'AND';
		$where[] = ['brand_id' => array_keys( $this->brands )];
		
		$_map = Product_EShopData::dataFetchAll(
			select: [
				'entity_id',
				'brand_id'
			],
			where: $where,
			raw_mode: true
		);
		
		$map = [];
		foreach( $_map as $m ) {
			$product_id = (int)$m['entity_id'];
			$brand_id = (int)$m['brand_id'];
			
			if(
				$this->previous_filter_result &&
				!in_array( $product_id, $this->previous_filter_result )
			) {
				continue;
			}
			
			
			$map[$brand_id][] = $product_id;
		}
		
		
		foreach( $map as $brand_id => $product_ids ) {
			if( isset( $this->brands[$brand_id] ) ) {
				$this->brands[$brand_id]->setProductIds( $product_ids );
			}
		}
		
		foreach( $this->brands as $brand_id => $brand ) {
			if( !count( $brand->getProductIds() ) ) {
				unset( $this->brands[$brand_id] );
			}
		}
		
		$res = [];
		foreach( $this->brands as $brand_id => $brand ) {
			if( $brand->isSelected() ) {
				$res = array_merge( $res, $brand->getProductIds() );
			}
		}
		
		$this->filter_result = array_unique( $res );
		
		
	}
	
	public function load( ProductFilter_Storage $storage ): void
	{
		$this->setSelectedBrands( array_keys( $storage->getValues( $this, 'selected_brand' ) ) );
	}
	
	public function save( ProductFilter_Storage $storage ): void
	{
		$selected_brands = [];
		foreach( $this->brands as $brand_id => $brand ) {
			if( $brand->isSelected() ) {
				$selected_brands[$brand_id] = [$brand_id];
			}
		}
		
		$storage->unsetValues( $this, 'selected_brand' );
		$storage->setValues( $this, 'selected_brand', $selected_brands );
	}
}