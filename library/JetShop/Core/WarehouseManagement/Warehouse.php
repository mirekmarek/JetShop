<?php
/**
 * 
 */

namespace JetShop;

use Jet\DataModel;
use Jet\DataModel_Definition;
use Jet\DataModel_IDController_Passive;
use Jet\Form;
use Jet\Form_Definition;
use Jet\Form_Field;
use Jet\Form_Field_Input;

use JetApplication\WarehouseManagement_Warehouse;
use JetApplication\Shops;
use JetApplication\Shops_Shop;
use JetApplication\WarehouseManagement_Warehouse_Shop;


/**
 *
 */
#[DataModel_Definition(
	name: 'warehouse',
	database_table_name: 'whm_warehouses',
	id_controller_class: DataModel_IDController_Passive::class
)]
class Core_WarehouseManagement_Warehouse extends DataModel
{

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
		type: DataModel::TYPE_ID,
		is_id: true,
	)]
	#[Form_Definition(
		type: Form_Field::TYPE_INPUT,
		is_required: true,
		label: 'Code:',
		error_messages: [
			Form_Field::ERROR_CODE_EMPTY => 'Please enter warehouse code'
		]		
	)]
	protected string $code = '';

	/**
	 * @var string
	 */ 
	#[DataModel_Definition(
		type: DataModel::TYPE_STRING,
		max_len: 255,
	)]
	#[Form_Definition(
		type: Form_Field::TYPE_INPUT,
		is_required: true,
		label: 'Internal name:',
		error_messages: [
			Form_Field::ERROR_CODE_EMPTY => 'Please enter internal name'
		]		
	)]
	protected string $internal_name = '';

	/**
	 * @var string
	 */ 
	#[DataModel_Definition(
		type: DataModel::TYPE_STRING,
		max_len: 999999,
	)]
	#[Form_Definition(
		type: Form_Field::TYPE_INPUT,
		label: 'Internal description:'		
	)]
	protected string $internal_description = '';

	/**
	 * @var bool
	 */ 
	#[DataModel_Definition(
		type: DataModel::TYPE_BOOL,
		is_key: true,
	)]
	#[Form_Definition(
		type: Form_Field::TYPE_CHECKBOX,
		label: 'Is active'
	)]
	protected bool $is_active = false;

	/**
	 * @var Core_WarehouseManagement_Warehouse_Shop[]
	 */ 
	#[DataModel_Definition(
		type: DataModel::TYPE_DATA_MODEL,
		data_model_class: Core_WarehouseManagement_Warehouse_Shop::class,
	)]
	#[Form_Definition(
		type: Form_Field::TYPE_MULTI_SELECT,
		label: 'Associated shops:',
		default_value_getter_name: 'getShopKeys',
		error_messages: [
			Form_Field::ERROR_CODE_INVALID_VALUE => 'Please select shop'
		],
		select_options_creator: [Shops::class, 'getScope']
	)]
	protected array $shops = [];

	/**
	 * @var array
	 */
	protected static array $_loaded = [];

	/**
	 * @var string
	 */ 
	#[DataModel_Definition(
		type: DataModel::TYPE_STRING,
		max_len: 255,
	)]
	#[Form_Definition(
		type: Form_Field::TYPE_INPUT,
		label: 'Address - street and number:'
	)]
	protected string $address_street_no = '';

	/**
	 * @var string
	 */ 
	#[DataModel_Definition(
		type: DataModel::TYPE_STRING,
		max_len: 255,
	)]
	#[Form_Definition(
		type: Form_Field::TYPE_INPUT,
		label: 'Address - country:'
	)]
	protected string $address_country = '';

	/**
	 * @var string
	 */ 
	#[DataModel_Definition(
		type: DataModel::TYPE_STRING,
		max_len: 255,
	)]
	#[Form_Definition(
		type: Form_Field::TYPE_INPUT,
		label: 'Address - town:'
	)]
	protected string $address_town = '';

	/**
	 * @var string
	 */ 
	#[DataModel_Definition(
		type: DataModel::TYPE_STRING,
		max_len: 255,
	)]
	#[Form_Definition(
		type: Form_Field::TYPE_INPUT,
		label: 'Address - zip:'
	)]
	protected string $address_zip = '';


	public function getEditForm() : Form
	{
		if(!$this->_form_edit) {
			$this->_form_edit = $this->createForm('edit_form');
			$this->_form_edit->field('code')->setIsReadonly(true);
		}
		
		return $this->_form_edit;
	}

	public function catchEditForm() : bool
	{
		return $this->getEditForm()->catch();
	}

	public function getAddForm() : Form
	{
		if(!$this->_form_add) {
			$this->_form_add = $this->createForm('add_form');
			$field = $this->_form_add->field('code');
			$field->setErrorMessages([
				Form_Field::ERROR_CODE_EMPTY => 'Please enter code',
				'exists' => 'Warehouse with the same code already exists'
			]);
			$field->setValidator(function(Form_Field_Input $field) {
				$code = $field->getValue();
				$code = str_replace('-', ' ', $code);
				$code = preg_replace('!\s+!', ' ', $code);
				$code = str_replace(' ', '-', $code);
				$field->setValue($code);

				if(!$field->validate_required()) {
					return false;
				}

				if(WarehouseManagement_Warehouse::get($code)) {
					$field->setError('exists');
					return false;
				}

				return true;
			});
		}
		
		return $this->_form_add;
	}

	public function catchAddForm() : bool
	{
		return $this->getAddForm()->catch();
	}

	public static function get( string $code ) : static|null
	{
		if(!isset(static::$_loaded[$code])) {
			static::$_loaded[$code] = static::load( $code );
		}

		return static::$_loaded[$code];
	}

	/**
	 * @return WarehouseManagement_Warehouse[]
	 */
	public static function getList() : iterable
	{
		$where = [];
		
		$list = static::fetchInstances( $where );
		
		return $list;
	}

	public function setCode( string $value ) : void
	{
		$this->code = $value;
	}

	public function getCode() : string
	{
		return $this->code;
	}

	public function setInternalName( string $value ) : void
	{
		$this->internal_name = $value;
	}

	public function getInternalName() : string
	{
		return $this->internal_name;
	}

	public function setInternalDescription( string $value ) : void
	{
		$this->internal_description = $value;
	}

	public function getInternalDescription() : string
	{
		return $this->internal_description;
	}

	public function setIsActive( bool $value ) : void
	{
		$this->is_active = $value;
	}

	public function getIsActive() : bool
	{
		return $this->is_active;
	}

	public function setShops( array $shops ) : void
	{
		foreach($this->shops as $code=>$shop) {
			if(!in_array($code, $shops)) {
				unset($this->shops[$code]);
			}
		}

		foreach($shops as $code) {
			if(isset($this->shops[$code])) {
				continue;
			}

			$shop = new WarehouseManagement_Warehouse_Shop();
			$shop->setWarehouseCode( $this->getCode() );
			$shop->setShop( $code );

			$this->shops[$code] = $shop;
		}
	}

	public function getShopKeys() : array
	{
		$shops = [];

		foreach($this->shops as $shop) {
			$shops[] = $shop->getShopKey();
		}

		return $shops;
	}

	public function hasShop( Shops_Shop $shop ) : bool
	{
		foreach($this->shops as $_shop) {
			if($shop->getKey()==$_shop->getShopKey()) {
				return true;
			}
		}

		return false;
	}

	public static function getScope( bool $only_active=true ) : array
	{
		$list = WarehouseManagement_Warehouse::getList();


		$res = [];

		foreach($list as $item) {
			if($only_active && !$item->is_active) {
				continue;
			}

			$res[$item->getCode()] = $item->getInternalName();
		}

		return $res;
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

}
