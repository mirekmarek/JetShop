<?php
/**
 * 
 */

namespace JetShop;

use Jet\DataModel;
use Jet\DataModel_Definition;
use Jet\DataModel_IDController_AutoIncrement;
use Jet\Form;
use Jet\Form_Definition;
use Jet\Form_Field;
use Jet\Data_DateTime;
use Jet\Form_Field_Int;

use JetApplication\WarehouseManagement;
use JetApplication\WarehouseManagement_Item_Event;
use JetApplication\WarehouseManagement_Item_Event_Type;

/**
 *
 */
#[DataModel_Definition(
	name: 'warehouse_item_event',
	database_table_name: 'whm_warehouses_items_events',
	id_controller_class: DataModel_IDController_AutoIncrement::class,
	id_controller_options: [
		'id_property_name' => 'id'
	]
)]
class Core_WarehouseManagement_Item_Event extends DataModel
{

	/**
	 * @var int
	 */ 
	#[DataModel_Definition(
		type: DataModel::TYPE_ID_AUTOINCREMENT,
		is_id: true,
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
		is_key: true,
		max_len: 255,
	)]
	protected string $warehouse_code = '';

	/**
	 * @var int
	 */ 
	#[DataModel_Definition(
		type: DataModel::TYPE_INT,
		is_key: true,
	)]
	#[Form_Definition(
		type: Form_Field::TYPE_HIDDEN
	)]
	protected int $product_id = 0;

	/**
	 * @var ?Data_DateTime
	 */ 
	#[DataModel_Definition(
		type: DataModel::TYPE_DATE,
	)]
	protected ?Data_DateTime $date = null;

	/**
	 * @var ?Data_DateTime
	 */ 
	#[DataModel_Definition(
		type: DataModel::TYPE_DATE_TIME,
	)]
	protected ?Data_DateTime $date_time = null;

	/**
	 * @var string
	 */ 
	#[DataModel_Definition(
		type: DataModel::TYPE_STRING,
		is_key: true,
		max_len: 255,
	)]
	protected string $event = '';

	/**
	 * @var int
	 */ 
	#[DataModel_Definition(
		type: DataModel::TYPE_INT,
	)]
	#[Form_Definition(
		type: Form_Field::TYPE_INT,
		is_required: true,
		label: 'Count:',
		min_value: 1,
		error_messages: [
			Form_Field_Int::ERROR_CODE_EMPTY => 'Please enter count',
			Form_Field_Int::ERROR_CODE_OUT_OF_RANGE => 'Minimal count is 1'
		]
	)]
	protected int $count = 0;

	/**
	 * @var string
	 */ 
	#[DataModel_Definition(
		type: DataModel::TYPE_STRING,
		is_key: true,
		max_len: 50,
	)]
	#[Form_Definition(
		type: Form_Field::TYPE_INPUT,
		is_required: true,
		label: 'Context type:',
		error_messages: [
			Form_Field::ERROR_CODE_EMPTY => 'Please enter context type',
			Form_Field::ERROR_CODE_INVALID_FORMAT => 'Please enter context type'
		]
	)]
	protected string $context_type = '';

	/**
	 * @var string
	 */ 
	#[DataModel_Definition(
		type: DataModel::TYPE_STRING,
		is_key: true,
		max_len: 100,
	)]
	#[Form_Definition(
		type: Form_Field::TYPE_INPUT,
		label: 'Context:',
		error_messages: [
			Form_Field::ERROR_CODE_EMPTY => 'Please enter context',
			Form_Field::ERROR_CODE_INVALID_FORMAT => 'Please enter context'
		]
	)]
	protected string $context = '';

	/**
	 * @var string
	 */ 
	#[DataModel_Definition(
		type: DataModel::TYPE_STRING,
		max_len: 9999,
	)]
	#[Form_Definition(
		type: Form_Field::TYPE_TEXTAREA,
		label: 'Internal description:'
	)]
	protected string $internal_description = '';

	/**
	 * @var float
	 */ 
	#[DataModel_Definition(
		type: DataModel::TYPE_FLOAT,
	)]
	#[Form_Definition(
		type: Form_Field::TYPE_FLOAT,
		label: 'Price (per item):'
	)]
	protected float $price = 0.0;

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
			$this->setDateTime(Data_DateTime::now());
			$this->_form_add = $this->createForm('add_form');
			/*
			$this->_form_add->field('date_time')->setFieldValueCatcher(function( $value ) {
				$value = new Data_DateTime($value);
				$this->setDateTime($value);
				$this->setDate($value);
			});
			*/
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
	 * @param string $warehouse_code
	 * @param int $product_id
	 *
	 * @return WarehouseManagement_Item_Event[]
	 */
	public static function getList( string $warehouse_code, int $product_id ) : iterable
	{
		$where = [
			'warehouse_code' => $warehouse_code,
			'AND',
			'product_id' => $product_id
		];
		
		$list = static::fetchInstances( $where );
		$list->getQuery()->setOrderBy(['id']);
		
		return $list;
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
	public function setWarehouseCode( string $value ) : void
	{
		$this->warehouse_code = $value;
	}

	/**
	 * @return string
	 */
	public function getWarehouseCode() : string
	{
		return $this->warehouse_code;
	}

	/**
	 * @param int $value
	 */
	public function setProductId( int $value ) : void
	{
		$this->product_id = $value;
	}

	/**
	 * @return int
	 */
	public function getProductId() : int
	{
		return $this->product_id;
	}

	/**
	 * @param Data_DateTime|string|null $value
	 */
	public function setDate( Data_DateTime|string|null $value ) : void
	{
		if( $value===null ) {
			$this->date = null;
			return;
		}
		
		if( !( $value instanceof Data_DateTime ) ) {
			$value = new Data_DateTime( (string)$value );
		}
		
		$this->date = $value;
	}

	/**
	 * @return Data_DateTime|null
	 */
	public function getDate() : Data_DateTime|null
	{
		return $this->date;
	}

	/**
	 * @param Data_DateTime|string|null $value
	 */
	public function setDateTime( Data_DateTime|string|null $value ) : void
	{
		if( $value===null ) {
			$this->date_time = null;
			return;
		}
		
		if( !( $value instanceof Data_DateTime ) ) {
			$value = new Data_DateTime( (string)$value );
		}
		
		$this->date_time = $value;
		$this->setDate($value);
	}

	/**
	 * @return Data_DateTime|null
	 */
	public function getDateTime() : Data_DateTime|null
	{
		return $this->date_time;
	}

	/**
	 * @param string $value
	 */
	public function setEvent( string $value ) : void
	{
		$this->event = $value;
	}

	/**
	 * @return string
	 */
	public function getEvent() : string
	{
		return $this->event;
	}

	public function getEventType() : WarehouseManagement_Item_Event_Type
	{
		return WarehouseManagement_Item_Event_Type::getList()[$this->event];
	}

	/**
	 * @param int $value
	 */
	public function setCount( int $value ) : void
	{
		$this->count = $value;
	}

	/**
	 * @return int
	 */
	public function getCount() : int
	{
		return $this->count;
	}

	/**
	 * @param string $value
	 */
	public function setContextType( string $value ) : void
	{
		$this->context_type = $value;
	}

	/**
	 * @return string
	 */
	public function getContextType() : string
	{
		return $this->context_type;
	}

	/**
	 * @param string $value
	 */
	public function setContext( string $value ) : void
	{
		$this->context = $value;
	}

	/**
	 * @return string
	 */
	public function getContext() : string
	{
		return $this->context;
	}

	/**
	 * @param string $value
	 */
	public function setInternalDescription( string $value ) : void
	{
		$this->internal_description = $value;
	}

	/**
	 * @return string
	 */
	public function getInternalDescription() : string
	{
		return $this->internal_description;
	}

	/**
	 * @param float $value
	 */
	public function setPrice( float $value ) : void
	{
		$this->price = $value;
	}

	/**
	 * @return float
	 */
	public function getPrice() : float
	{
		return $this->price;
	}

	public function afterAdd(): void
	{
		WarehouseManagement::itemRecalculate( $this->warehouse_code, $this->product_id );
	}


	public function afterUpdate(): void
	{
		WarehouseManagement::itemRecalculate( $this->warehouse_code, $this->product_id );
	}

}
