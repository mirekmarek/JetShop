<?php
namespace JetShop;

use Jet\Form;
use Jet\Form_Definition;
use Jet\Form_Definition_Interface;
use Jet\Form_Definition_Trait;
use Jet\Form_Field;
use Jet\Form_Field_Input;
use Jet\IO_File;
use Jet\Locale;
use Jet\SysConf_Path;
use JetApplication\MeasureUnit;
use JetApplication\Shops;

abstract class Core_MeasureUnit implements Form_Definition_Interface
{
	use Form_Definition_Trait;
	
	protected static ?array $list = null;
	
	#[Form_Definition(
		type: Form_Field::TYPE_INPUT,
		is_required: true,
		label: 'Code: ',
	)]
	protected string $code = '';
	
	protected array $name = [];
	
	
	#[Form_Definition(
		type: Form_Field::TYPE_CHECKBOX,
		label: 'Is decimal number',
	)]
	protected bool $is_decimal_number = false;
	
	#[Form_Definition(
		type: Form_Field::TYPE_FLOAT,
		label: 'Number step:',
	)]
	protected float $number_step = 1;
	
	#[Form_Definition(
		type: Form_Field::TYPE_INT,
		label: 'Decimal places:',
	)]
	protected int $decimal_places = 0;
	
	
	protected ?Form $edit_form = null;
	protected ?Form $add_form = null;
	
	
	public static function getCfgFilePath() : string
	{
		return SysConf_Path::getConfig().'shop/measure_units.php';
	}
	
	public static function loadCfg() : void
	{
		static::$list = [];
		
		$cfg = require static::getCfgFilePath();
		
		foreach($cfg as $item) {
			static::add( (new static( $item )) );
		}
	}
	
	public static function saveCfg() : void
	{
		$cfg = [];
		
		foreach( static::getList() as $item ) {
			$cfg[] = $item->toArray();
		}
		
		IO_File::writeDataAsPhp(
			static::getCfgFilePath(),
			$cfg
		);
	}
	
	
	public function __construct( ?array $data=null )
	{
		$this->name = [];
		foreach(Shops::getList() as $shop) {
			$locale = $shop->getLocale()->toString();
			if(!isset($this->name[$locale])) {
				$this->name[$locale] = '';
			}
		}
		
		if($data) {
			$this->code = $data['code'];
			
			if(is_array($data['name'])) {
				foreach($this->name as $locale => $name) {
					$this->name[$locale] = $data['name'][$locale]??'';
				}
			}
			
			$this->is_decimal_number = !empty($data['is_decimal_number']);
			if($this->is_decimal_number) {
				$this->number_step = (float)$data['number_step']??1;
				$this->decimal_places = (int)$data['decimal_places']??1;
			} else {
				$this->number_step = 0;
				$this->decimal_places = 0;
			}
			
		}
	}
	
	public static function add( Core_MeasureUnit $unit ) : void
	{
		static::$list[$unit->code] = $unit;
	}
	
	public static function remove( string $code ) : void
	{
		if(isset(static::$list[$code])) {
			unset( static::$list[$code] );
		}
	}
	
	
	public static function get( string $code ) : ?static
	{
		static::getList();
		
		return static::$list[$code]??null;
	}
	
	public static function getScope() : array
	{
		static::getList();
		$res = [];
		foreach(static::$list as $unit) {
			$res[$unit->getCode()] = $unit->getName();
		}
		
		return $res;
	}
	
	/**
	 * @return static[]
	 */
	public static function getList() : array
	{
		if(static::$list===null) {
			static::loadCfg();
		}
		
		return static::$list;
	}
	
	
	public function getCode(): string
	{
		return $this->code;
	}
	
	public function getName( ?Locale $locale=null ): string
	{
		if(!$locale) {
			$locale = Locale::getCurrentLocale();
		}
		
		return $this->name[$locale->toString()]??$this->code;
	}
	
	public function getNames() : array
	{
		return $this->name;
	}
	
	public function toString() : string
	{
		return $this->code;
	}
	
	public function isIsDecimalNumber(): bool
	{
		return $this->is_decimal_number;
	}
	
	public function getNumberStep(): float
	{
		return $this->number_step;
	}
	
	public function getDecimalPlaces(): float
	{
		return $this->decimal_places;
	}
	
	public function round( float|int $number_of_units ) : float|int
	{
		if($this->is_decimal_number) {
			return round( $number_of_units, $this->decimal_places );
		} else {
			return floor( $number_of_units );
		}
	}
	
	public function __toString() : string
	{
		return $this->toString();
	}
	
	public function toArray() : array
	{
		return [
			'code'              => $this->code,
			'name'              => $this->name,
			'is_decimal_number' => $this->is_decimal_number,
			'number_step'       => $this->number_step,
			'decimal_places'    => $this->decimal_places,
		];
	}
	
	protected function updateForm( Form $form ) : void
	{
		foreach($this->name as $locale=>$name) {
			$name_input = new Form_Field_Input('/name/'.$locale, '');
			$name_input->setDefaultValue( $name );
			$name_input->setFieldValueCatcher( function( string $name ) use ($locale) {
				$this->name[$locale] = $name;
			} );
			
			$form->addField( $name_input );
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
				if(MeasureUnit::get( $code->getValue() )) {
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