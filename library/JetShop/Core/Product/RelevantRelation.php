<?php
/**
 * @copyright Copyright (c) Miroslav Marek <mirek.marek@web-jet.cz>
 * @license EUPL 1.2  https://eupl.eu/1.2/en/
 * @author Miroslav Marek <mirek.marek@web-jet.cz>
 */
namespace JetShop;


use Jet\DataModel;
use Jet\DataModel_Definition;
use Jet\DataModel_IDController_Passive;
use JetApplication\EShopEntity_Basic;

#[DataModel_Definition(
	name: 'product_relevant_relation',
	database_table_name: 'products_relevant_relation',
	id_controller_class: DataModel_IDController_Passive::class,
)]
abstract class Core_Product_RelevantRelation extends DataModel
{
	
	#[DataModel_Definition(
		type: DataModel::TYPE_INT,
		is_id: true,
		is_key: true
	)]
	protected int $product_id = 0;
	
	#[DataModel_Definition(
		type: DataModel::TYPE_STRING,
		max_len: 100,
		is_id: true,
		is_key: true
	)]
	protected string $entity_type = '';
	
	#[DataModel_Definition(
		type: DataModel::TYPE_INT,
		is_id: true,
		is_key: true
	)]
	protected int $entity_id = 0;
	
	
	public function getProductId() : int
	{
		return $this->product_id;
	}
	
	public function setProductId( int $product_id ) : void
	{
		$this->product_id = $product_id;
	}
	
	public function getEntityType(): string
	{
		return $this->entity_type;
	}
	
	public function setEntityType( string $entity_type ): void
	{
		$this->entity_type = $entity_type;
	}
	
	public function getEntityId() : int
	{
		return $this->entity_id;
	}
	
	public function setEntityId( int $entity_id ) : void
	{
		$this->entity_id = $entity_id;
	}
	
	public static function add( EShopEntity_Basic $entity, int $product_id ) : void
	{
		$new = new static();
		$new->setEntityType( $entity::getEntityType() );
		$new->setEntityId( $entity->getId() );
		$new->setProductId( $product_id );
		$new->save();
	}
	
	public static function remove( EShopEntity_Basic $entity, int $product_id ) : void
	{
		static::dataDelete([
			'entity_type' => $entity::getEntityType(),
			'AND',
			'entity_id' => $entity->getId(),
			'AND',
			'product_id' => $product_id
		]);
		
	}
	
	public static function removeAll( EShopEntity_Basic $entity ) : void
	{
		static::dataDelete([
			'entity_type' => $entity::getEntityType(),
			'AND',
			'entity_id' => $entity->getId()
		]);
		
	}
	
	public static function get( EShopEntity_Basic $entity ) : array
	{
		return static::dataFetchCol(
			['product_id'],
			[
				'entity_type' => $entity::getEntityType(),
				'AND',
				'entity_id' => $entity->getId()
			],
			raw_mode: true
		);
	}
	
	
}