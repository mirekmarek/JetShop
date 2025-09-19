<?php
/**
 * @copyright Copyright (c) Miroslav Marek <mirek.marek@web-jet.cz>
 * @license EUPL 1.2  https://eupl.eu/1.2/en/
 * @author Miroslav Marek <mirek.marek@web-jet.cz>
 */
namespace JetShop;


use Jet\Application_Module;
use JetApplication\Application_Service_EShop;
use JetApplication\Currency;
use Jet\Application_Service_MetaInfo;
use JetApplication\Pricelist;

#[Application_Service_MetaInfo(
	group: Application_Service_EShop::GROUP,
	is_mandatory: true,
	name: 'Price formatter',
	description: '',
	module_name_prefix: 'EShop.'
)]
abstract class Core_Application_Service_EShop_PriceFormatter extends Application_Module
{
	
	abstract public function format( float $price, ?Pricelist $pricelist=null  ): string;
	
	abstract public function formatWithCurrency( float $price, ?Pricelist $pricelist=null ) : string;
	
	abstract public function format_WithoutVAT( float $price, ?Currency $currency=null ): string;
	abstract public function formatWithCurrency_WithoutVAT( float $price, ?Currency $currency=null ): string;
	
	abstract public function format_VAT( float $price, ?Currency $currency=null ): string;
	abstract public function formatWithCurrency_VAT( float $price, ?Currency $currency=null ): string;
	
	abstract public function format_WithVAT( float $price, ?Currency $currency=null ): string;
	abstract public function formatWithCurrency_WithVAT( float $price, ?Currency $currency=null ): string;
	
	
}