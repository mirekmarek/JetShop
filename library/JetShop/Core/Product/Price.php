<?php
namespace JetShop;


use Jet\DataModel;
use Jet\DataModel_Definition;
use JetApplication\EShopEntity_Price;
use JetApplication\Product;
use JetApplication\Product_PriceHistory;
use JetApplication\Product_SetItem;

#[DataModel_Definition(
	name: 'products_price',
	database_table_name: 'products_price'
)]
abstract class Core_Product_Price extends EShopEntity_Price
{
	
	#[DataModel_Definition(
		type: DataModel::TYPE_FLOAT,
		is_key: true
	)]
	protected float $price_before_discount = 0.0;
	
	#[DataModel_Definition(
		type: DataModel::TYPE_FLOAT,
		is_key: true
	)]
	protected float $discount_percentage = 0.0;
	
	#[DataModel_Definition(
		type: DataModel::TYPE_STRING,
		max_len: 20,
	)]
	protected string $set_discount_type = '';
	
	#[DataModel_Definition(
		type: DataModel::TYPE_FLOAT,
	)]
	protected float $set_discount_value = 0.0;
	
	protected bool $do_not_actualize_references = false;
	
	
	public function getPriceBeforeDiscount(): float
	{
		return $this->price_before_discount;
	}
	
	public function setPriceBeforeDiscount( float $price_before_discount ): void
	{
		$this->price_before_discount = $price_before_discount;
		$this->calcDiscount();
	}
	
	public function getDiscountPercentage(): float
	{
		return $this->discount_percentage;
	}
	
	
	protected function calcDiscount() : void
	{
		if($this->price_before_discount>0) {
			$this->discount_percentage = 100-( ($this->price * 100) / $this->price_before_discount);
		} else {
			$this->discount_percentage = 0;
		}
	}
	
	public function afterAdd(): void
	{
		parent::afterAdd();
		Product_PriceHistory::newRecord( $this );
		
		$this->actualizeReferences();
	}
	public function afterUpdate(): void
	{
		parent::afterUpdate();
		Product_PriceHistory::newRecord( $this );
		
		$this->actualizeReferences();
	}
	
	public function getSetDiscountType(): string
	{
		return $this->set_discount_type;
	}

	public function setSetDiscountType( string $set_discount_type ): void
	{
		$this->set_discount_type = $set_discount_type;
	}
	
	public function getSetDiscountValue(): float
	{
		return $this->set_discount_value;
	}
	
	public function setSetDiscountValue( float $set_discount_value ): void
	{
		$this->set_discount_value = $set_discount_value;
	}
	
	
	public function actualizeReferences() : void
	{
		if($this->do_not_actualize_references) {
			$this->do_not_actualize_references = false;
			return;
		}
		
		switch( Product::getProductType( $this->entity_id ) ) {
			case Product::PRODUCT_TYPE_REGULAR:
				foreach( Product_SetItem::getSetIds($this->entity_id) as $set_id ) {
					static::get( $this->getPricelist(), $set_id )->actualizeSet();
				}
				break;
			case Product::PRODUCT_TYPE_SET:
				$this->actualizeSet();
				break;
			case Product::PRODUCT_TYPE_VARIANT:
				static::get(
					$this->getPricelist(),
					Product::getProductVariantMasterProductId( $this->entity_id )
				)->actualizeVariantMasterPrice();
				break;
			case Product::PRODUCT_TYPE_VARIANT_MASTER:
				$this->actualizeVariantMasterPrice();
				break;
		}
	}
	
	
	public function actualizeVariantMasterPrice() : bool
	{
		$variant_ids = Product::getProductActiveVariantIds( $this->entity_id );

		
		$cheapest = null;
		foreach($variant_ids as $variant_id) {
			$variant_price = static::get( $this->getPricelist(), $variant_id );
			if(!$variant_price->getPrice()) {
				continue;
			}
			
			if(!$cheapest) {
				$cheapest = $variant_price;
				continue;
			}
			
			if( $variant_price->getPrice() < $cheapest->getPrice() ) {
				$cheapest = $variant_price;
			}
		}
		
		if(!$cheapest) {
			return false;
		}
		
		$updated = [];
		
		$price = $cheapest->getPrice();
		
		
		if($price!=$this->price) {
			$this->price = $price;
			$updated['price'] = $price;
		}
		
		if($updated) {
			$this->do_not_actualize_references = true;
			$this->calcDiscount();
			$this->save();
			
			Product_PriceHistory::newRecord( $this );
		}
		
		
		return (bool)$updated;
	}
	
	public function getCalculatedSetPrice() : float|int
	{
		$price = 0;
		
		foreach( Product_SetItem::getProductSetItems( $this->entity_id ) as $set_item) {
			$price += static::get( $this->getPricelist(), $set_item->getItemProductId() )->getPrice()*$set_item->getCount();
		}
		
		return $price;
	}
	
	
	public function actualizeSet() : bool
	{
		$calculated_price = $this->getCalculatedSetPrice();
			
		$price = $calculated_price;
		$price_before_discount = 0;
			
		if($this->getSetDiscountValue()>0) {
			switch( $this->getSetDiscountType() ) {
				case Product::SET_DISCOUNT_NOMINAL:
					if($this->getSetDiscountValue()<$calculated_price) {
						$price = $calculated_price-$this->getSetDiscountValue();
					}
					
					break;
				case Product::SET_DISCOUNT_PERCENT:
					if($this->getSetDiscountValue()<100) {
						$mtp = (100-$this->getSetDiscountValue())/100;
						$price = $this->getPricelist()->round( $calculated_price*$mtp );
					}
					
					break;
			}
		}
		
		if($calculated_price>$price) {
			$price_before_discount = $calculated_price;
		}
		
		
		$updated = [];
		
		if($price!=$this->getPrice()) {
			$this->price = $price;
			$updated['price'] = $price;
		}
		
		if($price_before_discount!=$this->price_before_discount) {
			$this->price_before_discount = $price_before_discount;
			$updated['price_before_discount'] = $price_before_discount;
		}
		
		
		if($updated) {
			$this->do_not_actualize_references = true;
			$this->calcDiscount();
			$this->save();
			
			Product_PriceHistory::newRecord( $this );
		}
		
		
		return (bool)$updated;
	}
	
	
}