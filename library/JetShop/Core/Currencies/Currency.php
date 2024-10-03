<?php
namespace JetShop;


use Jet\Form;
use Jet\Form_Definition;
use Jet\Form_Definition_Interface;
use Jet\Form_Definition_Trait;
use Jet\Form_Field;
use Jet\Form_Field_Float;
use JetApplication\Currencies;
use JetApplication\Currencies_Currency;

abstract class Core_Currencies_Currency implements Form_Definition_Interface
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
	)]
	protected string $symbol_left = '';
	
	#[Form_Definition(
		type: Form_Field::TYPE_INPUT,
		is_required: false,
		label: 'Currency symbol - after: ',
	)]
	protected string $symbol_right = '';
	
	
	#[Form_Definition(
		type: Form_Field::TYPE_INPUT,
		is_required: false,
		label: 'Decimal separator: ',
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
		label: 'Decimal places: ',
	)]
	protected int $decimal_places = 0;
	
	#[Form_Definition(
		type: Form_Field::TYPE_INT,
		is_required: true,
		label: 'Round precision - without VAT: ',
	)]
	protected int $round_precision_without_VAT = 0;
	
	#[Form_Definition(
		type: Form_Field::TYPE_INT,
		is_required: true,
		label: 'Round precision - VAT: ',
	)]
	protected int $round_precision_VAT = 0;
	
	#[Form_Definition(
		type: Form_Field::TYPE_INT,
		is_required: true,
		label: 'Round precision - with VAT: ',
	)]
	protected int $round_precision_with_VAT = 0;
	
	
	protected array $exchange_rates = [];
	
	
	protected ?Form $edit_form = null;
	protected ?Form $add_form = null;
	
	public function __construct( ?array $data=null )
	{
		if($data) {
			$this->setCode( $data['code'] );
			
			$this->setSymbolLeft( $data['symbol_left'] );
			$this->setSymbolRight( $data['symbol_right'] );
			
			$this->setDecimalSeparator( $data['decimal_separator'] );
			$this->setThousandsSeparator( $data['thousands_separator'] );
			$this->setDecimalPlaces( $data['decimal_places'] );

			$this->setRoundPrecisionWithVAT( $data['round_precision_with_VAT'] );
			$this->setRoundPrecisionVAT( $data['round_precision_VAT'] );
			$this->setRoundPrecisionWithoutVAT( $data['round_precision_without_VAT'] );
			
			$this->setExchangeRates( $data['exchange_rates']??[] );
		}
	}
	
	public function toArray() : array
	{
		return [
			'code' => $this->code,
			
			'symbol_left'  => $this->symbol_left,
			'symbol_right' => $this->symbol_right,
			
			'decimal_separator'   => $this->decimal_separator,
			'thousands_separator' => $this->thousands_separator,
			'decimal_places'      => $this->decimal_places,
			
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

	public function getSymbolLeft(): string
	{
		if(!$this->symbol_left) {
			return '';
		}
		return $this->symbol_left.' ';
	}

	public function setSymbolLeft( string $symbol_left ): void
	{
		$this->symbol_left = $symbol_left;
	}

	public function getSymbolRight(): string
	{
		if(!$this->symbol_right) {
			return '';
		}
		return ' '.$this->symbol_right;
	}

	public function setSymbolRight( string $symbol_right ): void
	{
		$this->symbol_right = $symbol_right;
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
		return $this->thousands_separator;
	}

	public function setThousandsSeparator( string $thousands_separator ): void
	{
		$this->thousands_separator = $thousands_separator;
	}

	public function getDecimalPlaces(): int
	{
		return $this->decimal_places;
	}

	public function setDecimalPlaces( int $decimal_places ): void
	{
		$this->decimal_places = $decimal_places;
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
	
	public function getExchangeRate( Currencies_Currency $to_currency ): float
	{
		return $this->exchange_rates[$to_currency->getCode()]??1.0;
	}
	
	public function setExchangeRate( Currencies_Currency $to_currency, float $exchange_rate ): void
	{
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