<?php
namespace JetShop;

use Jet\DataModel;
use Jet\DataModel_Definition;
use JetApplication\Product;
use JetApplication\Product_SetItem;
use JetApplication\Product_ShopData;

trait Core_Product_ShopData_Trait_Set {
	
	#[DataModel_Definition(
		type: DataModel::TYPE_STRING,
		max_len: 20,
	)]
	protected string $set_discount_type = '';
	
	#[DataModel_Definition(
		type: DataModel::TYPE_FLOAT,
	)]
	protected float $set_discount_value = 0.0;
	
	
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
	
	/**
	 * @return Product_SetItem[]
	 */
	public function getSetItems(): iterable
	{
		return Product_SetItem::fetchInstances(['product_id'=>$this->entity_id]);
	}
	
	public function getCalculatedSetPrice() : float|int
	{
		$price = 0;
		
		foreach($this->getSetItems() as $set_item) {
			$set_item_shop_data = Product_ShopData::get(
				$set_item->getItemProductId(),
				$this->getShop()
			);
			
			$price += $set_item_shop_data->getPrice()*$set_item->getCount();
		}
		
		return $price;
	}
	
	public function getSetIds() : array
	{
		return Product_SetItem::dataFetchCol(
			select: ['product_id'],
			where: [
				'item_product_id' => $this->getId()
			],
			group_by: ['product_id'],
			raw_mode: true
		);
	}
	
	public function actualizeSet() : void
	{
		if($this->type!=Product::PRODUCT_TYPE_SET) {
			return;
		}
		
		$updated_price     = $this->actualizeSet_Price();
		$updated_stock_qty = $this->actualizeSet_InStockQty();
		$updated_avl       = $this->actualizeSet_Availability();
		
		
		
		if($updated_price || $updated_stock_qty || $updated_avl) {
			$this->save();
		}
	}
	
	
	public function actualizeSet_Price() : bool
	{
		if($this->type!=Product::PRODUCT_TYPE_SET) {
			return false;
		}
		
		
		$calculated_price = 0;
		
		$shop = $this->getShop();
		
		foreach($this->getSetItems() as $set_item) {
			/**
			 * @var Product_ShopData $set_item_shop_data
			 */
			$set_item_shop_data = static::get( $set_item->getItemProductId(), $shop );
			
			$calculated_price += $set_item_shop_data->getPrice()*$set_item->getCount();
		}
		
		$updated = false;
		
		$price = $calculated_price;
		
		if($this->getSetDiscountValue()>0) {
			switch($this->getSetDiscountType()) {
				case Product::SET_DISCOUNT_NOMINAL:
					if($this->getSetDiscountValue()<$calculated_price) {
						$price = $calculated_price-$this->getSetDiscountValue();
					}
					
					break;
				case Product::SET_DISCOUNT_PERCENT:
					if($this->getSetDiscountValue()<100) {
						$mtp = (100-$this->getSetDiscountValue())/100;
						$price = round($calculated_price*$mtp, $shop->getRoundPrecision_WithVAT());
					}
					
					break;
			}
		}
		
		if($price!=$this->getPrice()) {
			if($calculated_price>$price) {
				$this->setStandardPrice( $calculated_price );
			} else {
				$this->setStandardPrice( 0 );
			}
			$this->setPrice( $price );
			
			$updated = true;
		}
		
		return $updated;
	}
	
	public function actualizeSet_InStockQty() : bool
	{
		if($this->type!=Product::PRODUCT_TYPE_SET) {
			return false;
		}
		
		$worst_stock_avl = null;
		
		$shop = $this->getShop();
		
		foreach($this->getSetItems() as $set_item) {
			/**
			 * @var Product_ShopData $set_item_shop_data
			 */
			$set_item_shop_data = static::get( $set_item->getItemProductId(), $shop );
			
			
			$stock_avl = floor($set_item_shop_data->getInStockQty()/$set_item->getCount());
			
			if(
				$worst_stock_avl===null ||
				$worst_stock_avl>$stock_avl
			) {
				$worst_stock_avl = $stock_avl;
			}
			
		}
		
		$updated = false;
		
		if($this->getInStockQty()!=$worst_stock_avl) {
			$this->setInStockQty( $worst_stock_avl );
			$updated = true;
		}
		
		return $updated;
	}
	
	public function actualizeSet_Availability() : bool
	{
		if($this->type!=Product::PRODUCT_TYPE_SET) {
			return false;
		}
		
		
		/**
		 * @var Product_ShopData $worst_delivery
		 */
		$worst_delivery = null;
		
		$shop = $this->getShop();
		
		foreach($this->getSetItems() as $set_item) {
			/**
			 * @var Product_ShopData $set_item_shop_data
			 */
			$set_item_shop_data = static::get( $set_item->getItemProductId(), $shop );
			
			if( $worst_delivery===null ) {
				$worst_delivery = $set_item_shop_data;
				continue;
			}
				
			if(
				$set_item_shop_data->getAvailableFrom() ||
				$worst_delivery->getAvailableFrom()
			) {
				if(
					!$worst_delivery->getAvailableFrom() &&
					$set_item_shop_data->getAvailableFrom()
				) {
					$worst_delivery = $set_item_shop_data;
					continue;
				}
				
				if(
					$worst_delivery->getAvailableFrom() &&
					$set_item_shop_data->getAvailableFrom() &&
					$set_item_shop_data->getAvailableFrom() > $worst_delivery->getAvailableFrom()
				) {
					$worst_delivery = $set_item_shop_data;
					continue;
				}


				continue;
			}
			
			if(
				$set_item_shop_data->getLengthOfDelivery() >
				$worst_delivery->getLengthOfDelivery()
			) {
				$worst_delivery = $set_item_shop_data;
			}
		}
		
		$updated = false;
		
		
		if($this->getAvailableFrom()!=$worst_delivery->getAvailableFrom()) {
			$this->setAvailableFrom( $worst_delivery->getAvailableFrom() );
			$updated = true;
		}
		
		if($this->getLengthOfDelivery()!=$worst_delivery->getLengthOfDelivery()) {
			$this->setLengthOfDelivery( $worst_delivery->getLengthOfDelivery() );
			$updated = true;
		}
		
		return $updated;
	}
	
	public function getSetDiscountAmount() : float
	{
		return $this->getPrice()-$this->getCalculatedSetPrice();
	}
	
}