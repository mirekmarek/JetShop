<?php
/**
 * @copyright Copyright (c) Miroslav Marek <mirek.marek@web-jet.cz>
 * @license EUPL 1.2  https://eupl.eu/1.2/en/
 * @author Miroslav Marek <mirek.marek@web-jet.cz>
 */
namespace JetShop;


use Jet\Application_Module;
use Jet\Application_Modules;
use Jet\Application_Service_List;
use Jet\DataModel;
use Jet\DataModel_Definition;
use Jet\Form_Definition;
use Jet\Form_Field;
use Jet\Locale;
use JetApplication\EShopEntity_Admin_Interface;
use JetApplication\EShopEntity_Admin_Trait;
use JetApplication\Application_Service_Admin;
use JetApplication\Application_Service_Admin_Supplier;
use JetApplication\Currencies;
use JetApplication\DataList;
use JetApplication\EShopEntity_Common;
use JetApplication\FulltextSearch_IndexDataProvider;
use JetApplication\EShopEntity_Definition;
use JetApplication\EShop;
use JetApplication\Supplier;
use JetApplication\Supplier_Backend_Module;


#[DataModel_Definition(
	name: 'suppliers',
	database_table_name: 'suppliers'
)]
#[EShopEntity_Definition(
	entity_name_readable: 'Supplier',
	admin_manager_interface: Application_Service_Admin_Supplier::class
)]
abstract class Core_Supplier extends EShopEntity_Common implements
	FulltextSearch_IndexDataProvider,
	EShopEntity_Admin_Interface
{
	use EShopEntity_Admin_Trait;
	
	#[DataModel_Definition(
		type: DataModel::TYPE_BOOL,
	)]
	protected bool $is_active = false;
	
	#[DataModel_Definition(
		type: DataModel::TYPE_STRING,
		is_key: true,
		max_len: 255,
	)]
	#[Form_Definition(
		type: Form_Field::TYPE_SELECT,
		label: 'Backend module:',
		select_options_creator: [Supplier::class, 'getBackendModulesScope'],
	)]
	protected string $backend_module_name = '';
	
	
	/**
	 * @var string
	 */
	#[DataModel_Definition(
		type: DataModel::TYPE_STRING,
		max_len: 255
	)]
	#[Form_Definition(
		type: Form_Field::TYPE_INPUT,
		label: 'Company name:',
		is_required: true,
		error_messages: [
			Form_Field::ERROR_CODE_EMPTY => 'Please enter company name'
		]
	)]
	protected string $company_name = '';
	
	/**
	 * @var string
	 */
	#[DataModel_Definition(
		type: DataModel::TYPE_STRING,
		max_len: 255
	)]
	#[Form_Definition(
		type: Form_Field::TYPE_INPUT,
		label: 'Company ID:',
		is_required: false,
		error_messages: [
		]
	)]
	protected string $company_id = '';
	
	/**
	 * @var string
	 */
	#[DataModel_Definition(
		type: DataModel::TYPE_STRING,
		max_len: 255
	)]
	#[Form_Definition(
		type: Form_Field::TYPE_INPUT,
		label: 'Company VAT ID:',
		is_required: false,
		error_messages: [
		]
	)]
	protected string $company_vat_id = '';
	
	/**
	 * @var string
	 */
	#[DataModel_Definition(
		type: DataModel::TYPE_STRING,
		max_len: 255
	)]
	#[Form_Definition(
		type: Form_Field::TYPE_INPUT,
		label: 'Address - street and number:',
		is_required: false,
		error_messages: [
		]
	)]
	protected string $address_street_and_no = '';
	
	/**
	 * @var string
	 */
	#[DataModel_Definition(
		type: DataModel::TYPE_STRING,
		max_len: 255
	)]
	#[Form_Definition(
		type: Form_Field::TYPE_INPUT,
		label: 'Address - town:',
		is_required: false,
		error_messages: [
		]
	)]
	protected string $address_town = '';
	
	/**
	 * @var string
	 */
	#[DataModel_Definition(
		type: DataModel::TYPE_STRING,
		max_len: 255
	)]
	#[Form_Definition(
		type: Form_Field::TYPE_INPUT,
		label: 'Address - ZIP:',
		is_required: false,
		error_messages: [
		]
	)]
	protected string $address_zip = '';
	
	/**
	 * @var string
	 */
	#[DataModel_Definition(
		type: DataModel::TYPE_STRING,
		max_len: 255
	)]
	#[Form_Definition(
		type: Form_Field::TYPE_SELECT,
		label: 'Address - country:',
		is_required: false,
		select_options_creator: [
			DataList::class,
			'countries'
		]
	)]
	protected string $address_country = '';
	
	#[DataModel_Definition(
		type: DataModel::TYPE_STRING,
		max_len: 255
	)]
	#[Form_Definition(
		type: Form_Field::TYPE_INPUT,
		label: 'Phone 1:',
		is_required: false,
		error_messages: [
		]
	)]
	protected string $phone_1 = '';
	
	#[DataModel_Definition(
		type: DataModel::TYPE_STRING,
		max_len: 255
	)]
	#[Form_Definition(
		type: Form_Field::TYPE_INPUT,
		label: 'Phone 2:',
		is_required: false,
		error_messages: [
		]
	)]
	protected string $phone_2 = '';
	
	#[DataModel_Definition(
		type: DataModel::TYPE_STRING,
		max_len: 255
	)]
	#[Form_Definition(
		type: Form_Field::TYPE_EMAIL,
		label: 'e-mail 1:',
		is_required: false,
		error_messages: [
			Form_Field::ERROR_CODE_INVALID_FORMAT => 'please enter e-mail'
		]
	)]
	protected string $email_1 = '';
	
	#[DataModel_Definition(
		type: DataModel::TYPE_STRING,
		max_len: 255
	)]
	#[Form_Definition(
		type: Form_Field::TYPE_EMAIL,
		label: 'e-mail 2:',
		is_required: false,
		error_messages: [
			Form_Field::ERROR_CODE_INVALID_FORMAT => 'please enter e-mail'
		]
	)]
	protected string $email_2 = '';
	
	#[DataModel_Definition(
		type: DataModel::TYPE_LOCALE
	)]
	#[Form_Definition(
		type: Form_Field::TYPE_SELECT,
		label: 'Communication language:',
		is_required: false,
		select_options_creator: [
			DataList::class,
			'locales'
		],
		error_messages: [
		]
	)]
	protected ?Locale $locale = null;
	
	#[DataModel_Definition(
		type: DataModel::TYPE_STRING,
		is_key: true,
		max_len: 50,
	)]
	#[Form_Definition(
		type: Form_Field::TYPE_SELECT,
		label: 'Currency:',
		is_required: false,
		select_options_creator: [
			Currencies::class,
			'getScope'
		],
		error_messages: [
		]
	)]
	protected string $currency_code = '';
	
	
	public function isActive() : bool
	{
		return $this->is_active;
	}
	
	public function setIsActive( bool $is_active ): void
	{
		$this->is_active = $is_active;
	}
	
	public function setCompanyName( string $value ) : void
	{
		$this->company_name = $value;
	}
	
	public function getCompanyName() : string
	{
		return $this->company_name;
	}
	
	public function setCompanyId( string $value ) : void
	{
		$this->company_id = $value;
	}
	
	public function getCompanyId() : string
	{
		return $this->company_id;
	}
	
	public function setCompanyVatId( string $value ) : void
	{
		$this->company_vat_id = $value;
	}
	
	public function getCompanyVatId() : string
	{
		return $this->company_vat_id;
	}
	
	public function setAddressStreetAndNo( string $value ) : void
	{
		$this->address_street_and_no = $value;
	}
	
	public function getAddressStreetAndNo() : string
	{
		return $this->address_street_and_no;
	}

	public function setAddressTown( string $value ) : void
	{
		$this->address_town = $value;
	}
	
	public function getAddressTown() : string
	{
		return $this->address_town;
	}

	public function setAddressZip( string $value ) : void
	{
		$this->address_zip = $value;
	}

	public function getAddressZip() : string
	{
		return $this->address_zip;
	}

	public function setAddressCountry( string $value ) : void
	{
		$this->address_country = $value;
	}
	
	public function getAddressCountry() : string
	{
		return $this->address_country;
	}

	public function setPhone1( string $value ) : void
	{
		$this->phone_1 = $value;
	}

	public function getPhone1() : string
	{
		return $this->phone_1;
	}

	public function setPhone2( string $value ) : void
	{
		$this->phone_2 = $value;
	}

	public function getPhone2() : string
	{
		return $this->phone_2;
	}

	public function setEmail1( string $value ) : void
	{
		$this->email_1 = $value;
	}

	public function getEmail1() : string
	{
		return $this->email_1;
	}

	public function setEmail2( string $value ) : void
	{
		$this->email_2 = $value;
	}

	public function getEmail2() : string
	{
		return $this->email_2;
	}

	public function setLocale( Locale|string $value ) : void
	{
		if( !( $value instanceof Locale ) ) {
			$value = new Locale( (string)$value );
		}
		
		$this->locale = $value;
	}

	public function getLocale() : Locale
	{
		return $this->locale;
	}
	
	public function getCurrencyCode(): string
	{
		return $this->currency_code;
	}
	
	public function setCurrencyCode( string $currency_code ): void
	{
		$this->currency_code = $currency_code;
	}
	
	
	public function getFulltextObjectType(): string
	{
		return static::getEntityType();
	}
	
	public function getFulltextObjectIsActive(): bool
	{
		return $this->isActive();
	}
	
	public function getInternalFulltextObjectTitle(): string
	{
		return $this->getAdminTitle();
	}
	
	public function getInternalFulltextTexts(): array
	{
		return [$this->getInternalName(), $this->getInternalCode()];
	}
	
	public function getShopFulltextTexts( EShop $eshop ) : array
	{
		return [];
	}
	
	public function updateFulltextSearchIndex() : void
	{
		Application_Service_Admin::FulltextSearch()->updateIndex( $this );
	}
	
	public function removeFulltextSearchIndex() : void
	{
		Application_Service_Admin::FulltextSearch()->deleteIndex( $this );
	}
	
	/**
	 * @return Supplier_Backend_Module[]
	 */
	public static function getBackendModules() : array
	{
		return Application_Service_List::findPossibleModules(Supplier_Backend_Module::class, 'SupplierBackend.');
	}
	
	public function setBackendModuleName( string $backend_module_name ): void
	{
		$this->backend_module_name = $backend_module_name;
	}
	
	public function getBackendModuleName(): string
	{
		return $this->backend_module_name;
	}
	
	
	
	public static function getBackendModulesScope() : array
	{
		$scope = [];
		
		foreach(static::getBackendModules() as $module) {
			$manifest = $module->getModuleManifest();
			
			$scope[$manifest->getName()] = $manifest->getLabel().' ('.$manifest->getName().')';
		}
		
		return $scope;
	}
	
	public function getBackendModule() : Supplier_Backend_Module|Application_Module
	{
		return Application_Modules::moduleInstance( $this->backend_module_name );
	}
	
}