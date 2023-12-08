<?php
namespace JetShop;

use Jet\Data_Tree;
use Jet\DataModel;
use Jet\DataModel_Definition;
use JetApplication\Category_Product;
use JetApplication\Category_ShopData;
use JetApplication\Entity_WithIDAndShopData;
use JetApplication\Product_ShopData;
use JetApplication\Shops;
use JetApplication\Shops_Shop;


#[DataModel_Definition(
	name: 'category',
	database_table_name: 'categories',
)]
abstract class Core_Category extends Entity_WithIDAndShopData {
	
	
	#[DataModel_Definition(
		type: DataModel::TYPE_INT,
		is_key: true,
	)]
	protected int $root_id = 0;

	#[DataModel_Definition(
		type: DataModel::TYPE_INT,
		is_key: true,
	)]
	protected int $parent_id = 0;
	
	#[DataModel_Definition(
		type: DataModel::TYPE_STRING,
		max_len: 65536,
	)]
	protected string $path = '';

	#[DataModel_Definition(
		type: DataModel::TYPE_STRING,
		max_len: 65536,
	)]
	protected string $children = '';

	#[DataModel_Definition(
		type: DataModel::TYPE_STRING,
		max_len: 65536,
	)]
	protected string $branch_children = '';

	#[DataModel_Definition(
		type: DataModel::TYPE_INT,
	)]
	protected int $priority = 0;
	
	#[DataModel_Definition(
		type: DataModel::TYPE_INT,
		is_key: true
	)]
	protected int $kind_of_product_id = 0;

	/**
	 * @var Category_ShopData[]
	 */
	#[DataModel_Definition(
		type: DataModel::TYPE_DATA_MODEL,
		data_model_class: Category_ShopData::class
	)]
	protected array $shop_data = [];
	
	/**
	 * @var Category_Product[]
	 */
	#[DataModel_Definition(
		type: DataModel::TYPE_DATA_MODEL,
		data_model_class: Category_Product::class
	)]
	protected array $products = [];

	

	public function setParentId( int $parent_id, bool $update_priority=true, bool $save=true ) : void
	{
		$this->parent_id = $parent_id;

		if($update_priority) {
			$data = static::dataFetchAll(
				select:[
					'id' => 'id',
					'parent_id' => 'parent_id',
					'priority' => 'priority',
					'name' => 'internal_name',
					'is_active' => 'is_active',
				],
				order_by: 'priority'
			);
			
			$tree = new Data_Tree();
			$tree->getRootNode()->setLabel('Root');
			$tree->setAdoptOrphans(true);
			
			$tree->setData( $data );
			
			

			$this->priority = 1;
			foreach( $tree->getNode( $parent_id )->getChildren() as $ch ) {
				$ch_p = $ch->getData()['priority'];

				if($ch_p>=$this->priority) {
					$this->setPriority( $ch_p+1 );
				}
			}
		}
		
		foreach($this->shop_data as $sd) {
			$sd->setParentId( $parent_id, false );
			$sd->setPriority( $this->priority, false );
		}
		
		if($save) {
			$this->save();
			
			$old_root_id = $this->root_id;
			
			static::actualizeTreeData();
			$new_root_id = static::dataFetchCol(['root_id'], ['id'=>$this]);
			
			static::actualizeProductAssoc( category_id: $old_root_id );
			if($new_root_id!=$old_root_id) {
				static::actualizeProductAssoc( category_id: $new_root_id );
			}
		}
		
	}
	
	public function activate(): void
	{
		parent::activate();
		
		static::actualizeBranchProductAssoc( $this->root_id );
	}
	
	public function deactivate(): void
	{
		parent::deactivate();
		
		static::actualizeBranchProductAssoc( $this->root_id );
	}
	
	public function getParentId() : int
	{
		return $this->parent_id;
	}

	public function getPriority() : int
	{
		return $this->priority;
	}

	public function setPriority( int $priority, bool $save=true ) : void
	{
		$this->priority = $priority;
		foreach($this->shop_data as $sd) {
			static::updateData(data: ['priority'=>$this->priority], where: ['id'=>$this->id]);
		}
	}
	
	/**
	 * @return int
	 */
	public function getKindOfProductId(): int
	{
		return $this->kind_of_product_id;
	}
	
	public function setKindOfProductId( int $kind_of_product_id ): void
	{
		$this->kind_of_product_id = $kind_of_product_id;
		$this->save();
	}
	
	
	public function getShopData( ?Shops_Shop $shop=null ) : Category_ShopData
	{
		return $this->shop_data[$shop ? $shop->getKey() : Shops::getCurrent()->getKey()];
	}
	
	
	public static function actualizeTreeData() : void
	{
		$active_categories = static::dataFetchAll(
			select:[
				'id' => 'id',
				'parent_id' => 'parent_id',
				'name' => 'internal_name',
			],
			order_by: 'priority'
		);
		
		
		$tree = new Data_Tree();
		$tree->getRootNode()->setLabel('Root');
		
		$tree->setIgnoreOrphans(true);
		
		$tree->setData( $active_categories );
		
		foreach( $tree as $node ) {
			if($node->getIsRoot()) {
				continue;
			}
			
			$id = (int)$node->getId();
			
			$path = [];
			$children = [];
			$branch_children = [];
			
			foreach( $node->getPathFromRoot() as $p_node ) {
				$p_id = (int)$p_node->getId();
				if( !$p_id ) {
					continue;
				}
				
				$path[] = $p_id;
			}
			
			foreach( $node->getChildren() as $p_node ) {
				$p_id = (int)$p_node->getId();
				if( !$p_id ) {
					continue;
				}
				
				$children[] = $p_id;
			}
			
			
			foreach( $node->getAllChildrenIds() as $p_id ) {
				$p_id = (int)$p_id;
				if( !$p_id ) {
					continue;
				}
				
				$branch_children[] = $p_id;
			}
			
			
			$root_id = $path[0];
			$path = implode( ',', $path );
			$children = implode( ',', $children );
			$branch_children = implode( ',', $branch_children );
			
			static::updateData(
				[
					'root_id' => $root_id,
					'path' => $path,
					'children' => $children,
					'branch_children' => $branch_children,
				],
				[
					'id' => $id,
					'AND',
					[
						'root_id !=' => $root_id,
						'OR',
						'path !=' => $path,
						'OR',
						'children !=' => $children,
						'OR',
						'branch_children !=' => $branch_children,
					]
				]
			);
			
			
			foreach( Shops::getList() as $shop ) {
				$where = $shop->getWhere();
				$where[] = 'AND';
				$where['entity_id'] = $id;
				$where[] = 'AND';
				$where[] = [
					'root_id !=' => $root_id,
					'OR',
					'path !=' => $path,
					'OR',
					'children !=' => $children,
					'OR',
					'branch_children !=' => $branch_children,
				];
				
				Category_ShopData::updateData(
					[
						'root_id' => $root_id,
						'path' => $path,
						'children' => $children,
						'branch_children' => $branch_children,
					],
					$where
				);
			}
			
		}
		
		
		
		
		
		foreach( Shops::getList() as $shop ) {
			$active_categories = Category_ShopData::dataFetchAssoc(
				select: [
					'id' => 'entity_id',
					'parent_id' => 'parent_id',
					'name' => 'name',
				],
				where: Category_ShopData::getActiveQueryWhere( $shop )
			);
			
			
			$tree = new Data_Tree();
			$tree->getRootNode()->setLabel( 'Root' );
			
			
			$tree->setIgnoreOrphans( true );
			
			$tree->setData( $active_categories );
			
			$active_ids = [0];
			
			foreach( $tree as $node ) {
				if( $node->getIsRoot() ) {
					continue;
				}
				
				
				$id = (int)$node->getId();
				$active_ids[] = $id;
				
				$branch_children = [];
				
				foreach( $node->getAllChildrenIds() as $p_id ) {
					$p_id = (int)$p_id;
					if( !$p_id ) {
						continue;
					}
					
					$branch_children[] = $p_id;
				}
				$branch_children = implode(',', $branch_children);
				
				$where = $shop->getWhere();
				$where[] = 'AND';
				$where['entity_id'] = $id;
				$where[] = 'AND';
				$where['active_branch_children !='] = $branch_children;
				
				Category_ShopData::updateData(
					[
						'active_branch_children' => $branch_children,
					],
					$where
				);
			}
			
			$non_active_categories = Category_ShopData::dataFetchCol(
				select: [
					'entity_id',
				],
				where: Category_ShopData::getNonActiveQueryWhere( $shop )
			);
			
			if($non_active_categories) {
				$where = $shop->getWhere();
				$where[] = 'AND';
				$where['entity_id'] = $non_active_categories;
				$where[] = 'AND';
				$where['active_branch_children !='] = '';
				
				Category_ShopData::updateData(
					[
						'active_branch_children' => '',
					],
					$where
				);
			}
			
		}
			
			
	}
	
	public function addProduct( int $product_id ) : void
	{
		if(!isset($this->products[$product_id])) {
			$this->products[$product_id] = new Category_Product();
			$this->products[$product_id]->setCategoryId( $this->id );
			$this->products[$product_id]->setProductId( $product_id );
			$this->products[$product_id]->setPriority( count($this->products) );
			$this->products[$product_id]->save();
		}
		
		
		static::actualizeBranchProductAssoc( $this->root_id );
	}
	
	public function removeProduct( int $product_id ) : void
	{
		if(!isset($this->products[$product_id])) {
			return;
		}
		
		$this->products[$product_id]->delete();
		unset($this->products[$product_id]);
		
		$i = 0;
		foreach($this->products as $p) {
			$i++;
			$p->setPriority( $i );
			$p->save();
		}
		
		static::actualizeBranchProductAssoc( $this->root_id );
	}
	
	public function getProductIds() : array
	{
		return array_keys($this->products);
	}
	
	public static function actualizeProductAssoc( int $category_id=null, int $product_id=null ) : void
	{
		if($product_id) {
			$category_ids = Category_Product::dataFetchCol(
				select: ['category_id'],
				where: ['product_id'=>$product_id]
			);

			$root_ids = static::dataFetchCol(select: ['root_id'], where: ['id'=>$category_ids]);
			
			$root_ids = array_unique($root_ids);
			
			foreach($root_ids as $root_id) {
				static::actualizeBranchProductAssoc( $root_id );
			}
			
			return;
		}
		
		if($category_id) {
			$root_category_id = static::dataFetchOne(select: ['root_id'], where: ['id'=>$category_id]);
			
			if($root_category_id) {
				static::actualizeBranchProductAssoc( $root_category_id );
			}
		}
	}
	
	public static function actualizeBranchProductAssoc( int $root_category_id ) : void
	{
		$branch_category_ids =
			array_merge(
				[$root_category_id],
				explode(',', static::dataFetchOne( select:['branch_children'], where: ['id'=>$root_category_id] ))
			);

		$_category_products_map = Category_Product::dataFetchAll(
			select: ['category_id', 'product_id'],
			where: ['category_id'=>$branch_category_ids],
			group_by:['category_id', 'priority']
		);
		
		$category_products_map = [];
		$all_product_ids = [];
		foreach($_category_products_map as $m) {
			$category_id = (int)$m['category_id'];
			$product_id = (int)$m['product_id'];
			if(!isset($category_products_map[$category_id])) {
				$category_products_map[$category_id] = [];
			}
			
			$category_products_map[$category_id][] = $product_id;
			
			if(!in_array($product_id, $all_product_ids)) {
				$all_product_ids[] = $product_id;
			}
		}
		
		$active_products = [];
		
		if($all_product_ids) {
			foreach( Shops::getList() as $shop ) {
				$where = Product_ShopData::getActiveQueryWhere( $shop );
				$where[] = 'AND';
				$where[] = [
					'entity_id' => $all_product_ids,
				];
				$active_products[$shop->getKey()] = Product_ShopData::dataFetchCol(
					select: ['entity_id'],
					where: $where
				);
			}
		} else {
			foreach( Shops::getList() as $shop ) {
				$active_products[$shop->getKey()] = [];
			}
			
		}
		
		
		foreach( Shops::getList() as $shop ) {
			$where = $shop->getWhere();
			$where[] = 'AND';
			$where['entity_id'] = $branch_category_ids;
			
			$_categories = Category_ShopData::dataFetchAll(
				select: [
					'entity_id',
					
					'entity_is_active',
					'is_active_for_shop',
					
					'children',
					'branch_children',
					'active_branch_children',
					
					'product_ids',
					'products_count',
					
					'branch_product_ids',
					'branch_products_count',
				],
				where: $where
			);
			
			$categories = [];
			foreach( $_categories as $c ) {
				$id = $c['entity_id'];
				$c['is_active']              = ($c['entity_is_active'] && $c['is_active_for_shop']);
				$c['children']               = $c['children'] ? explode(',', $c['children']) : [];
				$c['branch_children']        = $c['branch_children'] ? explode(',', $c['branch_children']) : [];
				$c['active_branch_children'] = $c['active_branch_children'] ? explode(',', $c['active_branch_children']) : [];
				
				if(isset($category_products_map[$id])) {
					$c['new_product_ids'] = array_intersect( $category_products_map[$id], $active_products[$shop->getKey()] );
					$c['new_products_count'] = count( $c['new_product_ids'] );
				} else {
					$c['new_product_ids'] = [];
					$c['new_products_count'] = 0;
				}
				$c['new_branch_product_ids'] = [];
				$c['new_branch_products_count'] = 0;
				
				
				$categories[$id] = $c;
			}
			
			foreach($categories as $id=>$c) {
				$categories[$id]['new_branch_product_ids'] = $c['new_product_ids'];
				
				foreach( $categories[$id]['active_branch_children'] as $ch_id ) {
					$categories[$id]['new_branch_product_ids'] = array_merge(
						$categories[$id]['new_branch_product_ids'],
						$categories[$ch_id]['new_product_ids']
					);
				}
				
				$categories[$id]['new_branch_product_ids'] = array_unique($categories[$id]['new_branch_product_ids']);
				$categories[$id]['new_branch_products_count'] = count( $categories[$id]['new_branch_product_ids'] );
			}
			
			foreach($categories as $id=>$c) {
				
				$c['new_product_ids'] = implode(',', $c['new_product_ids']);
				$c['new_branch_product_ids'] = implode(',', $c['new_branch_product_ids']);
				
				if(
					$c['new_product_ids']!=$c['product_ids'] ||
					$c['new_branch_product_ids']!=$c['branch_product_ids']
				) {
					$where = $shop->getWhere();
					$where[] = 'AND';
					$where['entity_id'] = $id;
					
					Category_ShopData::updateData(
						data: [
							'product_ids'           => $c['new_product_ids'],
							'branch_product_ids'    => $c['new_branch_product_ids'],
							'products_count'        => $c['new_products_count'],
							'branch_products_count' => $c['new_branch_products_count'],
						],
						where: $where
					);
				}
			}
		}
	}
	
	public static function productActivated( int $product_id ) : void
	{
		static::actualizeProductAssoc( product_id: $product_id );
	}
	
	
	public static function productDeactivated( int $product_id ) : void
	{
		static::actualizeProductAssoc( product_id: $product_id );
	}
	
	public static function productDeleted( int $product_id ) : void
	{
		$category_ids = Category_Product::dataFetchCol(select:['category_id'], where:['product_id'=>$product_id]);
		
		if(!$category_ids) {
			return;
		}
		
		$root_ids = static::dataFetchCol(select: ['root_id'], where: ['id'=>$category_ids]);
		$root_ids = array_unique($root_ids);
		
		Category_Product::dataDelete([
			'product_id' => $product_id
		]);
		
		foreach($root_ids as $root_id) {
			static::actualizeBranchProductAssoc( $root_id );
		}
		
	}
}