<?php
/**
 * @copyright Copyright (c) Miroslav Marek <mirek.marek@web-jet.cz>
 * @license EUPL 1.2  https://eupl.eu/1.2/en/
 * @author Miroslav Marek <mirek.marek@web-jet.cz>
 */
namespace JetShop;


use Jet\Data_Tree;
use Jet\DataModel;
use Jet\DataModel_Definition;
use Jet\DataModel_IDController_AutoIncrement;
use JetApplication\Admin_Managers;
use JetApplication\Admin_Managers_Category;
use JetApplication\Category;
use JetApplication\Category_Product;
use JetApplication\Category_EShopData;
use JetApplication\EShopEntity_Admin_WithEShopData_Interface;
use JetApplication\EShopEntity_Admin_WithEShopData_Trait;
use JetApplication\EShopEntity_Basic;
use JetApplication\EShopEntity_CanNotBeDeletedReason;
use JetApplication\EShopEntity_HasImages_Interface;
use JetApplication\EShopEntity_WithEShopData;
use JetApplication\EShopEntity_WithEShopData_HasImages_Trait;
use JetApplication\FulltextSearch_IndexDataProvider;
use JetApplication\EShopEntity_Definition;
use JetApplication\KindOfProduct;
use JetApplication\Product;
use JetApplication\Product_EShopData;
use JetApplication\ProductFilter;
use JetApplication\EShop_Managers;
use JetApplication\EShops;
use JetApplication\EShop;


#[DataModel_Definition(
	name: 'category',
	database_table_name: 'categories',
	id_controller_class: DataModel_IDController_AutoIncrement::class,
	id_controller_options: [
		'id_property_name' => 'id'
	]
)]
#[EShopEntity_Definition(
	entity_name_readable: 'Category',
	admin_manager_interface: Admin_Managers_Category::class,
	description_mode: true,
	separate_tab_form_shop_data: true,
	images: [
		'main' => 'Main image',
		'pictogram' => 'Pictogram image',
	]
)]
abstract class Core_Category extends EShopEntity_WithEShopData implements
	EShopEntity_HasImages_Interface,
	FulltextSearch_IndexDataProvider,
	EShopEntity_Admin_WithEShopData_Interface
{
	use EShopEntity_Admin_WithEShopData_Trait;
	use EShopEntity_WithEShopData_HasImages_Trait;
	
	public const SORT_NAME = 'name';
	public const SORT_PRIORITY = 'priority';
	
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
	 * @var Category_EShopData[]
	 */
	#[DataModel_Definition(
		type: DataModel::TYPE_DATA_MODEL,
		data_model_class: Category_EShopData::class
	)]
	protected array $eshop_data = [];
	
	protected ?array $product_ids = null;
	
	#[DataModel_Definition(
		type: DataModel::TYPE_BOOL,
		is_key: true
	)]
	protected bool $auto_append_products = false;
	
	protected static ?array $_names = null;
	
	public function setParentId( int $parent_id, bool $update_priority = true, bool $save=true ): void
	{
		$old_root_id = $this->root_id;
		
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
		
		foreach( EShops::getList() as $eshop ) {
			$sd = $this->getEshopData( $eshop );
			$sd->setParentId( $parent_id, false );
			$sd->setPriority( $this->priority, false );
		}
		
		
		if($save) {
			$this->save();
			
			static::actualizeTreeData();
			$new_root_id = static::dataFetchOne(['root_id'], ['id'=>$this->id]);
			
			static::actualizeProductAssoc( category_id: $old_root_id );
			if($new_root_id!=$old_root_id) {
				static::actualizeProductAssoc( category_id: $new_root_id );
			}
			
			Category::actualizeBranchProductAssoc( $old_root_id );
			if($new_root_id!=$old_root_id) {
				Category::actualizeBranchProductAssoc( $new_root_id );
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
	
	public function getRootId(): int
	{
		return $this->root_id;
	}
	
	public function getPriority() : int
	{
		return $this->priority;
	}

	public function setPriority( int $priority, bool $save=true ) : void
	{
		$this->priority = $priority;
		if($save) {
			static::updateData(data: ['priority'=>$this->priority], where: ['id'=>$this->id]);
		}
		
		foreach( EShops::getList() as $eshop) {
			$this->getEshopData( $eshop )->setPriority( $priority, $save );
		}
	}
	
	public function getPath() : array
	{
		if(!$this->path) {
			return [];
		}
		
		return explode(',', $this->path );
	}
	
	public static function getTree( string $sort_order=self::SORT_PRIORITY, ?bool $active_filter=null ) : Data_Tree
	{
		
		$sort = match($sort_order) {
			static::SORT_NAME => 'name',
			static::SORT_PRIORITY => 'priority',
		};
		
		$where = [];
		
		if($active_filter!==null) {
			$where['is_active'] = $active_filter;
		}
		
		$data = static::dataFetchAll(
			select:[
				'id' => 'id',
				'parent_id' => 'parent_id',
				'priority' => 'priority',
				'name' => 'internal_name',
				'is_active' => 'is_active',
			],
			where: $where,
			order_by: $sort
		);
		
		
		$tree = new Data_Tree();
		$tree->getRootNode()->setLabel('Root');
		
		$tree->setAdoptOrphans(true);
		
		$tree->setData( $data );
		
		
		return $tree;
	}
	
	
	public function getPathName( bool $as_array=false, string $path_str_glue=' / ' ) : array|string
	{
		$result = [];
		
		if(static::$_names===null) {
			static::$_names = static::dataFetchPairs( select: ['id', 'internal_name'] );
		}
		
		foreach( $this->getPath() as $id ) {
			$result[$id] = static::$_names[$id] ?? '';
		}
		
		if($as_array) {
			return $result;
		} else {
			return implode($path_str_glue, $result);
		}
	}
	

	public function getKindOfProductId(): int
	{
		return $this->kind_of_product_id;
	}
	
	public function setKindOfProductId( int $kind_of_product_id ): void
	{
		$this->kind_of_product_id = $kind_of_product_id;
		$this->save();
	}
	
	
	public function getEshopData( ?EShop $eshop=null ) : Category_EShopData
	{
		/** @noinspection PhpIncompatibleReturnTypeInspection */
		return $this->_getEshopData( $eshop );
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
			$active_branch_children = [];
			
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
				
				$active_branch_children[] = $p_id;
			}
			
			
			$parent_id = (int)$node->getParentId();
			$root_id = $path[0];
			$path = implode( ',', $path );
			$children = implode( ',', $children );
			$active_branch_children = implode( ',', $active_branch_children );
			
			static::updateData(
				[
					'root_id' => $root_id,
					'path' => $path,
					'children' => $children,
					'branch_children' => $active_branch_children,
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
						'branch_children !=' => $active_branch_children,
					]
				]
			);
			
			
			foreach( EShops::getList() as $eshop ) {
				$where = $eshop->getWhere();
				$where[] = 'AND';
				$where['entity_id'] = $id;
				$where[] = 'AND';
				$where[] = [
					'root_id !=' => $root_id,
					'OR',
					'parent_id !=' => $parent_id,
					'OR',
					'path !=' => $path,
					'OR',
					'children !=' => $children,
					'OR',
					'branch_children !=' => $active_branch_children,
				];
				
				Category_EShopData::updateData(
					[
						'root_id' => $root_id,
						'parent_id' => $parent_id,
						'path' => $path,
						'children' => $children,
						'branch_children' => $active_branch_children,
					],
					$where
				);
			}
			
		}
		
		
		
		
		
		foreach( EShops::getList() as $eshop ) {
			$active_categories = Category_EShopData::dataFetchAssoc(
				select: [
					'id' => 'entity_id',
					'parent_id' => 'parent_id',
					'name' => 'name',
				],
				where: Category_EShopData::getActiveQueryWhere( $eshop )
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
				
				$active_branch_children = [];
				
				foreach( $node->getAllChildrenIds() as $p_id ) {
					$p_id = (int)$p_id;
					if( !$p_id ) {
						continue;
					}
					
					$active_branch_children[] = $p_id;
				}
				$active_branch_children = implode(',', $active_branch_children);
				
				$where = $eshop->getWhere();
				$where[] = 'AND';
				$where['entity_id'] = $id;
				$where[] = 'AND';
				$where['active_branch_children !='] = $active_branch_children;
				
				Category_EShopData::updateData(
					[
						'active_branch_children' => $active_branch_children,
					],
					$where
				);
			}
			
			$non_active_categories = Category_EShopData::dataFetchCol(
				select: [
					'entity_id',
				],
				where: Category_EShopData::getNonActiveQueryWhere( $eshop )
			);
			
			if($non_active_categories) {
				$where = $eshop->getWhere();
				$where[] = 'AND';
				$where['entity_id'] = $non_active_categories;
				$where[] = 'AND';
				$where['active_branch_children !='] = '';
				
				Category_EShopData::updateData(
					[
						'active_branch_children' => '',
					],
					$where
				);
			}
			
		}
			
			
	}
	
	public function addProduct( int $product_id ) : bool
	{
		$product = Product::dataFetchRow(select: ['type', 'variant_master_product_id'], where: ['id'=>$product_id]);
		if(!$product) {
			return false;
		}
		if($product['type']==Product::PRODUCT_TYPE_VARIANT) {
			$product_id = $product['variant_master_product_id'];
		}
		
		$this->getProductIds();
		
		if(!in_array($product_id, $this->product_ids)) {
			$assoc = new Category_Product();
			$assoc->setCategoryId( $this->id );
			$assoc->setProductId( $product_id );
			$assoc->setPriority( count($this->product_ids) );
			$assoc->save();
			
			$this->product_ids = null;
			return true;
		}
		
		return false;
	}
	
	public function actualizeCategoryBranchProductAssoc() : void
	{
		static::actualizeBranchProductAssoc( $this->root_id );
	}
	
	public function removeProduct( int $product_id ) : bool
	{
		$this->getProductIds();
		
		if(!in_array($product_id, $this->product_ids )) {
			return false;
		}
		
		Category_Product::dataDelete([
			'category_id' => $this->id,
			'AND',
			'product_id' => $product_id
		]);
		
		$this->product_ids = null;

		$this->getProductIds();
		
		$i = 0;
		foreach( $this->product_ids as $p_id) {
			Category_Product::updateData(
				data: [
					'priority' => $i
				],
				where: [
					'category_id' => $this->id,
					'AND',
					'product_id' => $p_id
				]
			);
		}
		
		return true;
	}
	
	public function removeAllProducts() : bool
	{
		Category_Product::dataDelete([
			'category_id' => $this->id
		]);
		$this->product_ids = null;
		
		return true;
	}
	
	
	public function getProductIds() : array
	{
		if($this->product_ids===null) {
			$this->product_ids = Category_Product::dataFetchCol(
				select: ['product_id'],
				where: ['category_id'=>$this->id],
				order_by: ['priority'],
				raw_mode: true
			);
		}
		
		return $this->product_ids;
	}
	
	public static function actualizeProductAssoc( ?int $category_id=null, ?int $product_id=null ) : void
	{
		if($product_id) {
			$category_ids = Category_Product::dataFetchCol(
				select: ['category_id'],
				where: ['product_id'=>$product_id]
			);
			
			if($category_ids) {
				$root_ids = static::dataFetchCol(select: ['root_id'], where: ['id'=>$category_ids]);
				
				$root_ids = array_unique($root_ids);
				
				foreach($root_ids as $root_id) {
					static::actualizeBranchProductAssoc( $root_id );
				}
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
			order_by:['category_id', 'priority']
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
			foreach( EShops::getList() as $eshop ) {
				$where = Product_EShopData::getActiveQueryWhere( $eshop );
				$where[] = 'AND';
				$where[] = [
					'entity_id' => $all_product_ids,
				];
				$active_products[$eshop->getKey()] = Product_EShopData::dataFetchCol(
					select: ['entity_id'],
					where: $where
				);
			}
		} else {
			foreach( EShops::getList() as $eshop ) {
				$active_products[$eshop->getKey()] = [];
			}
			
		}
		
		foreach( EShops::getList() as $eshop ) {
			$where = $eshop->getWhere();
			$where[] = 'AND';
			$where['entity_id'] = $branch_category_ids;
			
			$_categories = Category_EShopData::dataFetchAll(
				select: [
					'entity_id',
					
					'entity_is_active',
					'is_active_for_eshop',
					
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
				$c['is_active']              = ($c['entity_is_active'] && $c['is_active_for_eshop']);
				$c['children']               = $c['children'] ? explode(',', $c['children']) : [];
				$c['branch_children']        = $c['branch_children'] ? explode(',', $c['branch_children']) : [];
				$c['active_branch_children'] = $c['active_branch_children'] ? explode(',', $c['active_branch_children']) : [];
				
				if(isset($category_products_map[$id])) {
					$c['new_product_ids'] = array_intersect( $category_products_map[$id], $active_products[$eshop->getKey()] );
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
					$where = $eshop->getWhere();
					$where[] = 'AND';
					$where['entity_id'] = $id;
					
					Category_EShopData::updateData(
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
	
	public static function getIdsByProduct( int $product_id ) : array
	{
		return Category_Product::dataFetchCol(['category_id'], ['product_id'=>$product_id]);
	}
	
	
	public static function actualizeAllAutoAppendCategories() : void
	{
		$category_ids = static::dataFetchCol(['id'], ['auto_append_products'=>true]);
		
		$update_roots = [];
		foreach($category_ids as $category_id) {
			
			$category = Category::load( $category_id );
			if($category->actualizeAutoAppend()) {
				$root_id = $category->getRootId();
				if(!in_array($root_id, $update_roots)) {
					$update_roots[] = $root_id;
				}
				
			}
			
		}
		
		foreach($update_roots as $root_id) {
			Category::actualizeBranchProductAssoc( $root_id );
		}
	}
	
	public function actualizeAutoAppend() : bool
	{
		
		$filter = $this->getAutoAppendProductsFilter();
		
		return $this->appendProductsByFilter(
			$filter, true
		);
		
	}
	
	public function getAutoAppendProducts(): bool
	{
		return $this->auto_append_products;
	}
	
	public function setAutoAppendProducts( bool $auto_append_products ): void
	{
		$this->auto_append_products = $auto_append_products;
	}
	
	public function getAutoAppendProductsFilter() : ProductFilter
	{
		$eshop = EShops::getDefault();
		
		$filter = new ProductFilter( $eshop );
		$filter->setContextEntity( Category::getEntityType() );
		$filter->setContextEntityId( $this->id );
		$filter->load();
		
		if(
			$this->getKindOfProductId() &&
			!$filter->getBasicFilter()->getKindOfProductId()
		) {
			$filter->getBasicFilter()->setKindOfProductId( $this->getKindOfProductId() );
		}
		
		
		return $filter;
	}
	
	public function appendProductsByFilter( ProductFilter $filter, bool $remove_non_relevant=true ) : bool
	{
		$new_product_ids = $filter->filter();
		
		$updated = false;
		$current_product_ids = $this->getProductIds();
		
		foreach( $new_product_ids as $product_id) {
			if($this->addProduct( $product_id )) {
				$updated = true;
			}
		}
		
		if($remove_non_relevant) {
			foreach($current_product_ids as $product_id) {
				if(!in_array($product_id, $new_product_ids)) {
					$this->removeProduct( $product_id );
					$updated = true;
				}
			}
		}
		
		return $updated;
		
	}
	
	public function sortProducts( array $product_ids ) : void
	{
		$p = 0;
		foreach($product_ids as $p_id) {
			$p_id = (int)$p_id;
			
			$where = [
				'category_id' => $this->id,
				'AND',
				'product_id' => $p_id
			];
			
			
			$assoc = Category_Product::dataFetchAll(['category_id', 'product_id','priority'], $where );
			if( $assoc ) {
				Category_Product::updateData(
					data:['priority'=>$p],
					where: $where
				);
				$p++;
			}
		}
		
	}
	
	protected function generateURLPathPart() : void
	{
		foreach( EShops::getList() as $eshop ) {
			$eshop_key = $eshop->getKey();
			
			$this->eshop_data[$eshop_key]->generateURLPathPart();
		}
	}
	
	
	
	public function afterAdd() : void
	{
		static::$_names = null;
		$this->generateURLPathPart();
		static::actualizeTreeData();
		
		parent::afterAdd();
	}
	
	public function afterUpdate() : void
	{
		static::$_names = null;
		parent::afterUpdate();
		$this->generateURLPathPart();
		
		parent::afterUpdate();
	}
	
	public function afterDelete() : void
	{
		static::$_names = null;
		parent::afterDelete();
		static::actualizeTreeData();
		
		parent::afterDelete();
	}
	
	
	
	public function getFulltextObjectType(): string
	{
		return '';
	}
	
	public function getFulltextObjectIsActive(): bool
	{
		return $this->isActive();
	}
	
	public function getInternalFulltextObjectTitle(): string
	{
		return $this->getAdminTitle();
	}
	
	public function getInternalFulltextTexts(): array
	{
		return [$this->getInternalName(), $this->getInternalCode()];
	}
	
	public function getAdminTitle(): string
	{
		$code = $this->internal_code ? : $this->id;
		
		return $this->getPathName().' ('.$code.')';
	}
	
	public function getShopFulltextTexts( EShop $eshop ) : array
	{
		$eshop_data = $this->getEshopData( $eshop );
		if(
			!$eshop_data->isActive() ||
			!$eshop_data->getBranchProductsCount()
		) {
			return [];
		}
		
		$texts = [];
		$texts[] = $eshop_data->getName();
		$texts[] = $eshop_data->getInternalCode();
		
		return $texts;
	}
	
	public function updateFulltextSearchIndex() : void
	{
		Admin_Managers::FulltextSearch()->updateIndex( $this );
		EShop_Managers::FulltextSearch()->updateIndex( $this );
	}
	
	public function removeFulltextSearchIndex() : void
	{
		Admin_Managers::FulltextSearch()->deleteIndex( $this );
		EShop_Managers::FulltextSearch()->deleteIndex( $this );
	}
	
	
	/**
	 * @param EShopEntity_Basic $entity_to_be_deleted
	 * @param EShopEntity_CanNotBeDeletedReason[] &$reasons
	 * @return bool
	 */
	public static function checkIfItCanBeDeleted( EShopEntity_Basic $entity_to_be_deleted, array &$reasons=[] ) : bool
	{
		/** @noinspection PhpSwitchStatementWitSingleBranchInspection */
		switch( get_class($entity_to_be_deleted) ) {
			case KindOfProduct::class:
				$ids = Category::dataFetchCol(
					select: [ 'id' ],
					where: ['kind_of_product_id' => $entity_to_be_deleted->getId() ]
				);
				if($ids) {
					$reasons[] = static::createCanNotBeDeletedReason(
						reason: 'Category - kind of product is used',
						ids:    $ids
					);
					
					return false;
				}
				break;
		}
		
		return true;
	}
	
	public function hasChildren() : bool
	{
		return (bool)$this->children;
	}
	
	public function getChildrenIds() : array
	{
		return $this->children ? explode(',', $this->children) : [];
	}
	
	public function getBranchChildrenIds() : array
	{
		return $this->branch_children ? explode(',', $this->children) : [];
	}
	
	public function move( int $target_category_id ) : void
	{
		if(
			$target_category_id==$this->id ||
			in_array($target_category_id, $this->getBranchChildrenIds()) ||
			!static::get( $target_category_id )
		) {
			return;
		}
		
		$this->setParentId( $target_category_id );
	}
	
	public function moveSubcategories( int $target_category_id ) : void
	{
		if(
			$target_category_id==$this->id ||
			in_array($target_category_id, $this->getBranchChildrenIds()) ||
			!static::get( $target_category_id )
		) {
			return;
		}
		
		$ids = $this->getChildrenIds();
		foreach($ids as $ch_id) {
			$ch = static::get( $ch_id );
			$ch->setParentId( $target_category_id );
		}
	}
	
}