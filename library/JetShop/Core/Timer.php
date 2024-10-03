<?php
namespace JetShop;

use Error;
use Jet\Data_DateTime;
use Jet\DataModel;
use Jet\DataModel_Definition;
use JetApplication\Entity_WithShopData;
use JetApplication\Entity_WithShopRelation;
use JetApplication\Shops_Shop;
use JetApplication\Timer;


#[DataModel_Definition(
	name: 'timers',
	database_table_name: 'timers'
)]
abstract class Core_Timer extends Entity_WithShopRelation {

	#[DataModel_Definition(
		type: DataModel::TYPE_DATE_TIME,
		is_key: true
	)]
	protected ?Data_DateTime $date_time;
	
	#[DataModel_Definition(
		type: DataModel::TYPE_STRING,
		max_len: 255,
		is_key: true
	)]
	protected string $action_entity_type = '';
	
	
	#[DataModel_Definition(
		type: DataModel::TYPE_STRING,
		max_len: 255,
		is_key: true
	)]
	protected string $action_entity_class = '';
	
	#[DataModel_Definition(
		type: DataModel::TYPE_INT,
		is_key: true
	)]
	protected int $action_entity_id = 0;
	
	#[DataModel_Definition(
		type: DataModel::TYPE_STRING,
		max_len: 255,
		is_key: true
	)]
	protected string $action = '';
	
	
	#[DataModel_Definition(
		type: DataModel::TYPE_STRING,
		max_len: 255,
		is_key: true
	)]
	protected string $action_context = '';
	
	#[DataModel_Definition(
		type: DataModel::TYPE_BOOL,
		is_key: true
	)]
	protected bool $processed = false;
	
	#[DataModel_Definition(
		type: DataModel::TYPE_DATE_TIME,
	)]
	protected ?Data_DateTime $processed_date_time = null;
	
	
	#[DataModel_Definition(
		type: DataModel::TYPE_BOOL,
		is_key: true
	)]
	protected bool $error = false;
	
	#[DataModel_Definition(
		type: DataModel::TYPE_STRING,
		max_len: 99999
	)]
	protected string $error_message = '';
	
	public function getDateTime(): Data_DateTime
	{
		return $this->date_time;
	}

	public function setDateTime( Data_DateTime|string $date_time ): void
	{
		$this->date_time = Data_DateTime::catchDateTime( $date_time );
	}

	public function getActionEntityType(): string
	{
		return $this->action_entity_type;
	}

	public function setActionEntityType( string $entity_type ): void
	{
		$this->action_entity_type = $entity_type;
	}
	
	public function getActionEntityClass(): string
	{
		return $this->action_entity_class;
	}
	
	public function setActionEntityClass( string $entity_class ): void
	{
		$this->action_entity_class = $entity_class;
	}
	
	public function getAction(): string
	{
		return $this->action;
	}

	public function setAction( string $action ): void
	{
		$this->action = $action;
	}

	public function getActionContext(): string
	{
		return $this->action_context;
	}
	
	public function setActionContext( mixed $action_context ): void
	{
		$this->action_context = (string)$action_context;
	}
	
	public static function newTimer(
		Entity_WithShopData $entity,
		Shops_Shop $shop,
		Data_DateTime $date_time,
		string $action,
		mixed $action_context
	) : static
	{
		$timer = new static();
		$timer->action_entity_type = $entity::getEntityType();
		$timer->action_entity_id = $entity->getId();
		$timer->action_entity_class = get_class($entity);
		$timer->setShop( $shop );
		
		$timer->date_time = $date_time;
		$timer->action = $action;
		$timer->action_context = (string)$action_context;
		
		$timer->save();
		
		return $timer;
	}
	
	protected static function getWhere( Entity_WithShopData $entity, Shops_Shop $shop ) : array
	{
		$where = [
			'action_entity_type' => $entity::getEntityType(),
			'AND',
			'action_entity_id' => $entity->getId(),
			'AND',
			$shop->getWhere()
		];

		return $where;
	}
	
	/**
	 * @return static[]
	 */
	public static function getScheduled( Entity_WithShopData $entity, Shops_Shop $shop ) : array
	{
		$where = static::getWhere( $entity, $shop );
		$where[] = 'AND';
		$where['processed'] = false;
		
		return static::fetch(
			where_per_model: ['timers' => $where],
			order_by: '+date_time',
			item_key_generator: function( Timer $item ) : int {
				return $item->getId();
			});
	}
	
	/**
	 * @return static[];
	 */
	public static function toPerform() : array
	{
		$where = [];
		$where['processed'] = false;
		
		$scheduled = static::fetch(
			where_per_model: ['timers' => $where],
			order_by: '+date_time',
			item_key_generator: function( Timer $item ) : int {
				return $item->getId();
			});

		
		$to_perform = [];
		foreach($scheduled as $item) {
			if($item->getDateTime()<=Data_DateTime::now()) {
				$to_perform[] = $item;
			}
		}

		//shuffle( $to_perform );
		
		return $to_perform;
	}
	
	public static function hasNotProcessed( Entity_WithShopData $entity, Shops_Shop $shop ) : bool
	{
		$where = static::getWhere($entity, $shop);
		$where[] = 'AND';
		$where['processed'] = false;
		
		return count(static::dataFetchCol(
			select: ['id'],
			where: $where
		))>0;
	}
	
	public function perform() : bool
	{
		/**
		 * @var Entity_WithShopData $class
		 */
		$class = $this->action_entity_class;
		
		$entity = $class::load( $this->action_entity_id );

		if(!$entity) {
			$this->errorDuringProcess('The item no longer exists');
			return false;
		}
		
		$actions = $entity->getShopData($this->getShop())->getAvailableTimerActions();
		
		if(!isset($actions[$this->action])) {
			$this->errorDuringProcess('The item action no longer exists');
			return false;
		}
		
		try {
			$actions[$this->action]->perform( $entity, $this->action_context );
		} catch( Error $e) {
			$this->errorDuringProcess($e->getMessage());
			return false;
		}
		
		$this->processed = true;
		$this->processed_date_time = Data_DateTime::now();
		$this->error = false;
		$this->error_message = '';
		
		$this->save();
		
		
		return true;
	}
	
	protected function errorDuringProcess( string $message ) : void
	{
		$this->processed = true;
		$this->processed_date_time = Data_DateTime::now();
		$this->error = true;
		$this->error_message = $message;
		
		$this->save();
	}
	
}
