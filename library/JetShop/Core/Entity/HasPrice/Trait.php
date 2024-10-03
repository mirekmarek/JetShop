<?php
namespace JetShop;

use JetApplication\Pricelists_Pricelist;

trait Core_Entity_HasPrice_Trait {
	
	public function getVatRate( Pricelists_Pricelist $pricelist ) : float
	{
		return $this->getPriceEntity( $pricelist )->getVatRate();
	}
	
	public function getPrice( Pricelists_Pricelist $pricelist ): float
	{
		return $this->getPriceEntity( $pricelist )->getPrice();
	}
	
	public function getPrice_WithoutVAT( Pricelists_Pricelist $pricelist ) : float
	{
		return $this->getPriceEntity( $pricelist )->getPrice_WithoutVAT();
	}
	
	public function getPrice_WithVAT( Pricelists_Pricelist $pricelist ) : float
	{
		return $this->getPriceEntity( $pricelist )->getPrice_WithVAT();
	}
	
	public function getPrice_VAT( Pricelists_Pricelist $pricelist ) : float
	{
		return $this->getPriceEntity( $pricelist )->getPrice_VAT();
	}
	
}