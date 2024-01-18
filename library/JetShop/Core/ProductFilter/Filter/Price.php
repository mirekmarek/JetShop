<?php
namespace JetShop;

use JetApplication\Product_ShopData;
use JetApplication\ProductFilter_Filter;
use JetApplication\ProductFilter_Storage;

abstract class Core_ProductFilter_Filter_Price extends ProductFilter_Filter
{
	protected null|int|float $min_price = null;
	protected null|int|float $max_price = null;

	
	
	public function getKey(): string
	{
		return 'price';
	}
	
	
	public function setMinPrice( null|int|float $min_price ): void
	{
		$this->is_active = true;
		$this->min_price = $min_price;
	}
	
	public function getMinPrice(): ?int
	{
		return $this->min_price;
	}
	
	public function setMaxPrice( null|int|float $max_price ): void
	{
		$this->is_active = true;
		$this->max_price = $max_price;
	}
	
	public function getMaxPrice(): ?int
	{
		return $this->max_price;
	}
	
	
	public function filter(): void
	{
		$where = $this->product_filter->getShop()->getWhere();
		
		if($this->previous_filter_result!==null) {
			$where[] = 'AND';
			$where['entity_id'] = $this->previous_filter_result;
		}
		
		if($this->min_price!==null) {
			$where[] = 'AND';
			$where[] = ['price >='=>$this->min_price];
		}
		if($this->max_price!==null) {
			$where[] = 'AND';
			$where[] = ['price <='=>$this->max_price];
		}
		
		$this->filter_result = [];
		$data = Product_ShopData::dataFetchAll(
			select: ['entity_id'],
			where: $where,
			raw_mode: true
		);
		
		foreach($data as $d) {
			$product_id = (int)$d['entity_id'];
			
			if(
				$this->previous_filter_result &&
				!in_array($product_id, $this->previous_filter_result)
			) {
				continue;
			}
			
			$this->filter_result[] = $product_id;
		}
	}
	
	public function load( ProductFilter_Storage $storage ) : void
	{
		if(($min_price = $storage->getValue($this, 'min_price'))!==null) {
			$this->setMinPrice($min_price/1000);
		}
		
		if(($max_price = $storage->getValue($this, 'max_price'))!==null) {
			$this->setMaxPrice($max_price/1000);
		}
		
		
	}
	
	public function save( ProductFilter_Storage $storage ) : void
	{
		if($this->min_price===null) {
			$storage->unsetValue( $this, 'min_price' );
		} else {
			$storage->setValue( $this, 'min_price', value: round($this->min_price*1000) );
		}
		
		if($this->max_price===null) {
			$storage->unsetValue( $this, 'max_price' );
		} else {
			$storage->setValue( $this, 'max_price', value: round($this->max_price*1000) );
		}
		
		
	}
	
}