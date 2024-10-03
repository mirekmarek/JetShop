<?php
namespace JetShop;

use JetApplication\Pricelists_Pricelist;
use JetApplication\Entity_Price;

interface Core_Entity_HasPrice_Interface {
	
	public function getPriceEntity( Pricelists_Pricelist $pricelist ) : Entity_Price;
	
	public function getVatRate( Pricelists_Pricelist $pricelist ) : float;
	
	public function getPrice( Pricelists_Pricelist $pricelist ): float;
	
	public function getPrice_WithoutVAT( Pricelists_Pricelist $pricelist ) : float;
	
	public function getPrice_WithVAT( Pricelists_Pricelist $pricelist ) : float;
	
	public function getPrice_VAT( Pricelists_Pricelist $pricelist ) : float;
	
}