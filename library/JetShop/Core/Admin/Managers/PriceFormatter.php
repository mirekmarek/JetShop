<?php
/**
 * @copyright Copyright (c) Miroslav Marek <mirek.marek@web-jet.cz>
 * @license EUPL 1.2  https://eupl.eu/1.2/en/
 * @author Miroslav Marek <mirek.marek@web-jet.cz>
 */
namespace JetShop;


use JetApplication\Currency;
use JetApplication\EShopEntity_Price;
use JetApplication\Pricelist;

interface Core_Admin_Managers_PriceFormatter {
	
	public function format( Pricelist $pricelist, float $price ) : string;
	
	public function formatWithCurrency( Pricelist $pricelist, float $price ) : string;
	
	public function formatWithCurrencyByHasVAT( bool $has_VAT, Currency $currency, float $price ): string;
	
	public function format_WithoutVAT( Currency $currency, float $price ): string;
	
	public function formatWithCurrency_WithoutVAT( Currency $currency, float $price ): string;
	
	public function format_VAT( Currency $currency, float $price ): string;
	
	public function formatWithCurrency_VAT( Currency $currency, float $price ): string;
	
	public function format_WithVAT( Currency $currency, float $price ): string;
	
	public function formatWithCurrency_WithVAT( Currency $currency, float $price ): string;
	
	public function showPriceInfo( EShopEntity_Price $price ) : string;
	
	
}