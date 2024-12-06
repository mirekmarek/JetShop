<?php
namespace JetShop;


use Jet\DataModel;
use Jet\DataModel_Definition;
use Jet\DataModel_IDController_Passive;

#[DataModel_Definition(
	name: 'products_accessories_group',
	database_table_name: 'products_accessories_groups',
	id_controller_class: DataModel_IDController_Passive::class
)]
abstract class Core_Product_Accessories_Group extends DataModel
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
	protected int $group_id = 0;
	
	public function getProductId(): int
	{
		return $this->product_id;
	}
	
	public function setProductId( int $product_id ): void
	{
		$this->product_id = $product_id;
	}
	
	public function getGroupId(): int
	{
		return $this->group_id;
	}
	
	public function setGroupId( int $group_id ): void
	{
		$this->group_id = $group_id;
	}
	
	public static function getGroupIds( int $product_id ) : array
	{
		return static::dataFetchCol(
			select: ['group_id'],
			where: ['product_id'=>$product_id]
		);
	}
	
	public static function setGroups( int $product_id, array $gorup_ids ) : void
	{
		static::dataDelete(['product_id'=>$product_id]);
		foreach($gorup_ids as $g_id) {
			$assoc = new static();
			$assoc->setProductId($product_id);
			$assoc->setGroupId($g_id);
			$assoc->save();
		}
	}
	
}