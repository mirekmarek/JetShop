<?php
namespace JetShop;

use Jet\Config;
use Jet\Config_Definition;
use Jet\Config_Section;
use Jet\Locale;

#[Config_Definition(
	name: 'shops'
)]
class Core_Shops_Shop extends Config_Section {


	#[Config_Definition(
		type : Config::TYPE_STRING
	)]
	protected string $id = '';

	#[Config_Definition(
		type : Config::TYPE_STRING
	)]
	protected string $name = '';

	#[Config_Definition(
		type : Config::TYPE_BOOL
	)]
	protected bool $is_default = false;

	#[Config_Definition(
		type : Config::TYPE_STRING
	)]
	protected string $site_id = '';

	#[Config_Definition(
		type : Config::TYPE_STRING
	)]
	protected string $locale = '';

	#[Config_Definition(
		type : Config::TYPE_STRING
	)]
	protected string $currency_code = '';

	#[Config_Definition(
		type : Config::TYPE_STRING
	)]
	protected string $currency_symbol_left = '';

	#[Config_Definition(
		type : Config::TYPE_STRING
	)]
	protected string $currency_symbol_right = '';

	#[Config_Definition(
		type : Config::TYPE_STRING
	)]
	protected string $currency_with_vat_txt = '';

	#[Config_Definition(
		type : Config::TYPE_STRING
	)]
	protected string $currency_wo_vat_txt = '';

	#[Config_Definition(
		type : Config::TYPE_STRING
	)]
	protected string $currency_decimal_separator = '';

	#[Config_Definition(
		type : Config::TYPE_STRING
	)]
	protected string $currency_thousands_separator = '';

	#[Config_Definition(
		type : Config::TYPE_INT
	)]
	protected int $currency_decimal_places = 0;

	#[Config_Definition(
		type : Config::TYPE_ARRAY
	)]
	protected array $vat_rates = [];

	#[Config_Definition(
		type : Config::TYPE_FLOAT
	)]
	protected float $default_vat_rate = 0.0;

	#[Config_Definition(
		type : Config::TYPE_STRING
	)]
	protected string $phone_validation_reg_exp = '';

	#[Config_Definition(
		type : Config::TYPE_STRING
	)]
	protected string $phone_prefix = '';

	#[Config_Definition(
		type : Config::TYPE_INT
	)]
	protected int $round_precision_without_VAT = 0;

	#[Config_Definition(
		type : Config::TYPE_INT
	)]
	protected int $round_precision_VAT = 0;

	#[Config_Definition(
		type : Config::TYPE_INT
	)]
	protected int $round_precision_with_VAT = 0;

	/**
	 * @return string
	 */
	public function getId(): string
	{
		return $this->id;
	}

	/**
	 * @param string $id
	 */
	public function setId( string $id ): void
	{
		$this->id = $id;
	}

	/**
	 * @return string
	 */
	public function getName(): string
	{
		return $this->name;
	}

	/**
	 * @param string $name
	 */
	public function setName( string $name ): void
	{
		$this->name = $name;
	}

	/**
	 * @return bool
	 */
	public function isDefault(): bool
	{
		return $this->is_default;
	}

	/**
	 * @param bool $is_default
	 */
	public function setIsDefault( bool $is_default ): void
	{
		$this->is_default = $is_default;
	}



	/**
	 * @return string
	 */
	public function getSiteId(): string
	{
		return $this->site_id;
	}

	/**
	 * @param string $site_id
	 */
	public function setSiteId( string $site_id ): void
	{
		$this->site_id = $site_id;
	}

	/**
	 * @param bool $as_string
	 *
	 * @return Locale
	 */
	public function getLocale( $as_string=false ): Locale
	{
		return new Locale($this->locale);
	}

	/**
	 * @param string $locale
	 */
	public function setLocale( string $locale ): void
	{
		$this->locale = $locale;
	}

	/**
	 * @return string
	 */
	public function getCurrencyCode(): string
	{
		return $this->currency_code;
	}

	/**
	 * @param string $currency_code
	 */
	public function setCurrencyCode( string $currency_code ): void
	{
		$this->currency_code = $currency_code;
	}

	/**
	 * @return string
	 */
	public function getCurrencySymbolLeft(): string
	{
		return $this->currency_symbol_left;
	}

	/**
	 * @param string $currency_symbol_left
	 */
	public function setCurrencySymbolLeft( string $currency_symbol_left ): void
	{
		$this->currency_symbol_left = $currency_symbol_left;
	}

	/**
	 * @return string
	 */
	public function getCurrencySymbolRight(): string
	{
		return $this->currency_symbol_right;
	}

	/**
	 * @param string $currency_symbol_right
	 */
	public function setCurrencySymbolRight( string $currency_symbol_right ): void
	{
		$this->currency_symbol_right = $currency_symbol_right;
	}

	/**
	 * @return string
	 */
	public function getCurrencyWithVatTxt(): string
	{
		return $this->currency_with_vat_txt;
	}

	/**
	 * @param string $currency_with_vat_txt
	 */
	public function setCurrencyWithVatTxt( string $currency_with_vat_txt ): void
	{
		$this->currency_with_vat_txt = $currency_with_vat_txt;
	}

	/**
	 * @return string
	 */
	public function getCurrencyWoVatTxt(): string
	{
		return $this->currency_wo_vat_txt;
	}

	/**
	 * @param string $currency_wo_vat_txt
	 */
	public function setCurrencyWoVatTxt( string $currency_wo_vat_txt ): void
	{
		$this->currency_wo_vat_txt = $currency_wo_vat_txt;
	}

	/**
	 * @return string
	 */
	public function getCurrencyDecimalSeparator(): string
	{
		return $this->currency_decimal_separator;
	}

	/**
	 * @param string $currency_decimal_separator
	 */
	public function setCurrencyDecimalSeparator( string $currency_decimal_separator ): void
	{
		$this->currency_decimal_separator = $currency_decimal_separator;
	}

	/**
	 * @return string
	 */
	public function getCurrencyThousandsSeparator(): string
	{
		return $this->currency_thousands_separator;
	}

	/**
	 * @param string $currency_thousands_separator
	 */
	public function setCurrencyThousandsSeparator( string $currency_thousands_separator ): void
	{
		$this->currency_thousands_separator = $currency_thousands_separator;
	}

	/**
	 * @return int
	 */
	public function getCurrencyDecimalPlaces(): int
	{
		return $this->currency_decimal_places;
	}

	/**
	 * @param int $currency_decimal_places
	 */
	public function setCurrencyDecimalPlaces( int $currency_decimal_places ): void
	{
		$this->currency_decimal_places = $currency_decimal_places;
	}

	/**
	 * @return array
	 */
	public function getVatRates(): array
	{
		return $this->vat_rates;
	}

	/**
	 * @return array
	 */
	public function getVatRatesScope() : array
	{
		$scope = [];
		foreach( $this->vat_rates as $rate) {
			$scope[$rate] = $rate.'%';
		}
		return $scope;

	}

	/**
	 * @param array $vat_rates
	 */
	public function setVatRates( array $vat_rates ): void
	{
		$this->vat_rates = $vat_rates;
	}

	/**
	 * @return float
	 */
	public function getDefaultVatRate(): float
	{
		return $this->default_vat_rate;
	}

	/**
	 * @param float $default_vat_rate
	 */
	public function setDefaultVatRate( float $default_vat_rate ): void
	{
		$this->default_vat_rate = $default_vat_rate;
	}

	/**
	 * @return string
	 */
	public function getPhoneValidationRegExp(): string
	{
		return $this->phone_validation_reg_exp;
	}

	/**
	 * @param string $phone_validation_reg_exp
	 */
	public function setPhoneValidationRegExp( string $phone_validation_reg_exp ): void
	{
		$this->phone_validation_reg_exp = $phone_validation_reg_exp;
	}

	/**
	 * @return string
	 */
	public function getPhonePrefix(): string
	{
		return $this->phone_prefix;
	}

	/**
	 * @param string $phone_prefix
	 */
	public function setPhonePrefix( string $phone_prefix ): void
	{
		$this->phone_prefix = $phone_prefix;
	}

	/**
	 * @return int
	 */
	public function getRoundPrecision_WithoutVAT(): int
	{
		return $this->round_precision_without_VAT;
	}

	/**
	 * @param int $round_precision_without_VAT
	 */
	public function setRoundPrecisionWithoutVAT( int $round_precision_without_VAT ): void
	{
		$this->round_precision_without_VAT = $round_precision_without_VAT;
	}

	/**
	 * @return int
	 */
	public function getRoundPrecision_VAT(): int
	{
		return $this->round_precision_VAT;
	}

	/**
	 * @param int $round_precision_VAT
	 */
	public function setRoundPrecisionVAT( int $round_precision_VAT ): void
	{
		$this->round_precision_VAT = $round_precision_VAT;
	}

	/**
	 * @return int
	 */
	public function getRoundPrecision_WithVAT(): int
	{
		return $this->round_precision_with_VAT;
	}

	/**
	 * @param int $round_precision_with_VAT
	 */
	public function setRoundPrecisionWithVAT( int $round_precision_with_VAT ): void
	{
		$this->round_precision_with_VAT = $round_precision_with_VAT;
	}




}