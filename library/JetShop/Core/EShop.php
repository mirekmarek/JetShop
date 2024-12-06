<?php
namespace JetShop;

use Jet\Form;
use Jet\Form_Definition;
use Jet\Form_Definition_Interface;
use Jet\Form_Definition_Trait;
use Jet\Form_Field;
use Jet\Form_Field_Select;
use Jet\Form_Field_Textarea;
use Jet\Locale;
use Jet\MVC;
use Jet\MVC_Base_Interface;
use Jet\MVC_Page_Interface;
use Jet\SysConf_URI;
use JetApplication\Application_Admin;
use JetApplication\Application_Exports;
use JetApplication\Application_Services;
use JetApplication\Availabilities;
use JetApplication\Availability;
use JetApplication\EShop_Pages;
use JetApplication\Pricelists;
use JetApplication\Pricelist;
use JetApplication\EShop_Template;
use JetApplication\EShops;
use Error;
use JetApplication\WarehouseManagement_Warehouse;


abstract class Core_EShop implements Form_Definition_Interface
{
	use Form_Definition_Trait;
	
	#[Form_Definition(
		type: Form_Field::TYPE_INPUT,
		is_required: true,
		label: 'Internal code: ',
	)]
	protected string $code = '';
	
	protected string $_base_id = '';
	
	protected ?Locale $_locale = null;
	
	#[Form_Definition(
		type: Form_Field::TYPE_INPUT,
		is_required: true,
		label: 'Internal name: ',
	)]
	protected string $name = '';
	
	#[Form_Definition(
		type: Form_Field::TYPE_CHECKBOX,
		label: 'Is default e-shop',
	)]
	protected bool $is_default = false;
	
	
	#[Form_Definition(
		type: Form_Field::TYPE_MULTI_SELECT,
		is_required: true,
		label: 'Price lists: ',
		select_options_creator: [
			Pricelists::class,
			'getScope'
		],
	)]
	protected array $pricelist_codes = [];
	
	#[Form_Definition(
		type: Form_Field::TYPE_SELECT,
		is_required: true,
		label: 'Default price lists: ',
		select_options_creator: [
			Pricelists::class,
			'getScope'
		],
	)]
	protected string $default_pricelist_code = '';
	
	#[Form_Definition(
		type: Form_Field::TYPE_MULTI_SELECT,
		is_required: true,
		label: 'Availabilities: ',
		select_options_creator: [
			Availabilities::class,
			'getScope'
		],
	)]
	protected array $availability_codes = [];
	
	#[Form_Definition(
		type: Form_Field::TYPE_SELECT,
		is_required: true,
		label: 'Default availability: ',
		select_options_creator: [
			Availabilities::class,
			'getScope'
		],
	)]
	protected string $default_availability_code = '';
	
	
	#[Form_Definition(
		type: Form_Field::TYPE_SELECT,
		is_required: true,
		label: 'Default warehouse: ',
		select_options_creator: [
			WarehouseManagement_Warehouse::class,
			'getScope'
		],
	)]
	protected string $default_warehouse_id = '';
	
	
	#[Form_Definition(
		type: Form_Field::TYPE_CHECKBOX,
		label: 'Use template',
	)]
	protected bool $use_template = true;
	
	#[Form_Definition(
		type: Form_Field::TYPE_INPUT,
		is_required: true,
		label: 'Template relative dir:',
	)]
	protected string $template_relative_dir = 'default';
	
	#[Form_Definition(
		type: Form_Field::TYPE_CHECKBOX,
		label: 'Is active',
	)]
	protected bool $_is_active = false;
	
	protected array $_URLs = [];
	
	protected ?Form $_edit_form = null;
	protected ?Form $_add_form = null;
	
	
	public static function init( MVC_Base_Interface $base ) : array
	{
		$res = [];
		foreach($base->getLocales() as $locale) {
			$ld = $base->getLocalizedData( $locale );
			if(!$ld->getParameter('code', '')) {
				continue;
			}

			$item = new static();
			$item->_base_id = $base->getId();
			$item->_locale = $locale;
			
			$item->_is_active = $base->getLocalizedData( $locale )->getIsActive();
			$item->_URLs = $base->getLocalizedData( $locale )->getURLs();

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
			$item->initEShop();
		}

		return $res;
	}
	
	public function save() : void
	{
		$base = MVC::getBase( $this->getBaseId() );
		$localized = $base->getLocalizedData( $this->getLocale() );
		
		foreach(get_object_vars($this) as $var=>$value) {
			if( $var[0] == '_' ) {
				continue;
			}
			
			if(is_array($value)) {
				$value = implode(',', $value);
			}
			
			$localized->setParameter( $var, $value );
		}
		
		$localized->setURLs( $this->_URLs );
		$localized->setIsActive( $this->_is_active );
		
		$base->saveDataFile();
	}
	
	protected function initEShop(): void
	{
	}
	
	public static function generateKey( string $code, Locale $locale ) : string
	{
		return $code.'_'.$locale;
	}

	public function getKey() : string
	{
		return static::generateKey( $this->code, $this->_locale );
	}

	public function getWhere( string $prefix='' ) : array
	{
		return [
			$prefix.'eshop_code' => $this->code,
			'AND',
			$prefix.'locale' => $this->_locale
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

	public function getLocale(): Locale
	{
		return new Locale($this->_locale);
	}

	public function setLocale( Locale $locale ): void
	{
		$this->_locale = $locale;
	}
	
	public function isIsActive(): bool
	{
		return $this->_is_active;
	}
	
	public function setIsActive( bool $is_active ): void
	{
		$this->_is_active = $is_active;
	}
	
	public function getURLs(): array
	{
		return $this->_URLs;
	}
	
	public function setURLs( array $URLs ): void
	{
		$this->_URLs = $URLs;
	}

	

	public function getName(): string
	{
		return $this->name;
	}

	public function setName( string $name ): void
	{
		$this->name = $name;
	}

	public function getIsDefault(): bool
	{
		return $this->is_default;
	}

	public function setIsDefault( bool $is_default ): void
	{
		$this->is_default = $is_default;
	}
	
	public function getDefaultWarehouseId(): int
	{
		return $this->default_warehouse_id;
	}
	
	public function setDefaultWarehouseId( int $default_warehouse_id ): void
	{
		$this->default_warehouse_id = $default_warehouse_id;
	}



	public function getBaseId(): string
	{
		return $this->_base_id;
	}

	public function setBaseId( string $_base_id ): void
	{
		$this->_base_id = $_base_id;
	}

	public function getHomepage() : MVC_Page_Interface
	{
		return MVC::getBase( $this->getBaseId() )->getHomepage( $this->getLocale() );
	}
	
	
	public function getURL( array $path_fragments = [], array $GET_params = [] ) : string
	{
		$base = MVC::getBase( $this->getBaseId() );
		
		return $base->getHomepage( $this->getLocale() )->getURL( $path_fragments, $GET_params );
	}

	public function setPricelistCodes( array $pricelist_codes ): void
	{
		$this->pricelist_codes = $pricelist_codes;
	}
	
	/**
	 * @return Pricelist[]
	 */
	public function getPricelists(): array
	{
		$pricelists = [];
		foreach($this->pricelist_codes as $code) {
			$pricelists[$code] = Pricelists::get( $code );
		}
		
		return $pricelists;
	}
	
	public function setDefaultPricelistCode( string $default_pricelist_code ): void
	{
		$this->default_pricelist_code = $default_pricelist_code;
	}
	
	public function getDefaultPricelist() : Pricelist
	{
		return Pricelists::get( $this->default_pricelist_code );
	}
	
	
	public function setAvailabilityCodes( array $availability_codes ): void
	{
		$this->availability_codes = $availability_codes;
	}
	
	/**
	 * @return Availability[]
	 */
	public function getAvailabilities() : array
	{
		$availabilities = [];
		
		foreach($this->availability_codes as $code) {
			$availabilities[$code] = Availabilities::get( $code );
		}
		
		return $availabilities;
	}

	
	public function setDefaultAvailabilityCode( string $default_availability_code ): void
	{
		$this->default_availability_code = $default_availability_code;
	}
	
	
	public function getDefaultAvailability(): Availability
	{
		return Availabilities::get( $this->default_availability_code );
	}
	
	
	public function getUseTemplate(): bool
	{
		return $this->use_template;
	}
	
	public function setUseTemplate( bool $use_template ): void
	{
		$this->use_template = $use_template;
	}
	
	public function getTemplateRelativeDir(): string
	{
		return $this->template_relative_dir;
	}
	
	public function setTemplateRelativeDir( string $template_relative_dir ): void
	{
		$this->template_relative_dir = $template_relative_dir;
	}
	
	public function getTemplate() : EShop_Template
	{
		return new EShop_Template( $this->getTemplateRelativeDir() );
	}
	
	protected function updateForm( Form $form ) : void
	{
		$URLs_field = new Form_Field_Textarea(name: 'URLs',label: 'URLs:');
		$URLs_field->setDefaultValue( implode("\n", $this->_URLs) );
		$URLs_field->setErrorMessages([
			'not_unique_URL' => 'URL %URL% is not unique. It is already used by e-shop %SHOP%',
			'invalid_URL' => 'URL %URL% is not valid'
		]);
		$URLs_field->setValidator( function() use ($URLs_field) : bool {
			$URLs = explode("\n", $URLs_field->getValue());
			
			$known_URL = [];
			foreach( EShops::getList() as $eshop) {
				if($this->_locale && $this->code) {
					if($eshop->getKey()==$this->getKey()) {
						continue;
					}
				}
				
				foreach($eshop->_URLs as $URL) {
					$known_URL[$URL] = $eshop;
				}
			}
			
			$has_some_URL = false;
			foreach($URLs as $URL) {
				$URL = trim($URL);
				if(!$URL) {
					continue;
				}
				
				$has_some_URL = true;
				
				if(isset($known_URL[$URL])) {
					$URLs_field->setError('not_unique_URL', [
						'URL' => $URL,
						'SHOP' => $known_URL[$URL]->getName()
					]);
					return false;
				}
				
				if(!filter_var('https://'.$URL, FILTER_VALIDATE_URL)) {
					$URLs_field->setError('invalid_URL', [
						'URL' => $URL
					]);
					return false;
				}
			}
			
			if(!$has_some_URL) {
				$URLs_field->setError( Form_Field::ERROR_CODE_EMPTY );
				return false;
			}
			
			return true;
		} );
		$URLs_field->setFieldValueCatcher( function( $URLs ) {
			$this->_URLs = [];
			$URLs = explode("\n", $URLs);
			
			foreach($URLs as $URL) {
				$URL = trim( $URL );
				if( !$URL ) {
					continue;
				}
				
				$this->_URLs[] = $URL;
			}
		} );
		
		$form->addField( $URLs_field );
	}
	
	public function getEditForm() : Form
	{
		if( $this->_edit_form===null ) {
			$form = $this->createForm( 'cfg_form' );
			
			$form->field('code')->setIsReadonly( true );
			
			
			$this->updateForm( $form );
			
			$this->_edit_form = $form;
		}
		
		return $this->_edit_form;
	}
	
	
	public function getCreateForm() : Form
	{
		if( $this->_add_form===null ) {
			$form = $this->createForm( 'cfg_form' );
			
			$bases_scope = [];
			foreach(MVC::getBases() as $base) {
				if(!in_array($base->getId(), [
					Application_Exports::getBaseId(),
					Application_Services::getBaseId(),
					Application_Admin::getBaseId()
				])) {
					$bases_scope[$base->getId()] = $base->getName();
				}
			}
			
			
			
			
			$base_field = new Form_Field_Select('base', 'MVC Base:');
			$base_field->setSelectOptions( $bases_scope );
			$base_field->setFieldValueCatcher( function( string $value ) {
				$this->setBaseId( $value );
			} );
			$form->addField( $base_field );
			
			$locale_field = new Form_Field_Select('locale', 'Locale:');
			$locale_field->setSelectOptions( Locale::getAllLocalesList() );
			$locale_field->setFieldValueCatcher( function( string $value ) {
				$this->setLocale( new Locale($value) );
			} );
			$locale_field->setErrorMessages([
				'not_unique_locale' => 'Such a e-shop already exists (%shop%)'
			]);
			$locale_field->setValidator( function() use ($locale_field, $base_field) {
				$selected_base_id = $base_field->getValue();
				$selected_locale = $locale_field->getValue();
				
				foreach( EShops::getList() as $eshop) {
					if(
						$eshop->getBaseId()==$selected_base_id &&
						$eshop->getLocale()->toString()==$selected_locale
					) {
						$locale_field->setError('not_unique_locale', ['eshop'=>$eshop->getName()]);
						return false;
					}
				}
				
				return true;
				
			} );
			$form->addField( $locale_field );
			
			
			
			
			$code_field = $form->field('code');
			$code_field->setErrorMessages([
				Form_Field::ERROR_CODE_EMPTY => 'Please enter code',
				'not_unique' => 'This code is already used'
			]);
			$code_field->setIsRequired(true);
			$code_field->setFieldValueCatcher( function( $value ) {
				$this->code = $value;
			} );
			$code_field->setValidator( function() use ($code_field, $locale_field) : bool {
				$code = $code_field->getValue();
				$locale = new Locale( $locale_field->getValue() );
				
				$key = $code.'_'.$locale;
				
				if(EShops::exists( $key )) {
					$code->setError('not_unique');
					return false;
				}
				
				return true;
			} );
			
			
			$this->updateForm( $form );
			
			$this->_add_form = $form;
		}
		
		return $this->_add_form;
	}
	
	public function catchCreateForm( string &$error_message ) : bool
	{
		$form = $this->getCreateForm();
		if(!$form->catch()) {
			return false;
		}
		
		try {
			$base = MVC::getBase( $this->_base_id );
			$locale = $this->getLocale();
			
			if(!$base->getHasLocale($locale)) {
				$base->addLocale( $locale );
				$base->saveDataFile();
			}
			
			
			EShop_Pages::createPages( $this );
			
			$this->save();
			
		} catch( Error $e ) {
			$error_message = $e->getMessage();
			
			return false;
		}
		
		return true;
	}
	
	
	public function getCssURI() : string
	{
		if(!$this->getUseTemplate()) {
			return SysConf_URI::getCss().'eshop/';
		}
		
		return $this->getTemplate()->getCssUrl();
	}
	
	
	public function getJsURI() : string
	{
		if(!$this->getUseTemplate()) {
			return SysConf_URI::getJs().'eshop/';
		}
		
		return $this->getTemplate()->getJsUrl();
	}
	
	public function getImagesURI() : string
	{
		if(!$this->getUseTemplate()) {
			return SysConf_URI::getImages().'eshop/';
		}
		
		return $this->getTemplate()->getImagesUrl();
	}
	
}