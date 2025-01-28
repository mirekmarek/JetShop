<?php
namespace JetShop;

use Jet\DataModel;
use Jet\DataModel_Definition;

use JetApplication\Accessories_Group_Product;
use JetApplication\Entity_Admin_Interface;
use JetApplication\Entity_Admin_Trait;
use JetApplication\Entity_Common;
use JetApplication\Entity_Definition;
use JetApplication\Product;
use JetApplication\Admin_Managers_AccessoriesGroups;

#[DataModel_Definition(
	name: 'accessories_group',
	database_table_name: 'accessories_groups',
)]
#[Entity_Definition(
	admin_manager_interface: Admin_Managers_AccessoriesGroups::class
)]
abstract class Core_Accessories_Group extends Entity_Common implements Entity_Admin_Interface
{
	use Entity_Admin_Trait;
	
	/**
	 * @var Accessories_Group_Product[]
	 */
	#[DataModel_Definition(
		type: DataModel::TYPE_DATA_MODEL,
		data_model_class: Accessories_Group_Product::class
	)]
	protected array $products = [];
	
	
	public function getProductIds() : array
	{
		return array_keys( $this->products );
	}
	
	public function addProduct( int $product_id ) : bool
	{
		if(
			isset( $this->products[$product_id] ) ||
			!Product::exists( $product_id )
		) {
			return false;
		}
		
		$product_assoc = new Accessories_Group_Product();
		$product_assoc->setAccessoriesGroupId( $this->id );
		$product_assoc->setProductId( $product_id );
		$product_assoc->setPriority( count($this->products)+1 );
		
		$product_assoc->save();
		
		$this->products[$product_id] = $product_assoc;
		
		return true;
	}
	
	public function removeProduct( int $property_id ) : bool
	{
		if(!isset( $this->products[$property_id] )) {
			return false;
		}
		
		$this->products[$property_id]->delete();
		unset($this->products[$property_id]);
		
		$priority = 0;
		foreach($this->products as $property) {
			$priority++;
			
			$property->setPriority( $priority );
			$property->save();
		}
		
		return true;
	}
	
	/**
	 * @return Accessories_Group_Product[]
	 */
	public function getProducts() : array
	{
		return $this->products;
	}
	
	public function sortProducts( array $products_ids ) : void
	{
		$p = 0;
		foreach($products_ids as $id) {
			if(!isset($this->products[$id])) {
				continue;
			}
			
			$this->products[$id]->setPriority( $p );
			$this->products[$id]->save();
			$p++;
		}
	}
	
	public static function getAccessoriesIds( array $gorup_ids ) : array
	{
		if(!$gorup_ids) {
			return [];
		}
		
		$active_group_ids = static::dataFetchCol(
			select: ['id'],
			where: [
				'is_active' => true,
				'AND',
				'id' => $gorup_ids
			],
			raw_mode: true
		);
		
		if(!$active_group_ids) {
			return [];
		}
		
		return Accessories_Group_Product::dataFetchCol(
			select: ['product_id'],
			where: ['accessories_group_id' =>$active_group_ids],
			group_by: ['product_id'],
			order_by: ['priority'],
			raw_mode: true
		);
		
		
	}
}