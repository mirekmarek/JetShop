<?php
/**
 * 
 */

namespace JetShop;

use Jet\DataModel;
use Jet\DataModel_Definition;
use Jet\DataModel_IDController_Passive;
use Jet\Form;

use JetApplication\WarehouseManagement_Item;
use JetApplication\WarehouseManagement_Item_Event;
use JetApplication\WarehouseManagement_Warehouse;
use JetApplication\WarehouseManagement;

/**
 *
 */
#[DataModel_Definition(
	name: 'warehouse_item',
	database_table_name: 'whm_warehouses_items',
	id_controller_class: DataModel_IDController_Passive::class
)]
class Core_WarehouseManagement_Item extends DataModel
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
		type: DataModel::TYPE_STRING,
		is_id: true,
		is_key: true,
		max_len: 64,
	)]
	protected string $warehouse_code = '';

	/**
	 * @var int
	 */ 
	#[DataModel_Definition(
		type: DataModel::TYPE_INT,
		is_id: true,
		is_key: true,
	)]
	protected int $product_id = 0;

	/**
	 * @var int
	 */ 
	#[DataModel_Definition(
		type: DataModel::TYPE_INT,
	)]
	protected int $available = 0;

	/**
	 * @var int
	 */ 
	#[DataModel_Definition(
		type: DataModel::TYPE_INT,
	)]
	protected int $blocked = 0;

	/**
	 * @var int
	 */ 
	#[DataModel_Definition(
		type: DataModel::TYPE_INT,
	)]
	protected int $in_stock = 0;

	/**
	 * @var int
	 */ 
	#[DataModel_Definition(
		type: DataModel::TYPE_INT,
	)]
	protected int $required = 0;

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
	 * @param string $warehouse_code
	 * @param int $product_id
	 * @return static
	 */
	public static function get( string $warehouse_code, int $product_id ) : static
	{
		$item = static::load( [
			'warehouse_code' => $warehouse_code,
			'AND',
			'product_id' => $product_id
		] );
		if(!$item) {
			$item = new WarehouseManagement_Item();
			$item->setWarehouseCode($warehouse_code);
			$item->setProductId($product_id);
		}

		return $item;
	}


	/**
	 * @param int $product_id
	 * @return static[]
	 */
	public static function getByProduct(int $product_id ) : iterable
	{
		return static::fetch( [
			'warehouse_item' => [
				'product_id' => $product_id
			]
		] );
	}


	/**
	 * @return WarehouseManagement_Item[]
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
	public function setProductId( int $value ) : void
	{
		$this->product_id = (string)$value;
	}

	/**
	 * @return int
	 */
	public function getProductId() : int
	{
		return $this->product_id;
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

	public function getWarehouse() : WarehouseManagement_Warehouse
	{
		return WarehouseManagement_Warehouse::get($this->getWarehouseCode());
	}

	/**
	 * @param int $value
	 */
	public function setAvailable( int $value ) : void
	{
		$this->available = $value;
	}

	/**
	 * @return int
	 */
	public function getAvailable() : int
	{
		return $this->available;
	}

	/**
	 * @param int $value
	 */
	public function setBlocked( int $value ) : void
	{
		$this->blocked = $value;
	}

	/**
	 * @return int
	 */
	public function getBlocked() : int
	{
		return $this->blocked;
	}

	/**
	 * @param int $value
	 */
	public function setInStock( int $value ) : void
	{
		$this->in_stock = $value;
	}

	/**
	 * @return int
	 */
	public function getInStock() : int
	{
		return $this->in_stock;
	}

	/**
	 * @param int $value
	 */
	public function setRequired( int $value ) : void
	{
		$this->required = $value;
	}

	/**
	 * @return int
	 */
	public function getRequired() : int
	{
		return $this->required;
	}

	public function recalculate() : void
	{

		$this->available = 0;
		$this->blocked = 0;
		$this->in_stock = 0;

		$this->required = 0;

		$events = WarehouseManagement_Item_Event::getList( $this->warehouse_code, $this->product_id );

		foreach($events as $event) {
			$et = $event->getEventType();

			if($et->isInStockAdd()) {
				$this->in_stock += $event->getCount();
			}

			if($et->isInStockSubtract()) {
				$this->in_stock -= $event->getCount();
			}

			if($et->isBlockedAdd()) {
				$this->blocked += $event->getCount();
			}

			if($et->isBlockedSubtract()) {
				$this->blocked -= $event->getCount();
			}
		}


		$this->available = $this->in_stock-$this->blocked;

		if($this->available<0) {
			$this->required = -1*$this->available;
			$this->available = 0;
		}

		$this->save();

		WarehouseManagement::recalculateProductAvailability( $this->product_id );
		//TODO: recalculate orders

	}
}
