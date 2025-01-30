<?php
/**
 *
 */

namespace JetShop;

use Jet\DataModel;
use Jet\DataModel_Definition;
use Jet\DataModel_IDController_AutoIncrement;
use Jet\Form_Definition;
use Jet\Form_Field;

use Jet\Form_Field_Select;
use JetApplication\DataList;
use JetApplication\EShopEntity_Basic;

/**
 *
 */
#[DataModel_Definition(
	id_controller_class: DataModel_IDController_AutoIncrement::class,
	id_controller_options: [
		'id_property_name' => 'id'
	]
)]
abstract class Core_EShopEntity_Address extends EShopEntity_Basic
{
	
	#[DataModel_Definition(
		type: DataModel::TYPE_ID_AUTOINCREMENT,
		is_id: true,
	)]
	protected int $id = 0;
	
	#[DataModel_Definition(
		type: DataModel::TYPE_STRING,
		max_len: 100,
	)]
	#[Form_Definition(
		type: Form_Field::TYPE_INPUT,
		label: 'Company name:'
	)]
	protected string $company_name = '';
	
	#[DataModel_Definition(
		type: DataModel::TYPE_STRING,
		max_len: 50,
	)]
	#[Form_Definition(
		type: Form_Field::TYPE_INPUT,
		label: 'Company id:'
	)]
	protected string $company_id = '';
	
	#[DataModel_Definition(
		type: DataModel::TYPE_STRING,
		max_len: 50,
	)]
	#[Form_Definition(
		type: Form_Field::TYPE_INPUT,
		label: 'Company VAT id:'
	)]
	protected string $company_vat_id = '';
	
	#[DataModel_Definition(
		type: DataModel::TYPE_STRING,
		max_len: 100,
	)]
	#[Form_Definition(
		type: Form_Field::TYPE_INPUT,
		label: 'First name:'
	)]
	protected string $first_name = '';
	
	#[DataModel_Definition(
		type: DataModel::TYPE_STRING,
		max_len: 100,
	)]
	#[Form_Definition(
		type: Form_Field::TYPE_INPUT,
		label: 'Surname:'
	)]
	protected string $surname = '';
	
	#[DataModel_Definition(
		type: DataModel::TYPE_STRING,
		max_len: 255,
	)]
	#[Form_Definition(
		type: Form_Field::TYPE_INPUT,
		label: 'Address - street and number:'
	)]
	protected string $address_street_no = '';
	
	#[DataModel_Definition(
		type: DataModel::TYPE_STRING,
		max_len: 50,
	)]
	#[Form_Definition(
		type: Form_Field::TYPE_INPUT,
		label: 'Address - zip:'
	)]
	protected string $address_zip = '';
	
	#[DataModel_Definition(
		type: DataModel::TYPE_STRING,
		max_len: 255,
	)]
	#[Form_Definition(
		type: Form_Field::TYPE_INPUT,
		label: 'Address - town:'
	)]
	protected string $address_town = '';
	
	#[DataModel_Definition(
		type: DataModel::TYPE_STRING,
		max_len: 255,
	)]
	#[Form_Definition(
		type: Form_Field::TYPE_SELECT,
		label: 'Address - country:',
		select_options_creator: [
			DataList::class,
			'countries'
		],
		error_messages: [
			Form_Field_Select::ERROR_CODE_EMPTY => 'Invalid value',
			Form_Field_Select::ERROR_CODE_INVALID_VALUE => 'Invalid value'
		]
	
	)]
	protected string $address_country = '';
	
	#[DataModel_Definition(
		type: DataModel::TYPE_STRING,
		is_key: true,
		max_len: 100,
	)]
	protected string $hash = '';
	
	public function __clone(): void
	{
		$this->setIsNew();
		$this->id = 0;
		$this->hash = '';
		
	}
	
	public function __wakeup(): void
	{
		$this->generateHash();
	}
	
	public function getId() : int
	{
		return $this->id;
	}
	
	
	/**
	 * @param int|string $id
	 * @return static|null
	 */
	public static function get( int|string $id ) : static|null
	{
		return static::load( $id );
	}
	
	
	public function setId( int $id ): void
	{
		$this->id = $id;
	}
	
	
	public function setCompanyName( string $value ) : void
	{
		$this->company_name = $value;
	}
	
	/**
	 * @return string
	 */
	public function getCompanyName() : string
	{
		return $this->company_name;
	}
	
	/**
	 * @param string $value
	 */
	public function setCompanyId( string $value ) : void
	{
		$this->company_id = $value;
	}
	
	/**
	 * @return string
	 */
	public function getCompanyId() : string
	{
		return $this->company_id;
	}
	
	/**
	 * @param string $value
	 */
	public function setCompanyVatId( string $value ) : void
	{
		$this->company_vat_id = $value;
	}
	
	/**
	 * @return string
	 */
	public function getCompanyVatId() : string
	{
		return $this->company_vat_id;
	}
	
	/**
	 * @param string $value
	 */
	public function setFirstName( string $value ) : void
	{
		$this->first_name = $value;
	}
	
	/**
	 * @return string
	 */
	public function getFirstName() : string
	{
		return $this->first_name;
	}
	
	/**
	 * @param string $value
	 */
	public function setSurname( string $value ) : void
	{
		$this->surname = $value;
	}
	
	/**
	 * @return string
	 */
	public function getSurname() : string
	{
		return $this->surname;
	}
	
	/**
	 * @param string $value
	 */
	public function setAddressStreetNo( string $value ) : void
	{
		$this->address_street_no = $value;
	}
	
	/**
	 * @return string
	 */
	public function getAddressStreetNo() : string
	{
		return $this->address_street_no;
	}
	
	/**
	 * @param string $value
	 */
	public function setAddressZip( string $value ) : void
	{
		$this->address_zip = $value;
	}
	
	/**
	 * @return string
	 */
	public function getAddressZip() : string
	{
		return $this->address_zip;
	}
	
	/**
	 * @param string $value
	 */
	public function setAddressCountry( string $value ) : void
	{
		$this->address_country = $value;
	}
	
	/**
	 * @return string
	 */
	public function getAddressCountry() : string
	{
		return $this->address_country;
	}
	
	/**
	 * @param string $value
	 */
	public function setAddressTown( string $value ) : void
	{
		$this->address_town = $value;
	}
	
	/**
	 * @return string
	 */
	public function getAddressTown() : string
	{
		return $this->address_town;
	}
	
	public function generateHash() : string
	{
		$this->hash = '';
		
		$properties = [
			'company_name',
			'company_id',
			'company_vat_id',
			'first_name',
			'surname',
			'address_street_no',
			'address_zip',
			'address_town',
			'address_country',
		];
		
		
		foreach($properties as $k) {
			$this->hash .= ':'.$this->{$k};
		}
		
		$this->hash = md5( $this->hash );
		
		return $this->hash;
	}
	
	public function getHash() : string
	{
		if(!$this->hash) {
			$this->generateHash();
		}
		
		return $this->hash;
	}
	
	/**
	 * @param string $value
	 */
	public function setHash( string $value ) : void
	{
		$this->hash = $value;
	}
	
	
	
	public function beforeSave(): void
	{
		$this->generateHash();
	}
	
}
