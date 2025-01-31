<?php
/**
 * @copyright Copyright (c) Miroslav Marek <mirek.marek@web-jet.cz>
 * @license EUPL 1.2  https://eupl.eu/1.2/en/
 * @author Miroslav Marek <mirek.marek@web-jet.cz>
 */
namespace JetShop;


use JetApplication\Pricelist;
use JetApplication\Product_Price;
use JetApplication\Product_SetItem;

trait Core_Product_EShopData_Trait_Set {
	/**
	 * @return Product_SetItem[]
	 */
	public function getSetItems(): iterable
	{
		return Product_SetItem::fetchInstances(['product_id'=>$this->entity_id]);
	}
	
	public function getSetDiscountType( Pricelist $pricelist ): string
	{
		return Product_Price::get( $pricelist, $this->getId() )->getSetDiscountType();
	}
	
	public function setSetDiscountType( Pricelist $pricelist, string $set_discount_type ): void
	{
		Product_Price::get( $pricelist, $this->getId() )->setSetDiscountType( $set_discount_type );
	}
	
	public function getSetDiscountValue( Pricelist $pricelist ): float
	{
		return Product_Price::get( $pricelist, $this->getId() )->getSetDiscountValue();
	}
	
	public function setSetDiscountValue( Pricelist $pricelist, float $set_discount_value ): void
	{
		Product_Price::get( $pricelist, $this->getId() )->setSetDiscountValue( $set_discount_value );
	}
	
	public function getCalculatedSetPrice( Pricelist $pricelist ) : float|int
	{
		$price = 0;
		
		foreach($this->getSetItems() as $set_item) {
			$price += Product_Price::get( $pricelist, $set_item->getItemProductId() )->getPrice()*$set_item->getCount();
		}
		
		return $price;
	}
	
	public function getSetDiscountAmount( Pricelist $pricelist ) : float
	{
		return $this->getPrice( $pricelist )-$this->getCalculatedSetPrice( $pricelist );
	}
	
}