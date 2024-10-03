<?php
namespace JetShop;

use Jet\DataModel_Definition;
use JetApplication\Entity_Basic;

#[DataModel_Definition]
abstract class Core_Entity_Simple extends Entity_Basic
{
	
	
	protected static array $scope = [];
	
	public function getId() : int
	{
		return $this->id;
	}
	
	public function setId( int $id ) : void
	{
		$this->id = $id;
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
	
	
	
	public function afterAdd(): void
	{
		parent::afterAdd();
	}
	
	public function afterUpdate(): void
	{
		parent::afterAdd();
	}
	
	
	public function afterDelete(): void
	{
		parent::afterAdd();
	}
	
}