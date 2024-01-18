<?php
namespace JetShop;

use Jet\Locale;
use Jet\MVC;
use Jet\MVC_Base_Interface;
use Jet\MVC_Page_Interface;


abstract class Core_Shops_Shop  {


	protected string $shop_code = '';
	protected ?Locale $locale = null;
	protected string $shop_name = '';
	protected bool $is_default_shop = false;
	protected string $base_id = '';
	protected string $currency_code = '';
	protected string $currency_symbol_left = '';
	protected string $currency_symbol_right = '';
	protected string $currency_with_vat_txt = '';
	protected string $currency_wo_vat_txt = '';
	protected string $currency_decimal_separator = '';
	protected string $currency_thousands_separator = '';
	protected int $currency_decimal_places = 0;
	protected array $vat_rates = [];
	protected float $default_vat_rate = 0.0;
	protected string $phone_validation_reg_exp = '';
	protected string $phone_prefix = '';
	protected int $round_precision_without_VAT = 0;
	protected int $round_precision_VAT = 0;
	protected int $round_precision_with_VAT = 0;

	public static function init( MVC_Base_Interface $base ) : array
	{
		$res = [];
		foreach($base->getLocales() as $locale) {
			$ld = $base->getLocalizedData( $locale );
			if(!$ld->getParameter('shop_code', '')) {
				continue;
			}

			$item = new static();
			$item->base_id = $base->getId();
			$item->locale = $locale;

			foreach($ld->getParameters() as $param=>$value) {
				if(is_int($item->{$param})) {
					$value = (int)$value;
				}
				if(is_float($item->{$param})) {
					$value = (float)$value;
				}
				if(is_bool($item->{$param})) {
					$value = (bool)$value;
				}
				if(is_array($item->{$param})) {
					$value = explode(',', $value);
				}

				$item->{$param} = $value;
			}

			$res[$item->getKey()] = $item;
		}

		return $res;
	}

	public function getKey() : string
	{
		return $this->shop_code.'_'.$this->locale;
	}

	public function getWhere( string $prefix='' ) : array
	{
		return [
			$prefix.'shop_code' => $this->shop_code,
			'AND',
			$prefix.'locale' => $this->locale
		];
	}

	public function getShopCode(): string
	{
		return $this->shop_code;
	}

	public function setShopCode( string $shop_code ): void
	{
		$this->shop_code = $shop_code;
	}

	public function getLocale(): Locale
	{
		return new Locale($this->locale);
	}

	public function setLocale( Locale $locale ): void
	{
		$this->locale = $locale;
	}


	public function getShopName(): string
	{
		return $this->shop_name;
	}

	public function setShopName( string $shop_name ): void
	{
		$this->shop_name = $shop_name;
	}

	public function getIsDefaultShop(): bool
	{
		return $this->is_default_shop;
	}

	public function setIsDefaultShop( bool $is_default_shop ): void
	{
		$this->is_default_shop = $is_default_shop;
	}



	public function getBaseId(): string
	{
		return $this->base_id;
	}

	public function setBaseId( string $base_id ): void
	{
		$this->base_id = $base_id;
	}

	public function getHomepage() : MVC_Page_Interface
	{
		return MVC::getBase( $this->getBaseId() )->getHomepage( $this->getLocale() );
	}

	public function getCurrencyCode(): string
	{
		return $this->currency_code;
	}

	public function setCurrencyCode( string $currency_code ): void
	{
		$this->currency_code = $currency_code;
	}

	public function getCurrencySymbolLeft(): string
	{
		return $this->currency_symbol_left;
	}

	public function setCurrencySymbolLeft( string $currency_symbol_left ): void
	{
		$this->currency_symbol_left = $currency_symbol_left;
	}

	public function getCurrencySymbolRight(): string
	{
		return $this->currency_symbol_right;
	}

	public function setCurrencySymbolRight( string $currency_symbol_right ): void
	{
		$this->currency_symbol_right = $currency_symbol_right;
	}

	public function getCurrencyWithVatTxt(): string
	{
		return $this->currency_with_vat_txt;
	}

	public function setCurrencyWithVatTxt( string $currency_with_vat_txt ): void
	{
		$this->currency_with_vat_txt = $currency_with_vat_txt;
	}

	public function getCurrencyWoVatTxt(): string
	{
		return $this->currency_wo_vat_txt;
	}

	public function setCurrencyWoVatTxt( string $currency_wo_vat_txt ): void
	{
		$this->currency_wo_vat_txt = $currency_wo_vat_txt;
	}

	public function getCurrencyDecimalSeparator(): string
	{
		return $this->currency_decimal_separator;
	}

	public function setCurrencyDecimalSeparator( string $currency_decimal_separator ): void
	{
		$this->currency_decimal_separator = $currency_decimal_separator;
	}

	public function getCurrencyThousandsSeparator(): string
	{
		return $this->currency_thousands_separator;
	}

	public function setCurrencyThousandsSeparator( string $currency_thousands_separator ): void
	{
		$this->currency_thousands_separator = $currency_thousands_separator;
	}

	public function getCurrencyDecimalPlaces(): int
	{
		return $this->currency_decimal_places;
	}

	public function setCurrencyDecimalPlaces( int $currency_decimal_places ): void
	{
		$this->currency_decimal_places = $currency_decimal_places;
	}

	public function getVatRates(): array
	{
		return $this->vat_rates;
	}

	public function getVatRatesScope() : array
	{
		$scope = [];
		foreach( $this->vat_rates as $rate) {
			$scope[$rate] = $rate.'%';
		}
		return $scope;
	}

	public function setVatRates( array $vat_rates ): void
	{
		$this->vat_rates = $vat_rates;
	}

	public function getDefaultVatRate(): float
	{
		return $this->default_vat_rate;
	}

	public function setDefaultVatRate( float $default_vat_rate ): void
	{
		$this->default_vat_rate = $default_vat_rate;
	}

	public function getPhoneValidationRegExp(): string
	{
		return $this->phone_validation_reg_exp;
	}

	public function setPhoneValidationRegExp( string $phone_validation_reg_exp ): void
	{
		$this->phone_validation_reg_exp = $phone_validation_reg_exp;
	}

	public function getPhonePrefix(): string
	{
		return $this->phone_prefix;
	}

	public function setPhonePrefix( string $phone_prefix ): void
	{
		$this->phone_prefix = $phone_prefix;
	}

	public function getRoundPrecision_WithoutVAT(): int
	{
		return $this->round_precision_without_VAT;
	}

	public function setRoundPrecisionWithoutVAT( int $round_precision_without_VAT ): void
	{
		$this->round_precision_without_VAT = $round_precision_without_VAT;
	}

	public function getRoundPrecision_VAT(): int
	{
		return $this->round_precision_VAT;
	}

	public function setRoundPrecisionVAT( int $round_precision_VAT ): void
	{
		$this->round_precision_VAT = $round_precision_VAT;
	}

	public function getRoundPrecision_WithVAT(): int
	{
		return $this->round_precision_with_VAT;
	}

	public function setRoundPrecisionWithVAT( int $round_precision_with_VAT ): void
	{
		$this->round_precision_with_VAT = $round_precision_with_VAT;
	}

	
	public function getURL( array $path_fragments = [], array $GET_params = [] ) : string
	{
		$base = MVC::getBase( $this->getBaseId() );
		
		return $base->getHomepage( $this->getLocale() )->getURL( $path_fragments, $GET_params );
	}
	
}