<?php
/**
 * @copyright Copyright (c) Miroslav Marek <mirek.marek@web-jet.cz>
 * @license EUPL 1.2  https://eupl.eu/1.2/en/
 * @author Miroslav Marek <mirek.marek@web-jet.cz>
 */
namespace JetShop;


use Jet\Locale;
use JetApplication\EShop;

interface Core_EShopEntity_HasEShopRelation_Interface {
	
	public function setEshop( EShop $eshop ) : void;
	public function getEshopCode() : string;
	public function getLocale(): ?Locale;
	public function getEshop() : EShop;
	public function getEshopKey() : string;
}