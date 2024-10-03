<?php
namespace JetShop;

use Jet\DataModel;
use Jet\DataModel_Definition;
use Jet\Form_Definition;
use Jet\Form_Field;
use Jet\Logger;
use JetApplication\Entity_Basic;
use JetApplication\FulltextSearch_IndexDataProvider;

#[DataModel_Definition]
abstract class Core_Entity_Common extends Entity_Basic
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
	
	
	public function isActive() : bool
	{
		return $this->is_active;
	}
	
	
	public function getId() : int
	{
		return $this->id;
	}
	
	public function setId( int $id ) : void
	{
		$this->id = $id;
	}
	
	public function getInternalName(): string
	{
		return $this->internal_name;
	}
	
	public function setInternalName( string $internal_name ): void
	{
		$this->internal_name = $internal_name;
	}
	
	public function getInternalCode(): string
	{
		return $this->internal_code;
	}
	
	public function setInternalCode( string $internal_code ): void
	{
		$this->internal_code = $internal_code;
	}
	
	public function getInternalNotes(): string
	{
		return $this->internal_notes;
	}
	

	public function setInternalNotes( string $internal_notes ): void
	{
		$this->internal_notes = $internal_notes;
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
	
	
	public function getAdminTitle() : string
	{
		$code = $this->internal_code?:$this->id;
		
		return $this->internal_name.' ('.$code.')';
	}
	
	public function activate() : void
	{
		if($this->is_active) {
			return;
		}
		
		$this->is_active = true;
		static::updateData(data: ['is_active'=>$this->is_active], where: ['id'=>$this->id]);
		
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