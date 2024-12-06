<?php
namespace JetShop;

use JetApplication\Currency;
use JetApplication\Entity_Price;
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
	
	public function showPriceInfo( Entity_Price $price ) : string;
	
	
}