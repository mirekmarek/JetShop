<?php
/**
 * @copyright Copyright (c) Miroslav Marek <mirek.marek@web-jet.cz>
 * @license EUPL 1.2  https://eupl.eu/1.2/en/
 * @author Miroslav Marek <mirek.marek@web-jet.cz>
 */
namespace JetShop;

use Jet\Application_Module;
use JetApplication\Application_Service_Admin;
use JetApplication\Currency;
use JetApplication\EShopEntity_Price;
use Jet\Application_Service_MetaInfo;
use JetApplication\Pricelist;

#[Application_Service_MetaInfo(
	group: Application_Service_Admin::GROUP,
	is_mandatory: true,
	name: 'Price Formatter',
	description: '',
	module_name_prefix: 'Admin.'
)]
abstract class Core_Application_Service_Admin_PriceFormatter extends Application_Module
{
	
	abstract public function format( Pricelist $pricelist, float $price ) : string;
	
	abstract public function formatWithCurrency( Pricelist $pricelist, float $price ) : string;
	
	abstract public function formatWithCurrencyByHasVAT( bool $has_VAT, Currency $currency, float $price ): string;
	
	abstract public function format_WithoutVAT( Currency $currency, float $price ): string;
	
	abstract public function formatWithCurrency_WithoutVAT( Currency $currency, float $price ): string;
	
	abstract public function format_VAT( Currency $currency, float $price ): string;
	
	abstract public function formatWithCurrency_VAT( Currency $currency, float $price ): string;
	
	abstract public function format_WithVAT( Currency $currency, float $price ): string;
	
	abstract public function formatWithCurrency_WithVAT( Currency $currency, float $price ): string;
	
	abstract public function showPriceInfo( EShopEntity_Price $price ) : string;
	
}