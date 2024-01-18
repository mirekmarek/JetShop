<?php
namespace JetShop;

use JetApplication\Category_ShopData;
use JetApplication\ProductFilter_Filter;
use JetApplication\ProductFilter_Storage;

abstract class Core_ProductFilter_Filter_Categories extends ProductFilter_Filter
{
	protected array $category_ids;
	protected bool $branch_mode = false;
	
	
	
	public function setCategoryIds( array $category_ids ) : void
	{
		$this->is_active = true;
		$this->category_ids = $category_ids;
	}
	
	public function getCategoryIds(): array
	{
		return $this->category_ids;
	}
	
	
	public function getKey(): string
	{
		return 'categories';
	}
	
	public function getBranchMode(): bool
	{
		return $this->branch_mode;
	}
	
	public function setBranchMode( bool $branch_mode ): void
	{
		$this->branch_mode = $branch_mode;
	}
	
	
	
	public function filter(): void
	{
		$where = ['category'=>$this->category_ids];
		
		
		$where = $this->product_filter->getShop()->getWhere();
		$where[] = 'AND';
		$where['entity_id'] = $this->category_ids;
		
		if($this->branch_mode) {
			$_ids = Category_ShopData::dataFetchCol(
				select:['branch_product_ids'],
				where: $where,
				raw_mode: true
			);
		} else {
			$_ids = Category_ShopData::dataFetchCol(
				select:['product_ids'],
				where: $where,
				raw_mode: true
			);
		}
		
		$this->filter_result = [];
		foreach($_ids as $__ids) {
			if(!$__ids) {
				continue;
			}
			
			$__ids = explode(',', $__ids);
			foreach($__ids as $product_id) {
				$product_id = (int)$product_id;
				
				if(
					$this->previous_filter_result &&
					!in_array($product_id, $this->previous_filter_result)
				) {
					continue;
				}
				
				if(!in_array($product_id, $this->filter_result)) {
					$this->filter_result[] = $product_id;
				}
			}
		}
		
	}
	
	public function load( ProductFilter_Storage $storage ): void
	{
		$categories = $storage->getValues( $this, 'categories' );
		
		if($categories) {
			$this->setCategoryIds( array_keys( $categories[0] ) );
			$this->setBranchMode( (bool)$storage->getValue($this, 'branch_mode') );
		}
	}
	
	public function save( ProductFilter_Storage $storage ): void
	{
		$storage->unsetValues( $this, 'categories' );
		$storage->setValues( $this, 'categories', [0=>$this->category_ids] );
		$storage->setValue( $this, 'branch_mode', value: $this->branch_mode?1:0 );
	}
	
}