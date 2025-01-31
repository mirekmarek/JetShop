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
use Jet\Locale;
use JetApplication\Currencies;
use JetApplication\Currency;
use JetApplication\Pricelists;

abstract class Core_Pricelist extends BaseObject implements Form_Definition_Interface
{
	use Form_Definition_Trait;
	
	#[Form_Definition(
		type: Form_Field::TYPE_INPUT,
		is_required: true,
		label: 'Internal code: ',
	)]
	protected string $code = '';
	
	#[Form_Definition(
		type: Form_Field::TYPE_SELECT,
		is_required: true,
		label: 'Currency: ',
		select_options_creator: [
			Currencies::class,
			'getScope'
		]
	)]
	protected string $currency_code = '';
	
	#[Form_Definition(
		type: Form_Field::TYPE_INPUT,
		is_required: true,
		label: 'Internal name: ',
	)]
	protected string $name = '';
	
	#[Form_Definition(
		type: Form_Field::TYPE_CHECKBOX,
		label: 'Prices are without VAT'
	)]
	protected bool $prices_are_without_vat = false;
	
	#[Form_Definition(
		type: Form_Field::TYPE_INPUT,
		is_required: true,
		label: 'VAT rates: ',
	)]
	protected array $vat_rates = [];
	
	#[Form_Definition(
		type: Form_Field::TYPE_FLOAT,
		is_required: true,
		label: 'Default VAT rate: ',
	)]
	protected float $default_vat_rate = 0.0;
	
	protected array $custom_discount_prc = [];
	protected array $custom_discount_mtp = [];
	
	protected ?Form $edit_form = null;
	protected ?Form $add_form = null;
	
	public function __construct( ?array $data=null )
	{
		if($data) {
			$this->setCode( $data['code'] );
			$this->setCurrencyCode( $data['currency_code'] );
			$this->setPricesAreWithoutVat( $data['prices_are_without_vat']??false );
			$this->setName( $data['name'] );
			$this->setVatRates( $data['vat_rates'] );
			$this->setDefaultVatRate( $data['default_vat_rate'] );
		}
	}
	
	public function toArray() : array
	{
		return [
			'code'                 => $this->code,
			'currency_code'        => $this->currency_code,
			'prices_are_without_vat' => $this->prices_are_without_vat,
			'name'                 => $this->name,
			'vat_rates'            => $this->vat_rates,
			'default_vat_rate'     => $this->default_vat_rate,
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
	
	public function getName(): string
	{
		return $this->name;
	}
	
	public function setName( string $name ): void
	{
		$this->name = $name;
	}
	
	public function getCurrencyCode(): string
	{
		return $this->currency_code;
	}
	
	public function setCurrencyCode( string $currency_code ): void
	{
		$this->currency_code = $currency_code;
	}
	
	public function getCurrency() : Currency
	{
		return Currencies::get( $this->currency_code );
	}
	
	
	public function getVatRates(): array
	{
		return $this->vat_rates;
	}
	
	public function getVatRatesScope() : array
	{
		$scope = [];
		foreach( $this->vat_rates as $rate) {
			$scope[(string)$rate] = Locale::float($rate).'%';
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
	
	public function getPricesAreWithoutVat(): bool
	{
		return $this->prices_are_without_vat;
	}
	
	public function setPricesAreWithoutVat( bool $prices_are_without_vat ): void
	{
		$this->prices_are_without_vat = $prices_are_without_vat;
	}
	


	
	
	public function round( float $price ) : float
	{
		if($this->prices_are_without_vat) {
			return $this->round_WithoutVAT( $price );
		} else {
			return $this->round_WithVAT( $price );
		}
	}
	
	public function round_WithVAT( float $price ) : float
	{
		return round( $price, $this->getCurrency()->getRoundPrecision_WithVAT() );
	}
	
	public function round_VAT( float $price ) : float
	{
		return round( $price, $this->getCurrency()->getRoundPrecision_VAT() );
	}
	
	public function round_WithoutVAT( float $price ) : float
	{
		return round( $price, $this->getCurrency()->getRoundPrecision_WithoutVAT() );
	}
	
	
	public function getRoundPrecision() : int
	{
		if($this->prices_are_without_vat) {
			return $this->getRoundPrecision_WithoutVAT();
		} else {
			return $this->getRoundPrecision_WithVAT();
		}
	}
	
	public function getRoundPrecision_WithVAT() : float
	{
		return $this->getCurrency()->getRoundPrecision_WithVAT();
	}
	
	public function getRoundPrecision_VAT() : float
	{
		return $this->getCurrency()->getRoundPrecision_VAT();
	}
	
	public function getRoundPrecision_WithoutVAT() : float
	{
		return $this->getCurrency()->getRoundPrecision_WithoutVAT();
	}
	
	protected function updateForm( Form $form ) : void
	{
		$vat_rates = $form->field('vat_rates');
		
		$vat_rates->setDefaultValue( implode(';', $this->vat_rates) );
		$vat_rates->setFieldValueCatcher( function( string $value ) {
			$value = explode(';', $value);
			foreach($value as $i=>$v) {
				$v = str_replace(',', '.', $v);
				$value[$i] = (float)$v;
			}
			$this->setVatRates( $value );
			
		} );
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
				if(Pricelists::exists( $code->getValue() )) {
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
	
	public function setCustomDiscountPrc( string $price_entity_class, float $custom_discount_prc ): void
	{
		$this->custom_discount_prc[$price_entity_class] = $custom_discount_prc;
		$this->custom_discount_mtp[$price_entity_class] = (100 - $custom_discount_prc)/100;
	}
	
	public function getCustomDiscountPrc( string $price_entity_class ): float
	{
		return $this->custom_discount_prc[$price_entity_class]??0.0;
	}
	
	public function getCustomDiscountMtp( string $price_entity_class ): float
	{
		return $this->custom_discount_mtp[$price_entity_class]??0.0;
	}
	
}