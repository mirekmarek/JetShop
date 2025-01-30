<?php
namespace JetShop;

use JetApplication\Pricelist;

trait Core_EShopEntity_HasPrice_Trait {
	
	public function getVatRate( Pricelist $pricelist ) : float
	{
		return $this->getPriceEntity( $pricelist )->getVatRate();
	}
	
	public function getPrice( Pricelist $pricelist ): float
	{
		return $this->getPriceEntity( $pricelist )->getPrice();
	}
	
	public function getPrice_WithoutVAT( Pricelist $pricelist ) : float
	{
		return $this->getPriceEntity( $pricelist )->getPrice_WithoutVAT();
	}
	
	public function getPrice_WithVAT( Pricelist $pricelist ) : float
	{
		return $this->getPriceEntity( $pricelist )->getPrice_WithVAT();
	}
	
	public function getPrice_VAT( Pricelist $pricelist ) : float
	{
		return $this->getPriceEntity( $pricelist )->getPrice_VAT();
	}
	
}