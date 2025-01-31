<?php
/**
 * @copyright Copyright (c) Miroslav Marek <mirek.marek@web-jet.cz>
 * @license EUPL 1.2  https://eupl.eu/1.2/en/
 * @author Miroslav Marek <mirek.marek@web-jet.cz>
 */
namespace JetShop;


use JetApplication\Currency;
use JetApplication\Pricelist;

interface Core_EShop_Managers_PriceFormatter {
	
	public function format( float $price, ?Pricelist $pricelist=null  ): string;
	
	public function formatWithCurrency( float $price, ?Pricelist $pricelist=null ) : string;
	
	public function format_WithoutVAT( float $price, ?Currency $currency=null ): string;
	public function formatWithCurrency_WithoutVAT( float $price, ?Currency $currency=null ): string;
	
	public function format_VAT( float $price, ?Currency $currency=null ): string;
	public function formatWithCurrency_VAT( float $price, ?Currency $currency=null ): string;
	
	public function format_WithVAT( float $price, ?Currency $currency=null ): string;
	public function formatWithCurrency_WithVAT( float $price, ?Currency $currency=null ): string;
	
	
}