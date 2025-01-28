<?php
namespace JetShop;

use Jet\Locale;
use JetApplication\EShop;

interface Core_Entity_HasEShopRelation_Interface {
	
	public function setEshop( EShop $eshop ) : void;
	public function getEshopCode() : string;
	public function getLocale(): ?Locale;
	public function getEshop() : EShop;
	public function getEshopKey() : string;
}