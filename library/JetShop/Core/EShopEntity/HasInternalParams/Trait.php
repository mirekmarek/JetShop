<?php
namespace JetShop;

use Jet\DataModel;
use Jet\DataModel_Definition;
use Jet\Form_Definition;
use Jet\Form_Field;

trait Core_EShopEntity_HasInternalParams_Trait {
	
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
	
	protected static array $scope = [];
	
	public static function getByInternalCode( string $internal_code ) : ?static
	{
		$id = static::dataFetchOne(select: ['id'], where: ['internal_code'=>$internal_code]);
		if(!$id) {
			return null;
		}
		
		return static::get( $id );
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
	
}