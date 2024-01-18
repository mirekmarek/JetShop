<?php
namespace JetShop;

use JetApplication\Product_ShopData;
use JetApplication\ProductFilter_Filter;
use JetApplication\ProductFilter_Storage;

abstract class Core_ProductFilter_Filter_Basic extends ProductFilter_Filter
{
	protected ?int $kind_of_product_id = null;
	protected ?bool $in_stock = null;
	protected ?bool $has_discount = null;
	protected ?bool $item_is_active = null;
	
	
	public function getKey(): string
	{
		return 'basic';
	}
	
	
	public function __construct()
	{
	}
	
	protected function actualizeIsActive() : void
	{
		$this->is_active = (
			$this->kind_of_product_id !== null ||
			$this->in_stock !== null ||
			$this->has_discount !== null ||
			$this->item_is_active !== null
		);

	}
	
	public function getInStock(): ?bool
	{
		return $this->in_stock;
	}
	
	public function setInStock( ?bool $in_stock ): void
	{
		$this->in_stock = $in_stock;
		$this->actualizeIsActive();
	}
	
	public function getHasDiscount(): ?bool
	{
		return $this->has_discount;
	}
	
	public function setHasDiscount( ?bool $has_discount ): void
	{
		$this->has_discount = $has_discount;
		$this->actualizeIsActive();
	}
	
	public function getKindOfProductId(): ?int
	{
		return $this->kind_of_product_id;
	}
	
	public function setKindOfProductId( ?int $kind_of_product_id ): void
	{
		$this->kind_of_product_id = $kind_of_product_id;
		$this->actualizeIsActive();
	}

	public function getItemIsActive(): ?bool
	{
		return $this->item_is_active;
	}
	
	public function setItemIsActive( ?bool $item_is_active ): void
	{
		$this->item_is_active = $item_is_active;
		$this->actualizeIsActive();
	}
	
	
	public function prepareWhere(): array
	{
		$where = $this->product_filter->getShop()->getWhere();
		
		if($this->previous_filter_result!==null) {
			$where[] = 'AND';
			$where['entity_id'] = $this->previous_filter_result;
		}
		
		if($this->kind_of_product_id!==null) {
			$where[] = 'AND';
			$where['kind_id'] = $this->kind_of_product_id;
		}
		
		if($this->in_stock!==null) {
			$where[] = 'AND';
			if($this->in_stock) {
				$where['in_stock_qty >'] = 0;
			} else {
				$where['in_stock_qty'] = 0;
			}
		}
		
		if($this->has_discount!==null) {
			$where[] = 'AND';
			if($this->has_discount) {
				$where['discount_percentage >'] = 0;
			} else {
				$where['discount_percentage'] = 0;
			}
		}
		
		
		if($this->item_is_active!==null) {
			$where[] = 'AND';
			if($this->item_is_active) {
				$where[] = [
					'entity_is_active' => true,
					'AND',
					'is_active_for_shop' => true
				];
			} else {
				$where[] = [
					'entity_is_active' => false,
					'OR',
					'is_active_for_shop' => false
				];
			}
		}
		
		return $where;
	}
	
	
	public function filter(): void
	{
		$where = $this->prepareWhere();
		
		$this->filter_result = Product_ShopData::dataFetchCol(
			select: ['entity_id'],
			where: $where,
			raw_mode: true
		);
		
	}
	
	public function load( ProductFilter_Storage $storage ) : void
	{
		if(($kind_of_product_id = $storage->getValue($this, 'kind_of_product_id'))!==null) {
			$this->setKindOfProductId( $kind_of_product_id );
		} else {
			$this->setKindOfProductId( null );
		}
		
		if(($in_stock = $storage->getValue($this, 'in_stock'))!==null) {
			$this->setInStock( (bool)$in_stock );
		} else {
			$this->setInStock( null );
		}
		
		if(($has_discount = $storage->getValue($this, 'has_discount'))!==null) {
			$this->setHasDiscount( (bool)$has_discount );
		} else {
			$this->setHasDiscount( null );
		}
		
		if(($item_is_active = $storage->getValue($this, 'item_is_active'))!==null) {
			$this->setItemIsActive( (bool)$item_is_active );
		} else {
			$this->setItemIsActive( null );
		}
		
	}
	
	public function save( ProductFilter_Storage $storage ) : void
	{
		if($this->kind_of_product_id===null) {
			$storage->unsetValue( $this, 'kind_of_product_id' );
		} else {
			$storage->setValue( $this, 'kind_of_product_id', value: $this->kind_of_product_id );
		}
		
		if($this->in_stock===null) {
			$storage->unsetValue( $this, 'in_stock' );
		} else {
			$storage->setValue( $this, 'in_stock', value: $this->in_stock?1:0 );
		}
		
		if($this->has_discount===null) {
			$storage->unsetValue( $this, 'has_discount' );
		} else {
			$storage->setValue( $this, 'has_discount', value: $this->has_discount?1:0 );
		}
		
		if($this->item_is_active===null) {
			$storage->unsetValue( $this, 'item_is_active' );
		} else {
			$storage->setValue( $this, 'item_is_active', value: $this->item_is_active?1:0 );
		}
		
	}
	
}