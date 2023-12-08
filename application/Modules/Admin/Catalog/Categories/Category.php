<?php

namespace JetApplicationModule\Admin\Catalog\Categories;

use Jet\Data_Tree;
use Jet\DataModel_Definition;
use Jet\Form;
use Jet\Session;
use Jet\Tr;
use JetApplication\Admin_Entity_Interface;
use JetApplication\Admin_Entity_Trait;
use JetApplication\Admin_FulltextSearch_IndexDataProvider;
use JetApplication\Admin_Managers;
use JetApplication\Category as Application_Category;
use JetApplication\Shops;

#[DataModel_Definition(
	force_class_name: Application_Category::class
)]
class Category extends Application_Category implements Admin_Entity_Interface, Admin_FulltextSearch_IndexDataProvider {
	use Admin_Entity_Trait;
	
	public const SORT_NAME = 'name';
	public const SORT_PRIORITY = 'priority';
	
	/**
	 * @var Data_Tree[]
	 */
	protected static array $tree = [];
	
	
	/**
	 * @deprecated
	 */
	protected static ?Session $filter_session = null;
	
	protected static ?array $_names = null;
	
	
	public function getEditURL() : string
	{
		return Main::getEditUrl( $this->id );
	}
	
	/**
	 * @deprecated
	 */
	public static function getFilterSession() : Session
	{
		if(!static::$filter_session) {
			static::$filter_session = new Session('category_filter');
		}
		
		return static::$filter_session;
	}
	
	/**
	 * @deprecated
	 */
	public static function getFilter_selectedSort() : string
	{
		return static::getFilterSession()->getValue('sort', static::SORT_PRIORITY);
	}
	
	/**
	 * @deprecated
	 */
	public static function setFilter_selectedSort( string $val ) : void
	{
		if(isset(static::getSortScope()[$val])) {
			static::getFilterSession()->setValue('sort', $val);
		}
	}
	
	/**
	 * @deprecated
	 */
	public static function getFilter_onlyActive() : bool
	{
		return static::getFilterSession()->getValue('only_active', false);
	}
	
	public static function setFilter_onlyActive( string $val ) : void
	{
		static::getFilterSession()->setValue('only_active', (bool)$val);
	}
	
	/**
	 * @deprecated
	 */
	public static function getSortScope() : array
	{
		$_scope = [
			static::SORT_PRIORITY => 'priority',
			static::SORT_NAME => 'name',
		];
		
		$scope = [];
		
		foreach( $_scope as $option=>$label ) {
			$scope[$option] = Tr::_($label );
		}
		
		return $scope;
	}
	
	public static function resetTree() : void
	{
		static::$tree = [];
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
	
	
	public function getPath() : array
	{
		if(!$this->path) {
			return [];
		}
		
		return explode(',', $this->path );
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
	
	public function getAddForm() : Form
	{
		if(!$this->_add_form) {
			$this->_add_form = $this->createForm('add_form');
		}
		
		return $this->_add_form;
	}
	
	
	
	
	public function getEditForm() : Form
	{
		
		if(!$this->_edit_form) {
			$this->_edit_form = $this->createForm('edit_form');
		}
		
		if(!Main::getCurrentUserCanEdit()) {
			//TODO: add everywhere
			$this->_edit_form->setIsReadonly();
		}
		
		return $this->_edit_form;
	}
	
	
	public function defineImages(): void
	{
		$manager = Admin_Managers::Image();
		
		
		foreach(Shops::getList() as $shop) {
			$manager->defineImage(
				entity: 'category',
				object_id: $this->id,
				image_class:  'main',
				image_title:  Tr::_('Main image'),
				image_property_getter: function() use ($shop) : string {
					return $this->getShopData( $shop )->getImageMain();
				} ,
				image_property_setter: function( string $val )  use ($shop) : void {
					$this->getShopData( $shop )->setImageMain( $val );
					$this->getShopData( $shop )->save();
				},
				shop: $shop
			);
			
			
			$manager->defineImage(
				entity: 'category',
				object_id: $this->id,
				image_class:  'pictogram',
				image_title:  Tr::_('Pictogram image'),
				image_property_getter: function() use ($shop) : string {
					return $this->getShopData( $shop )->getImagePictogram();
				} ,
				image_property_setter: function( string $val )  use ($shop) : void {
					$this->getShopData( $shop )->setImagePictogram( $val );
					$this->getShopData( $shop )->save();
				},
				shop: $shop
			);
			
			
			
		}
	}
	
	public function setParentId( int $parent_id, bool $update_priority = true, bool $save=true ): void
	{
		$old_root_id = $this->root_id;
		parent::setParentId( $parent_id, $update_priority, $save );
		
		if($save) {
			$new_root_id = Category::dataFetchOne(['root_id'], ['id'=>$this->id]);
			
			Category::actualizeBranchProductAssoc( $old_root_id );
			if($new_root_id!=$old_root_id) {
				Category::actualizeBranchProductAssoc( $new_root_id );
			}
		}
	}
	
	
	public function afterAdd() : void
	{
		foreach( Shops::getList() as $shop ) {
			$shop_key = $shop->getKey();
			
			$this->shop_data[$shop_key]->generateURLPathPart();
			$this->shop_data[$shop_key]->save();
		}
		
		static::resetTree();
		static::actualizeTreeData();
		
		Admin_Managers::FulltextSearch()->addIndex( $this );
	}
	
	public function afterUpdate() : void
	{
		Admin_Managers::FulltextSearch()->updateIndex( $this );
	}
	
	public function afterDelete() : void
	{
		static::resetTree();
		static::actualizeTreeData();
		Admin_Managers::FulltextSearch()->deleteIndex( $this );
	}
	
	public function getAdminFulltextObjectClass(): string
	{
		return $this->getEntityType();
	}
	
	public function getAdminFulltextObjectId(): string
	{
		return $this->id;
	}
	
	public function getAdminFulltextObjectType(): string
	{
		return '';
	}
	
	public function getAdminFulltextObjectIsActive(): bool
	{
		return $this->isActive();
	}
	
	public function getAdminFulltextObjectTitle(): string
	{
		return $this->getPathName();
	}
	
	public function getAdminFulltextTexts(): array
	{
		return [$this->getPathName()];
	}
	
}