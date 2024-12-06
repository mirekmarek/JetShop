<?php
namespace JetShop;

use JetApplication\Pricelist;
use JetApplication\Entity_Price;

interface Core_Entity_HasPrice_Interface {
	
	public function getPriceEntity( Pricelist $pricelist ) : Entity_Price;
	
	public function getVatRate( Pricelist $pricelist ) : float;
	
	public function getPrice( Pricelist $pricelist ): float;
	
	public function getPrice_WithoutVAT( Pricelist $pricelist ) : float;
	
	public function getPrice_WithVAT( Pricelist $pricelist ) : float;
	
	public function getPrice_VAT( Pricelist $pricelist ) : float;
	
}