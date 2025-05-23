<?php
/**
 * @copyright Copyright (c) Miroslav Marek <mirek.marek@web-jet.cz>
 * @license EUPL 1.2  https://eupl.eu/1.2/en/
 * @author Miroslav Marek <mirek.marek@web-jet.cz>
 */
namespace JetShop;



use Jet\BaseObject;
use Jet\Form;
use Jet\Form_Definition;
use Jet\Form_Definition_Interface;
use Jet\Form_Definition_Trait;
use Jet\Form_Field;
use Jet\Form_Field_Float;
use JetApplication\Currencies;
use JetApplication\Currency;

abstract class Core_Currency extends BaseObject implements Form_Definition_Interface
{
	use Form_Definition_Trait;
	
	#[Form_Definition(
		type: Form_Field::TYPE_INPUT,
		is_required: true,
		label: 'Code (ISO 4217): ',
	)]
	protected string $code = '';
	
	#[Form_Definition(
		type: Form_Field::TYPE_INPUT,
		is_required: false,
		label: 'Currency symbol - before: ',
		default_value_getter_name: 'getSymbolLeft_WithVAT',
		setter_name: 'setSymbolLeft_WithVAT',
		
	)]
	protected string $symbol_left_with_VAT = '';
	
	#[Form_Definition(
		type: Form_Field::TYPE_INPUT,
		is_required: false,
		label: 'Currency symbol - before: ',
		default_value_getter_name: 'getSymbolLeft_WithoutVAT',
		setter_name: 'setSymbolLeft_WithoutVAT',
	)]
	protected string $symbol_left_without_VAT = '';
	
	#[Form_Definition(
		type: Form_Field::TYPE_INPUT,
		is_required: false,
		label: 'Currency symbol - before: ',
		default_value_getter_name: 'getSymbolLeft_VAT',
		setter_name: 'setSymbolLeft_VAT',
	)]
	protected string $symbol_left_VAT = '';

	#[Form_Definition(
		type: Form_Field::TYPE_INPUT,
		is_required: false,
		label: 'Currency symbol - after: ',
		default_value_getter_name: 'getSymbolRight_WithVAT',
		setter_name: 'getSymbolRight_WithVAT',
	)]
	protected string $symbol_right_with_VAT = '';
	
	#[Form_Definition(
		type: Form_Field::TYPE_INPUT,
		is_required: false,
		label: 'Currency symbol - after: ',
		default_value_getter_name: 'getSymbolRight_WithoutVAT',
		setter_name: 'getSymbolRight_WithoutVAT',
	)]
	protected string $symbol_right_without_VAT = '';
	
	#[Form_Definition(
		type: Form_Field::TYPE_INPUT,
		is_required: false,
		label: 'Currency symbol - after: ',
		default_value_getter_name: 'getSymbolRight_VAT',
		setter_name: 'getSymbolRight_VAT',
	)]
	protected string $symbol_right_VAT = '';
	
	
	#[Form_Definition(
		type: Form_Field::TYPE_INPUT,
		is_required: false,
		label: 'Decimal separator: ',
		default_value_getter_name: 'getDecimalSeparator',
		setter_name: 'setDecimalSeparator',
	)]
	protected string $decimal_separator = '';
	
	#[Form_Definition(
		type: Form_Field::TYPE_INPUT,
		is_required: false,
		label: 'Thousands separator: ',
	)]
	protected string $thousands_separator = '';
	
	#[Form_Definition(
		type: Form_Field::TYPE_INT,
		is_required: true,
		label: 'Decimal places:',
		default_value_getter_name: 'getDecimalPlaces_WithVAT',
		setter_name: 'setDecimalPlaces_WithVAT',
	)]
	protected int $decimal_places_with_VAT = 0;
	
	#[Form_Definition(
		type: Form_Field::TYPE_INT,
		is_required: true,
		label: 'Decimal places: ',
		default_value_getter_name: 'getDecimalPlaces_WithoutVAT',
		setter_name: 'setDecimalPlaces_WithoutVAT',
	)]
	protected int $decimal_places_without_VAT = 0;
	
	#[Form_Definition(
		type: Form_Field::TYPE_INT,
		is_required: true,
		label: 'Decimal places: ',
		default_value_getter_name: 'getDecimalPlaces_VAT',
		setter_name: 'setDecimalPlaces_VAT',
	)]
	protected int $decimal_places_VAT = 0;
	
	#[Form_Definition(
		type: Form_Field::TYPE_INT,
		is_required: true,
		label: 'Round precision: ',
		default_value_getter_name: 'getRoundPrecision_WithoutVAT',
		setter_name: 'setRoundPrecision_WithoutVAT',
	)]
	protected int $round_precision_without_VAT = 0;
	
	#[Form_Definition(
		type: Form_Field::TYPE_INT,
		is_required: true,
		label: 'Round precision: ',
		default_value_getter_name: 'getRoundPrecision_VAT',
		setter_name: 'setRoundPrecision_VAT',
	)]
	protected int $round_precision_VAT = 0;
	
	#[Form_Definition(
		type: Form_Field::TYPE_INT,
		is_required: true,
		label: 'Round precision: ',
		default_value_getter_name: 'getRoundPrecision_WithVAT',
		setter_name: 'setRoundPrecision_WithVAT',
	)]
	protected int $round_precision_with_VAT = 0;
	
	
	protected array $exchange_rates = [];
	
	
	protected ?Form $edit_form = null;
	protected ?Form $add_form = null;
	
	public function __construct( ?array $data=null )
	{
		if($data) {
			$this->setCode( $data['code'] );
			
			$this->setDecimalSeparator( $data['decimal_separator']??'' );
			$this->setThousandsSeparator( $data['thousands_separator']??'' );
			
			
			$this->setSymbolLeft_WithVAT( $data['symbol_left_with_VAT']??'' );
			$this->setSymbolLeft_WithoutVAT( $data['symbol_left_without_VAT']??'' );
			$this->setSymbolLeft_VAT( $data['symbol_left_VAT']??'' );
			
			$this->setSymbolRight_WithVAT( $data['symbol_right_with_VAT']??'' );
			$this->setSymbolRight_WithoutVAT( $data['symbol_right_without_VAT']??'' );
			$this->setSymbolRight_VAT( $data['symbol_right_VAT']??'' );
			
			$this->setDecimalPlaces_WithVAT( $data['decimal_places_with_VAT']??0 );
			$this->setDecimalPlaces_WithoutVAT( $data['decimal_places_without_VAT']??0 );
			$this->setDecimalPlaces_VAT( $data['decimal_places_VAT']??0 );

			$this->setRoundPrecision_WithVAT( $data['round_precision_with_VAT']??0 );
			$this->setRoundPrecision_VAT( $data['round_precision_VAT']??0 );
			$this->setRoundPrecision_WithoutVAT( $data['round_precision_without_VAT']??0 );
			
			$this->setExchangeRates( $data['exchange_rates']??[] );
		}
	}
	
	public function toArray() : array
	{
		return [
			'code' => $this->code,
			
			'symbol_left_with_VAT'    => $this->symbol_left_with_VAT,
			'symbol_left_without_VAT' => $this->symbol_left_without_VAT,
			'symbol_left_VAT'         => $this->symbol_left_VAT,
			
			'symbol_right_with_VAT'    => $this->symbol_right_with_VAT,
			'symbol_right_without_VAT' => $this->symbol_right_without_VAT,
			'symbol_right_VAT'         => $this->symbol_right_VAT,
			
			'decimal_separator'   => $this->decimal_separator,
			'thousands_separator' => $this->thousands_separator,
			
			'decimal_places_with_VAT'     => $this->decimal_places_with_VAT,
			'decimal_places_without_VAT'  => $this->decimal_places_without_VAT,
			'decimal_places_VAT'          => $this->decimal_places_VAT,
			
			'round_precision_without_VAT' => $this->round_precision_without_VAT,
			'round_precision_VAT'         => $this->round_precision_VAT,
			'round_precision_with_VAT'    => $this->round_precision_with_VAT,
		
			'exchange_rates' => $this->exchange_rates,
			
		];
	}
	
	
	public function getCode(): string
	{
		return $this->code;
	}

	public function setCode( string $code ): void
	{
		$this->code = $code;
	}
	
	protected function _format( string $text ) : string
	{
		return str_replace('_', ' ', $text);
	}
	
	public function getSymbolLeft_WithVAT(): string
	{
		if(!$this->symbol_left_with_VAT) {
			return '';
		}
		return $this->_format( $this->symbol_left_with_VAT );
	}
	
	public function setSymbolLeft_WithVAT( string $symbol_left_with_VAT ): void
	{
		$this->symbol_left_with_VAT = $symbol_left_with_VAT;
	}
	
	public function getSymbolLeft_WithoutVAT(): string
	{
		if(!$this->symbol_left_without_VAT) {
			return '';
		}
		return $this->_format( $this->symbol_left_without_VAT );
	}
	
	public function setSymbolLeft_WithoutVAT( string $symbol_left_without_VAT ): void
	{
		$this->symbol_left_without_VAT = $symbol_left_without_VAT;
	}
	
	public function getSymbolLeft_VAT(): string
	{
		if(!$this->symbol_left_VAT) {
			return '';
		}
		return $this->_format( $this->symbol_left_VAT );
	}
	
	public function setSymbolLeft_VAT( string $symbol_left_VAT ): void
	{
		$this->symbol_left_VAT = $symbol_left_VAT;
	}
	
	public function getSymbolRight_WithVAT(): string
	{
		if(!$this->symbol_right_with_VAT) {
			return '';
		}
		return $this->_format( $this->symbol_right_with_VAT );
	}
	
	public function setSymbolRight_WithVAT( string $symbol_right_with_VAT ): void
	{
		$this->symbol_right_with_VAT = $symbol_right_with_VAT;
	}
	
	public function getSymbolRight_WithoutVAT(): string
	{
		if(!$this->symbol_right_without_VAT) {
			return '';
		}
		return $this->_format( $this->symbol_right_without_VAT );
	}
	
	public function setSymbolRight_WithoutVAT( string $symbol_right_without_VAT ): void
	{
		$this->symbol_right_without_VAT = $symbol_right_without_VAT;
	}
	
	public function getSymbolRight_VAT(): string
	{
		if(!$this->symbol_right_VAT) {
			return '';
		}
		return $this->_format( $this->symbol_right_VAT );
	}
	
	public function setSymbolRight_VAT( string $symbol_right_VAT ): void
	{
		$this->symbol_right_VAT = $symbol_right_VAT;
	}

	
	public function getDecimalSeparator(): string
	{
		return $this->decimal_separator;
	}

	public function setDecimalSeparator( string $decimal_separator ): void
	{
		$this->decimal_separator = $decimal_separator;
	}

	public function getThousandsSeparator(): string
	{
		return $this->_format( $this->thousands_separator );
	}

	public function setThousandsSeparator( string $thousands_separator ): void
	{
		$this->thousands_separator = $thousands_separator;
	}
	
	public function getDecimalPlaces_WithVAT(): int
	{
		return $this->decimal_places_with_VAT;
	}
	
	public function setDecimalPlaces_WithVAT( int $decimal_places_with_VAT ): void
	{
		$this->decimal_places_with_VAT = $decimal_places_with_VAT;
	}
	
	public function getDecimalPlaces_WithoutVAT(): int
	{
		return $this->decimal_places_without_VAT;
	}
	
	public function setDecimalPlaces_WithoutVAT( int $decimal_places_without_VAT ): void
	{
		$this->decimal_places_without_VAT = $decimal_places_without_VAT;
	}
	
	public function getDecimalPlaces_VAT(): int
	{
		return $this->decimal_places_VAT;
	}
	
	public function setDecimalPlaces_VAT( int $decimal_places_VAT ): void
	{
		$this->decimal_places_VAT = $decimal_places_VAT;
	}

	
	
	public function getRoundPrecision_WithoutVAT(): int
	{
		return $this->round_precision_without_VAT;
	}

	public function setRoundPrecision_WithoutVAT( int $round_precision_without_VAT ): void
	{
		$this->round_precision_without_VAT = $round_precision_without_VAT;
	}

	public function getRoundPrecision_VAT(): int
	{
		return $this->round_precision_VAT;
	}

	public function setRoundPrecision_VAT( int $round_precision_VAT ): void
	{
		$this->round_precision_VAT = $round_precision_VAT;
	}

	public function getRoundPrecision_WithVAT(): int
	{
		return $this->round_precision_with_VAT;
	}
	
	public function setRoundPrecision_WithVAT( int $round_precision_with_VAT ): void
	{
		$this->round_precision_with_VAT = $round_precision_with_VAT;
	}
	
	public function getExchangeRate( Currency $to_currency ): float
	{
		return $this->exchange_rates[$to_currency->getCode()]?? 0;
	}
	
	
	
	public function setExchangeRate( Currency $to_currency, float $exchange_rate ): void
	{
		if(!$exchange_rate) {
			if(isset($this->exchange_rates[$to_currency->getCode()])) {
				unset( $this->exchange_rates[$to_currency->getCode()] );
			}
			return;
		}
		$this->exchange_rates[$to_currency->getCode()] = $exchange_rate;
	}
	
	
	public function setExchangeRates( array $exchange_rates ): void
	{
		$this->exchange_rates = $exchange_rates;
	}
	
	
	protected function updateForm( Form $form ) : void
	{
		foreach( Currencies::getList() as $currency ) {
			if($currency->getCode()==$this->code) {
				continue;
			}
			
			$exchange_rate = new Form_Field_Float('/exchange_rate/'.$currency->getCode(), '');
			$exchange_rate->setDefaultValue( $this->getExchangeRate( $currency ) );
			$exchange_rate->setFieldValueCatcher( function( float $value ) use ($currency) {
				$this->setExchangeRate( $currency, $value );
			} );
			
			$form->addField( $exchange_rate );
		}
	}
	
	public function getEditForm() : Form
	{
		if( $this->edit_form===null ) {
			$form = $this->createForm( 'cfg_form' );
			
			$form->field('code')->setIsReadonly( true );
			
			$this->updateForm( $form );
			
			$this->edit_form = $form;
		}
		
		return $this->edit_form;
	}
	
	
	public function getAddForm() : Form
	{
		if( $this->add_form===null ) {
			$form = $this->createForm( 'cfg_form' );
			
			$code = $form->field('code');
			$code->setErrorMessages([
				Form_Field::ERROR_CODE_EMPTY => 'Please enter code',
				'not_unique' => 'This code is already used'
			]);
			$code->setValidator( function() use ($code) : bool {
				if(Currencies::exists( $code->getValue() )) {
					$code->setError('not_unique');
					return false;
				}
				
				return true;
			} );
			
			
			$this->updateForm( $form );
			
			$this->add_form = $form;
		}
		
		return $this->add_form;
	}
	
}