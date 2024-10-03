<?php
namespace JetShop;

use JetApplication\Pricelists_Pricelist;
use JetApplication\Product_Price;
use JetApplication\Product_PriceHistory;

trait Core_Product_Trait_Price
{
	use Core_Entity_HasPrice_Trait;
	
	public function getPriceEntity( Pricelists_Pricelist $pricelist ) : Product_Price
	{
		return Product_Price::get( $pricelist, $this->getId() );
	}
	
	public function getPriceBeforeDiscount( Pricelists_Pricelist $pricelist ) : float
	{
		return $this->getPriceEntity( $pricelist )->getPriceBeforeDiscount();
	}
	
	public function getDiscountPercentage( Pricelists_Pricelist $pricelist ) : float
	{
		return $this->getPriceEntity( $pricelist )->getDiscountPercentage();
	}
	
	/**
	 * @return Product_PriceHistory[]
	 */
	public function getPriceHistory( Pricelists_Pricelist $pricelist ) : array
	{
		return Product_PriceHistory::get( $pricelist, $this->getId() );
	}
	
}