<?php
/**
 * 
 */

namespace JetShop;

use Jet\DataModel;
use Jet\DataModel_Definition;
use Jet\DataModel_IDController_AutoIncrement;
use Jet\Data_DateTime;

/**
 *
 */
#[DataModel_Definition(
	name: 'discounts_code_usage',
	database_table_name: 'discounts_code_usage',
	id_controller_class: DataModel_IDController_AutoIncrement::class,
	id_controller_options: [
		'id_property_name' => 'id'
	]
)]
abstract class Core_Discounts_Code_Usage extends DataModel
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
	 * @var int
	 */ 
	#[DataModel_Definition(
		type: DataModel::TYPE_INT,
		is_key: true,
	)]
	protected int $code_id = 0;

	/**
	 * @var int
	 */ 
	#[DataModel_Definition(
		type: DataModel::TYPE_INT,
		is_key: true,
	)]
	protected int $order_id = 0;

	/**
	 * @var ?Data_DateTime
	 */ 
	#[DataModel_Definition(
		type: DataModel::TYPE_DATE_TIME,
	)]
	protected ?Data_DateTime $date_time = null;

	/**
	 * @var bool
	 */ 
	#[DataModel_Definition(
		type: DataModel::TYPE_BOOL,
		is_key: true,
	)]
	protected bool $cancelled = false;

	/**
	 * @var ?Data_DateTime
	 */ 
	#[DataModel_Definition(
		type: DataModel::TYPE_DATE_TIME,
	)]
	protected ?Data_DateTime $cancelled_date_time = null;

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
	 * @return int
	 */
	public function getId() : int
	{
		return $this->id;
	}

	/**
	 * @param int $value
	 */
	public function setCodeId( int $value ) : void
	{
		$this->code_id = $value;
	}

	/**
	 * @return int
	 */
	public function getCodeId() : int
	{
		return $this->code_id;
	}

	/**
	 * @param int $value
	 */
	public function setOrderId( int $value ) : void
	{
		$this->order_id = $value;
	}

	/**
	 * @return int
	 */
	public function getOrderId() : int
	{
		return $this->order_id;
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
	}

	/**
	 * @return Data_DateTime|null
	 */
	public function getDateTime() : Data_DateTime|null
	{
		return $this->date_time;
	}

	/**
	 * @param bool $value
	 */
	public function setCancelled( bool $value ) : void
	{
		$this->cancelled = $value;
	}

	/**
	 * @return bool
	 */
	public function getCancelled() : bool
	{
		return $this->cancelled;
	}

	/**
	 * @param Data_DateTime|string|null $value
	 */
	public function setCancelledDateTime( Data_DateTime|string|null $value ) : void
	{
		if( $value===null ) {
			$this->cancelled_date_time = null;
			return;
		}
		
		if( !( $value instanceof Data_DateTime ) ) {
			$value = new Data_DateTime( (string)$value );
		}
		
		$this->cancelled_date_time = $value;
	}

	/**
	 * @return Data_DateTime|null
	 */
	public function getCancelledDateTime() : Data_DateTime|null
	{
		return $this->cancelled_date_time;
	}
}
