<?php
/**
 * @copyright Copyright (c) Miroslav Marek <mirek.marek@web-jet.cz>
 * @license EUPL 1.2  https://eupl.eu/1.2/en/
 * @author Miroslav Marek <mirek.marek@web-jet.cz>
 */
namespace JetShop;


use Jet\Data_DateTime;
use Jet\DataModel;
use Jet\DataModel_Definition;
use Jet\DataModel_Fetch_Instances;
use Jet\DataModel_IDController_AutoIncrement;
use Jet\Logger;
use Jet\MVC_Cache;
use JetApplication\EShopEntity;
use JetApplication\EShopEntity_Basic;
use JetApplication\EShopEntity_CanNotBeDeletedReason;
use JetApplication\EShopEntity_Definition;
use JetApplication\FulltextSearch_IndexDataProvider;

#[DataModel_Definition(
	name: '',
	database_table_name: '',
	id_controller_class: DataModel_IDController_AutoIncrement::class,
	id_controller_options: ['id_property_name'=>'id']
)]
abstract class Core_EShopEntity_Basic extends DataModel
{
	#[DataModel_Definition(
		type: DataModel::TYPE_ID_AUTOINCREMENT,
		is_id: true,
	)]
	protected int $id = 0;
	
	
	#[DataModel_Definition(
		type: DataModel::TYPE_DATE_TIME,
		is_key: true,
	)]
	protected ?Data_DateTime $created = null;
	
	#[DataModel_Definition(
		type: DataModel::TYPE_DATE_TIME,
		is_key: true,
	)]
	protected ?Data_DateTime $last_update = null;
	
	
	
	public function getId(): int
	{
		return $this->id;
	}
	

	public function getCreated(): ?Data_DateTime
	{
		return $this->created;
	}
	
	public function getLastUpdate(): ?Data_DateTime
	{
		return $this->last_update;
	}
	
	
	
	
	public function beforeSave(): void
	{
		if($this->getIsNew()) {
			$this->created = Data_DateTime::now();
		} else {
			$this->last_update = Data_DateTime::now();
		}
	}
	
	public static function exists( int $id ) : bool
	{
		return (bool)static::dataFetchCol(['id'], ['id'=>$id]);
	}
	
	public static function getEntityType() : string
	{
		return static::getDataModelDefinition(static::class)->getModelName();
	}
	
	/**
	 *
	 * @return DataModel_Fetch_Instances|static[]
	 */
	public static function getList() : DataModel_Fetch_Instances|iterable
	{
		return static::fetchInstances( [] );
	}
	
	public static function updateData( array $data, array $where ): void
	{
		$data['last_update'] = Data_DateTime::now();
		
		parent::updateData( $data, $where );
	}
	
	public function afterUpdate(): void
	{
		Logger::success(
			event: $this::getEntityType().'_updated',
			event_message: $this::getEntityType().' updated',
			context_object_id: $this->getId(),
			context_object_data: $this
		);
		
		if($this instanceof FulltextSearch_IndexDataProvider) {
			$this->updateFulltextSearchIndex();
		}
		
		MVC_Cache::resetOutputCache();
	}
	
	public function afterDelete(): void
	{
		Logger::success(
			event: $this::getEntityType().'_deleted',
			event_message: $this::getEntityType().' deleted',
			context_object_id: $this->getId(),
			context_object_data: $this
		);
		
		if($this instanceof FulltextSearch_IndexDataProvider) {
			$this->removeFulltextSearchIndex();
		}
		
		MVC_Cache::resetOutputCache();
	}
	
	public function afterAdd(): void
	{
		Logger::success(
			event: $this::getEntityType().'_created',
			event_message: $this::getEntityType().' created',
			context_object_id: $this->getId(),
			context_object_data: $this
		);
		
		if($this instanceof FulltextSearch_IndexDataProvider) {
			$this->updateFulltextSearchIndex();
		}
		
		MVC_Cache::resetOutputCache();
	}
	
	/**
	 * @param EShopEntity_CanNotBeDeletedReason[] &$reasons
	 * @return bool
	 */
	public function canBeDeleted( array &$reasons = [] ) : bool
	{
		$reasons = [];
		/**
		 * @var EShopEntity_Basic $this
		 */
		return EShopEntity::checkIfItCanBeDeleted( $this, $reasons );
	}
	
	public static function getEntityDefinition() : EShopEntity_Definition
	{
		return EShopEntity_Definition::get( static::class );
	}
	
	protected static function createCanNotBeDeletedReason( string $reason, array $ids  ) : EShopEntity_CanNotBeDeletedReason
	{
		return new EShopEntity_CanNotBeDeletedReason(
			entity_class: static::class,
			reason: $reason,
			entity_ids: $ids
		);
	}
	
	/**
	 * @param EShopEntity_Basic $entity_to_be_deleted
	 * @param EShopEntity_CanNotBeDeletedReason[] &$reasons
	 * @return bool
	 */
	public static function checkIfItCanBeDeleted( EShopEntity_Basic $entity_to_be_deleted, array &$reasons=[] ) : bool
	{
		return true;
	}
	
	public function __clone() : void
	{
		$this->id = 0;
		$this->setIsNew( true );
	}
}