<?php
/**
 * 
 */

namespace JetShop;

use Jet\DataModel;
use Jet\DataModel_Definition;
use Jet\DataModel_IDController_AutoIncrement;

/**
 *
 */
#[DataModel_Definition(
	name: 'customer_address',
	database_table_name: 'customers_addresses',
	id_controller_class: DataModel_IDController_AutoIncrement::class,
	id_controller_options: [
		'id_property_name' => 'id'
	]
)]
abstract class Core_Customer_Address extends DataModel
{

	/**
	 * @var int
	 */ 
	#[DataModel_Definition(
		type: DataModel::TYPE_ID_AUTOINCREMENT,
		is_id: true,
		form_field_type: 'Hidden'
	)]
	protected int $id = 0;

	/**
	 * @var int
	 */ 
	#[DataModel_Definition(
		type: DataModel::TYPE_INT,
		is_key: true,
		form_field_type: false
	)]
	protected int $customer_id = 0;

	/**
	 * @var string
	 */ 
	#[DataModel_Definition(
		type: DataModel::TYPE_STRING,
		max_len: 100,
		form_field_type: 'Input',
		form_field_label: 'Company name:'
	)]
	protected string $company_name = '';

	/**
	 * @var string
	 */ 
	#[DataModel_Definition(
		type: DataModel::TYPE_STRING,
		max_len: 50,
		form_field_type: 'Input',
		form_field_label: 'Company id:'
	)]
	protected string $company_id = '';

	/**
	 * @var string
	 */ 
	#[DataModel_Definition(
		type: DataModel::TYPE_STRING,
		max_len: 50,
		form_field_type: 'Input',
		form_field_label: 'Company VAT id:'
	)]
	protected string $company_vat_id = '';

	/**
	 * @var string
	 */ 
	#[DataModel_Definition(
		type: DataModel::TYPE_STRING,
		max_len: 100,
		form_field_type: 'Input',
		form_field_label: 'First name:'
	)]
	protected string $first_name = '';

	/**
	 * @var string
	 */ 
	#[DataModel_Definition(
		type: DataModel::TYPE_STRING,
		max_len: 100,
		form_field_type: 'Input',
		form_field_label: 'Surname:'
	)]
	protected string $surname = '';

	/**
	 * @var string
	 */ 
	#[DataModel_Definition(
		type: DataModel::TYPE_STRING,
		max_len: 255,
		form_field_type: 'Input',
		form_field_label: 'Address - street and number:'
	)]
	protected string $address_street_no = '';

	/**
	 * @var string
	 */ 
	#[DataModel_Definition(
		type: DataModel::TYPE_STRING,
		max_len: 50,
		form_field_type: 'Input',
		form_field_label: 'Address - zip:'
	)]
	protected string $address_zip = '';

	/**
	 * @var string
	 */ 
	#[DataModel_Definition(
		type: DataModel::TYPE_STRING,
		max_len: 255,
		form_field_type: 'Input',
		form_field_label: 'Address - country:'
	)]
	protected string $address_country = '';

	/**
	 * @var string
	 */ 
	#[DataModel_Definition(
		type: DataModel::TYPE_STRING,
		max_len: 255,
		form_field_type: 'Input',
		form_field_label: 'Address - town:'
	)]
	protected string $address_town = '';

	/**
	 * @var string
	 */ 
	#[DataModel_Definition(
		type: DataModel::TYPE_STRING,
		is_key: true,
		max_len: 100,
		form_field_type: false
	)]
	protected string $hash = '';

	/**
	 * @var bool
	 */ 
	#[DataModel_Definition(
		type: DataModel::TYPE_BOOL,
		is_key: true,
		form_field_type: false
	)]
	protected bool $is_default = false;


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

	/**
	 * @return int
	 */
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

	/**
	 * @return Customer_Address[]
	 */
	public static function getList() : iterable
	{
		$where = [];
		
		$list = static::fetchInstances( $where );
		
		return $list;
	}

	/**
	 * @param int $value
	 */
	public function setCustomerId( int $value ) : void
	{
		$this->customer_id = $value;
	}

	/**
	 * @return int
	 */
	public function getCustomerId() : int
	{
		return $this->customer_id;
	}

	/**
	 * @param string $value
	 */
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

		foreach(get_object_vars($this) as $k=>$v) {
			if(
				$k[0]=='_' ||
				$k=='is_default' ||
				$k=='hash' ||
				$k=='id' ||
				$k=='customer_id'
			) {
				continue;
			}

			$this->hash .= ':'.$v;
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

	/**
	 * @param bool $value
	 */
	public function setIsDefault( bool $value ) : void
	{
		$this->is_default = $value;
	}

	/**
	 * @return bool
	 */
	public function isDefault() : bool
	{
		return $this->is_default;
	}


	public function beforeSave(): void
	{
		$this->generateHash();
	}

	public static function setDefaultAddress( Customer_Address $address ) : void
	{
		Customer_Address::updateData(
			[
				'is_default' => true
			],
			[
				'id' => $address->getId()
			]
		);

		Customer_Address::updateData(
			[
				'is_default' => false
			],
			[
				'customer_id' => $address->getCustomerId(),
				'AND',
				'id !=' => $address->getId(),
			]
		);

	}
}
