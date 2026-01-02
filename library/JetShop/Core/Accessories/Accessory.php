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
use JetApplication\Accessories_Group;

#[DataModel_Definition(
	name: 'accessories_accessory',
	database_table_name: 'accessories_accessory',
	parent_model_class: Accessories_Group::class,
	default_order_by: [
		'+priority'
	],
	id_controller_class: DataModel_IDController_Passive::class
)]
abstract class Core_Accessories_Accessory extends DataModel
{
	
	#[DataModel_Definition(
		type: DataModel::TYPE_INT,
		is_id: true,
		is_key: true
	)]
	protected int $product_id = 0;
	
	#[DataModel_Definition(
		type: DataModel::TYPE_INT,
		is_id: true,
		is_key: true
	)]
	protected int $accessory_product_id = 0;
	
	#[DataModel_Definition(
		type: DataModel::TYPE_INT
	)]
	protected int $priority = 0;
	

	public function setProductId( int $value ) : void
	{
		$this->product_id = $value;
	}
	
	public function getProductId() : string
	{
		return $this->product_id;
	}
	
	public function getAccessoryProductId(): int
	{
		return $this->accessory_product_id;
	}
	
	public function setAccessoryProductId( int $accessory_product_id ): void
	{
		$this->accessory_product_id = $accessory_product_id;
	}
	
	
	public function setPriority( int $value ) : void
	{
		$this->priority = $value;
	}
	
	public function getPriority() : int
	{
		return $this->priority;
	}
	
	public static function getAccessoryIds( int $product_id ): array
	{
		return static::dataFetchCol(
			select: ['accessory_product_id'],
			where: [
				'product_id' => $product_id,
			],
			order_by: ['priority'],
		);
	}
	
	public static function setAccessory( int $product_id, array $accessory_ids ) : void
	{
		static::dataDelete(where: ['product_id' => $product_id]);
		
		$p = 0;
		foreach($accessory_ids as $accessory_id) {
			$p++;
			$item = new static();
			$item->setAccessoryProductId( $accessory_id );
			$item->setProductId( $product_id );
			$item->setPriority( $p );
			$item->save();
		}
	}
}
