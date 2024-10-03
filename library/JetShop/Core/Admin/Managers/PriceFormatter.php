<?php
namespace JetShop;

use JetApplication\Currencies_Currency;

interface Core_Admin_Managers_PriceFormatter {
	
	public function format( Currencies_Currency $currency, float $price ) : string;
	
	public function formatWithCurrency( Currencies_Currency $currency, float $price ) : string;
	
	public function format_WithoutVAT( Currencies_Currency $currency, float $price ): string;
	
	public function formatWithCurrency_WithoutVAT( Currencies_Currency $currency, float $price ): string;
	
	public function format_VAT( Currencies_Currency $currency, float $price ): string;
	
	public function formatWithCurrency_VAT( Currencies_Currency $currency, float $price ): string;
	
	public function format_WithVAT( Currencies_Currency $currency, float $price ): string;
	
	public function formatWithCurrency_WithVAT( Currencies_Currency $currency, float $price ): string;
	
	
}