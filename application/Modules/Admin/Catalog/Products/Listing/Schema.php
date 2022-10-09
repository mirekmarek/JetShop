<?php
/**
 * 
 */

namespace JetShopModule\Admin\Catalog\Products;

use Jet\DataModel;
use Jet\DataModel_Definition;
use Jet\DataModel_Fetch_Instances;
use Jet\DataModel_IDController_AutoIncrement;
use Jet\Form;
use Jet\Form_Field;
use Jet\Form_Definition;

/**
 *
 */
#[DataModel_Definition(
	name: 'listing_schema',
	database_table_name: 'listing_schema',
	id_controller_class: DataModel_IDController_AutoIncrement::class,
	id_controller_options: [
		'id_property_name' => 'id'
	]
)]
class Listing_Schema extends DataModel
{

	/**
	 * @var int
	 */ 
	#[DataModel_Definition(
		type: DataModel::TYPE_ID_AUTOINCREMENT,
		is_id: true
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
	 * @var string
	 */ 
	#[DataModel_Definition(
		type: DataModel::TYPE_STRING,
		max_len: 255
	)]
	#[Form_Definition(
		type: Form_Field::TYPE_INPUT,
		label: 'Schema name:',
		is_required: true,
		error_messages: [
			Form_Field::ERROR_CODE_EMPTY => 'Please enter schema name'
		]
	)]
	protected string $name = '';

	/**
	 * @var string
	 */ 
	#[DataModel_Definition(
		type: DataModel::TYPE_STRING,
		max_len: 65536
	)]
	#[Form_Definition(
		type: Form_Field::TYPE_HIDDEN
	)]
	protected string $cols = '';

	/**
	 * @var bool
	 */ 
	#[DataModel_Definition(
		type: DataModel::TYPE_BOOL,
		is_key: true
	)]
	#[Form_Definition(
		type: Form_Field::TYPE_CHECKBOX,
		label: 'Is default',
		is_required: false,
		error_messages: [
		]
	)]
	protected bool $is_default = false;

	/**
	 * @return Form
	 */
	public function getEditForm() : Form
	{
		if(!$this->_form_edit) {
			$this->_form_edit = $this->createForm('edit_form');
		}
		
		return $this->_form_edit;
	}

	/**
	 * @return bool
	 */
	public function catchEditForm() : bool
	{
		return $this->getEditForm()->catch();
	}

	/**
	 * @return Form
	 */
	public function getAddForm() : Form
	{
		if(!$this->_form_add) {
			$this->_form_add = $this->createForm('add_form');
		}
		
		return $this->_form_add;
	}

	/**
	 * @return bool
	 */
	public function catchAddForm() : bool
	{
		return $this->getAddForm()->catch();
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
	 * @noinspection PhpDocSignatureInspection
	 * @return static[]|DataModel_Fetch_Instances
	 */
	public static function getList() : iterable
	{
		$where = [];
		
		return static::fetchInstances( $where );
	}

	/**
	 * @return int
	 */
	public function getId() : int
	{
		return $this->id;
	}

	/**
	 * @param string $value
	 */
	public function setName( string $value ) : void
	{
		$this->name = $value;
	}

	/**
	 * @return string
	 */
	public function getName() : string
	{
		return $this->name;
	}

	/**
	 * @param string $value
	 */
	public function setCols( string $value ) : void
	{
		$this->cols = $value;
	}

	/**
	 * @return string
	 */
	public function getCols() : string
	{
		return $this->cols;
	}

	/**
	 * @param bool $value
	 */
	public function setIsDefault( bool $value ) : void
	{
		$this->is_default = (bool)$value;
	}

	/**
	 * @return bool
	 */
	public function getIsDefault() : bool
	{
		return $this->is_default;
	}
}
