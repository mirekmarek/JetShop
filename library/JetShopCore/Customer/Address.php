<?php
/**
 * 
 */

namespace JetShop;

use Jet\DataModel;
use Jet\DataModel_Definition;
use Jet\DataModel_IDController_AutoIncrement;
use Jet\Form;

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
class Core_Customer_Address extends DataModel
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
	 * @var ?Form
	 */ 
	protected ?Form $_form_edit = null;

	/**
	 * @var ?Form
	 */ 
	protected ?Form $_form_add = null;

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
	 * @return int
	 */
	public function getId() : int
	{
		return $this->id;
	}

	/**
	 * @return Form
	 */
	public function getEditForm() : Form
	{
		if(!$this->_form_edit) {
			$this->_form_edit = $this->getCommonForm('edit_form');
		}
		
		return $this->_form_edit;
	}

	/**
	 * @return bool
	 */
	public function catchEditForm() : bool
	{
		return $this->catchForm( $this->getEditForm() );
	}

	/**
	 * @return Form
	 */
	public function getAddForm() : Form
	{
		if(!$this->_form_add) {
			$this->_form_add = $this->getCommonForm('add_form');
		}
		
		return $this->_form_add;
	}

	/**
	 * @return bool
	 */
	public function catchAddForm() : bool
	{
		return $this->catchForm( $this->getAddForm() );
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
	 * @return iterable
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
}
