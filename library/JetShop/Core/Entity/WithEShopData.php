<?php
namespace JetShop;

use Jet\DataModel;
use Jet\DataModel_Definition;
use Jet\DataModel_Definition_Property_DataModel;
use Jet\Form_Definition;
use Jet\Form_Field;
use Jet\Logger;
use JetApplication\Entity_Basic;
use JetApplication\Entity_WithEShopData_EShopData;
use JetApplication\FulltextSearch_IndexDataProvider;
use JetApplication\EShops;
use JetApplication\EShop;

#[DataModel_Definition]
abstract class Core_Entity_WithEShopData extends Entity_Basic
{
	
	#[DataModel_Definition(
		type: DataModel::TYPE_STRING,
		max_len: 100,
	)]
	#[Form_Definition(
		type: Form_Field::TYPE_INPUT,
		label: 'Internal name:'
	)]
	protected string $internal_name = '';
	
	#[DataModel_Definition(
		type: DataModel::TYPE_STRING,
		max_len: 100,
		is_key: true,
	)]
	#[Form_Definition(
		type: Form_Field::TYPE_INPUT,
		label: 'Internal code:',
		error_messages: [
			'code_used' => 'This code is already used'
		]
	)]
	protected string $internal_code = '';
	
	
	#[DataModel_Definition(
		type: DataModel::TYPE_STRING,
		max_len: 65536
	)]
	#[Form_Definition(
		type: Form_Field::TYPE_TEXTAREA,
		label: 'Internal notes:'
	)]
	protected string $internal_notes = '';
	
	
	#[DataModel_Definition(
		type: DataModel::TYPE_BOOL,
	)]
	protected bool $is_active = false;
	
	
	/**
	 * @var Entity_WithEShopData_EShopData[]
	 */
	#[Form_Definition(is_sub_forms: true)]
	protected array $eshop_data = [];
	
	protected static array $scope = [];
	
	protected static array $loaded_items = [];
	
	
	public static function get( int $id ) : ?static
	{
		$key = get_called_class().':'.$id;
		
		if(!array_key_exists($key, static::$loaded_items)) {
			$where['id'] = $id;
			
			static::$loaded_items[ $key ] = static::load( $where );
		}
		
		
		return static::$loaded_items[ $key ];
	}
	
	public function __construct()
	{
		$this->checkShopData();
	}
	
	public function setId( int $id ) : void
	{
		$this->checkShopData();
		$this->id = $id;
	}
	
	
	public function afterLoad() : void
	{
		$this->checkShopData();
	}
	
	public function checkShopData() : void
	{
		/**
		 * @var DataModel_Definition_Property_DataModel $def
		 */
		$def = static::getDataModelDefinition()->getProperty('eshop_data');
		
		$eshop_data_class = $def->getValueDataModelClass();

		foreach( EShops::getList() as $eshop ) {
			$key = $eshop->getKey();
			
			if(!isset( $this->eshop_data[$key])) {
				
				/**
				 * @var Entity_WithEShopData_EShopData $sh
				 */
				$sh = new $eshop_data_class();
				$sh->setEntityId( $this->getId() );
				$sh->setEshop( $eshop );
				
				$this->eshop_data[$key] = $sh;
				
				if($this->getId()) {
					$this->eshop_data[$key]->actualizeRelations( $this->getIDController() );
					$this->eshop_data[$key]->save();
				}
			}
			
			$this->eshop_data[$key]->setEshop( $eshop );
		}
	}
	
	public static function internalCodeUsed( string $internal_code, int $skip_id=0 ) : bool
	{
		return (bool)static::dataFetchCol(['id'], [
			'internal_code'=>$internal_code,
			'AND',
			'id !=' => $skip_id
		]);
	}
	
	public static function getScope() : array
	{
		if(isset(static::$scope[static::class])) {
			return static::$scope[static::class];
		}
		
		static::$scope[static::class] = static::dataFetchPairs(
				select: [
					'id',
					'internal_name'
				], order_by: ['internal_name']);
		
		return static::$scope[static::class];
	}
	
	public static function getOptionsScope() : array
	{
		return [0=>'']+static::getScope();
	}
	
	
	
	public function isActive() : bool
	{
		return $this->is_active;
	}
	
	
	public function getId() : int
	{
		return $this->id;
	}
	
	
	public function getInternalName(): string
	{
		return $this->internal_name;
	}
	
	public function setInternalName( string $internal_name ): void
	{
		$this->internal_name = $internal_name;
	}
	
	
	public function getInternalCode() : string
	{
		return $this->internal_code;
	}
	
	public function setInternalCode( string $internal_code ) : void
	{
		$this->internal_code = $internal_code;
		foreach( EShops::getList() as $eshop ) {
			$this->getEshopData( $eshop )->setInternalCode( $this->internal_code );
		}
		
	}
	
	
	public function getInternalNotes(): string
	{
		return $this->internal_notes;
	}
	
	public function setInternalNotes( string $internal_notes ): void
	{
		$this->internal_notes = $internal_notes;
	}
	
	
	public function activate() : void
	{
		if($this->is_active) {
			return;
		}
		
		$this->is_active = true;
		static::updateData(data: ['is_active'=>$this->is_active], where: ['id'=>$this->id]);
		
		foreach( EShops::getList() as $eshop ) {
			$this->getEshopData( $eshop )->_setEntityIsActive( $this->is_active );
		}
		
		$entity_type = $this->getEntityType();
		
		Logger::success(
			event: 'entity_activated:'.$entity_type,
			event_message: 'Entity '.$entity_type.' \''.$this->getInternalName().'\' ('.$this->id.') activated',
			context_object_id: $this->id,
			context_object_name: $this->getAdminTitle()
		);
		
		
		if($this instanceof FulltextSearch_IndexDataProvider) {
			$this->updateFulltextSearchIndex();
		}
		
	}
	
	public function deactivate() : void
	{
		if(!$this->is_active) {
			return;
		}
		
		$this->is_active = false;
		static::updateData(data: ['is_active'=>$this->is_active], where: ['id'=>$this->id]);
		
		foreach( EShops::getList() as $eshop ) {
			$this->getEshopData( $eshop )->_setEntityIsActive( $this->is_active );
		}
		
		$entity_type = $this->getEntityType();
		
		Logger::success(
			event: 'entity_deactivated:'.$entity_type,
			event_message: 'Entity '.$entity_type.' \''.$this->getInternalName().'\' ('.$this->getId().') deactivated',
			context_object_id: $this->getId(),
			context_object_name: $this->getAdminTitle()
		);
		
		if($this instanceof FulltextSearch_IndexDataProvider) {
			$this->updateFulltextSearchIndex();
		}
	}
	
	public function activateEShopData( EShop $eshop ) : void
	{
		$sd = $this->getEshopData( $eshop );

		if( $sd->isActiveForShop() ) {
			return;
		}
		
		$sd->_activate();
		
		$entity_type = $this->getEntityType();
		$entity_id = $this->getId();
		
		Logger::success(
			event: 'entity_eshop_data_activated:'.$entity_type,
			event_message: 'Entity '.$entity_type.' \''.$this->getInternalName().'\' ('.$entity_id.') eshop data '.$eshop->getKey().' activated',
			context_object_id: $entity_id.':'.$eshop->getKey(),
			context_object_name: $this->getAdminTitle()
		);
		
		
		if($this instanceof FulltextSearch_IndexDataProvider) {
			$this->updateFulltextSearchIndex();
		}
	}
	
	public function deactivateEShopData( EShop $eshop ) : void
	{
		$sd = $this->getEshopData( $eshop );
		if( !$sd->isActiveForShop() ) {
			return;
		}
		
		$sd->_deactivate();
		
		$entity_type = $this->getEntityType();
		$entity_id = $this->getId();
		
		Logger::success(
			event: 'entity_eshop_data_deactivated:'.$entity_type,
			event_message: 'Entity '.$entity_type.' \''.$this->getInternalName().'\' ('.$entity_id.') eshop data '.$eshop->getKey().' deactivated',
			context_object_id: $entity_id.':'.$eshop->getKey(),
			context_object_name: $this->getAdminTitle()
		);
		
		
		if($this instanceof FulltextSearch_IndexDataProvider) {
			$this->updateFulltextSearchIndex();
		}
	}
	
	public function activateCompletely() : void
	{
		
		$updated = false;
		
		if(!$this->is_active) {
			$this->is_active = true;
			static::updateData(data: ['is_active'=>$this->is_active], where: ['id'=>$this->id]);
			
			foreach( EShops::getList() as $eshop ) {
				$this->getEshopData( $eshop )->_setEntityIsActive( $this->is_active );
			}
			
			$entity_type = $this->getEntityType();
			
			Logger::success(
				event: 'entity_activated:'.$entity_type,
				event_message: 'Entity '.$entity_type.' \''.$this->getInternalName().'\' ('.$this->id.') activated',
				context_object_id: $this->id,
				context_object_name: $this->getAdminTitle()
			);
			
			$updated = true;
		}
		
		foreach( EShops::getList() as $eshop) {
			$sd = $this->getEshopData( $eshop );
			
			if( !$sd->isActiveForShop() ) {
				$sd->_activate();
				
				$entity_type = $this->getEntityType();
				$entity_id = $this->getId();
				
				Logger::success(
					event: 'entity_eshop_data_activated:'.$entity_type,
					event_message: 'Entity '.$entity_type.' \''.$this->getInternalName().'\' ('.$entity_id.') eshop data '.$eshop->getKey().' activated',
					context_object_id: $entity_id.':'.$eshop->getKey(),
					context_object_name: $this->getAdminTitle()
				);
				
				$updated = true;
			}
		}
		
		
		
		if(
			$updated &&
			$this instanceof FulltextSearch_IndexDataProvider
		) {
			$this->updateFulltextSearchIndex();
		}
		
	}
	
	protected function _getEshopData( ?EShop $eshop=null ) : Entity_WithEShopData_EShopData
	{
		if(!isset( $this->eshop_data[$eshop->getKey()])) {
			$this->checkShopData();
		}
		
		return $this->eshop_data[$eshop->getKey()];
	}
	
	
	abstract public function getEshopData( ?EShop $eshop = null ): Entity_WithEShopData_EShopData;
	
	public function getAdminTitle() : string
	{
		$code = $this->internal_code?:$this->id;
		
		return $this->internal_name.' ('.$code.')';
	}
	
	public function afterAdd(): void
	{
		parent::afterAdd();
		
		if($this instanceof FulltextSearch_IndexDataProvider) {
			$this->updateFulltextSearchIndex();
		}
	}
	
	public function afterUpdate(): void
	{
		parent::afterAdd();
		
		if($this instanceof FulltextSearch_IndexDataProvider) {
			$this->updateFulltextSearchIndex();
		}
	}
	
	
	public function afterDelete(): void
	{
		parent::afterAdd();
		
		if($this instanceof FulltextSearch_IndexDataProvider) {
			$this->removeFulltextSearchIndex();
		}
	}
	
}