<?php
namespace JetShop;

use Jet\Application_Modules;
use Jet\DataModel;
use Jet\Data_Tree;
use Jet\DataModel_Definition;
use Jet\Form;
use Jet\Form_Definition;
use Jet\DataModel_IDController_AutoIncrement;
use Jet\MVC;
use Jet\Session;
use Jet\Tr;
use Jet\MVC_View;
use Jet\UI_icon;
use Jet\Application_Module;

#[DataModel_Definition(
	name: 'category',
	database_table_name: 'categories',
	id_controller_class: DataModel_IDController_AutoIncrement::class,
	id_controller_options: ['id_property_name'=>'id']
)]
abstract class Core_Category extends DataModel {
	protected static string $manage_module_name = 'Admin.Catalog.Categories';

	const SORT_NAME = 'name';
	const SORT_PRIORITY = 'priority';


	#[DataModel_Definition(
		type: DataModel::TYPE_ID_AUTOINCREMENT,
		is_id: true
	)]
	protected int $id = 0;

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
	protected string $all_children = '';

	#[DataModel_Definition(
		type: DataModel::TYPE_INT,
	)]
	protected int $priority = 0;
	
	
	
	#[DataModel_Definition(
		type: DataModel::TYPE_INT,
		is_key: true
	)]
	protected int $kind_of_product_id = 0;

	#[DataModel_Definition(
		type: DataModel::TYPE_BOOL
	)]
	protected bool $auto_append_products = false;
	
	#[DataModel_Definition(
		type: DataModel::TYPE_CUSTOM_DATA,
	)]
	protected array $auto_append_products_filter = [];

	/**
	 * @var Category_ShopData[]
	 */
	#[DataModel_Definition(
		type: DataModel::TYPE_DATA_MODEL,
		data_model_class: Category_ShopData::class
	)]
	#[Form_Definition(is_sub_forms: true)]
	protected array $shop_data = [];

	
	/**
	 * @var Data_Tree[]
	 */
	protected static array $tree = [];

	protected static ?Session $filter_session = null;

	protected ?Form $_add_form = null;

	protected ?Form $_edit_form = null;

	protected Form|null|bool $_auto_append_product_filter_edit_form = null;

	protected ?ProductListing $_product_listing = null;

	/**
	 * @var Category[]
	 */
	protected ?array $_children = null;

	/**
	 * @var Category[]
	 */
	protected static array $loaded_items = [];

	protected static array $sync_categories = [];

	public static function getManageModuleName(): string
	{
		return self::$manage_module_name;
	}

	public static function getManageModule() : Category_ManageModuleInterface|Application_Module
	{
		return Application_Modules::moduleInstance( Category::getManageModuleName() );
	}

	
	public static function getFilterSession() : Session
	{
		if(!Category::$filter_session) {
			Category::$filter_session = new Session('category_filter');
		}

		return Category::$filter_session;
	}

	public static function getFilter_selectedSort() : string
	{
		return Category::getFilterSession()->getValue('sort', Category::SORT_PRIORITY);
	}

	public static function setFilter_selectedSort( string $val ) : void
	{
		if(isset(Category::getSortScope()[$val])) {
			Category::getFilterSession()->setValue('sort', $val);
		}
	}

	public static function getFilter_onlyActive() : bool
	{
		return Category::getFilterSession()->getValue('only_active', false);
	}

	public static function setFilter_onlyActive( string $val ) : void
	{
		Category::getFilterSession()->setValue('only_active', (bool)$val);
	}

	public static function getSortScope() : array
	{
		$_scope = [
			Category::SORT_PRIORITY => 'priority',
			Category::SORT_NAME => 'name',
		];

		$scope = [];

		foreach( $_scope as $option=>$label ) {
			$scope[$option] = Tr::_($label, [], Category::getManageModuleName() );
		}

		return $scope;
	}

	public function __construct()
	{
		parent::__construct();

		$this->afterLoad();
	}

	public function afterLoad() : void
	{
		Category_ShopData::checkShopData( $this, $this->shop_data );
	}

	public static function get( int $id ) : Category|null
	{
		if(isset(static::$loaded_items[$id])) {
			return static::$loaded_items[$id];
		}

		$cache_key = 'category:'.$id;

		static::$loaded_items[$id] = Cache::load( $cache_key );

		if(!static::$loaded_items[$id]) {
			static::$loaded_items[$id] = Category::load( $id );

			Cache::save( $cache_key, static::$loaded_items[$id]);
		}

		return static::$loaded_items[$id];
	}

	public static function resetTree() : void
	{
		Category::$tree = [];
	}

	public static function getTree( ?Shops_Shop $shop=null , string|null $sort_order=null, bool|null $only_active=false ) : Data_Tree
	{

		if(!$shop) {
			$shop = Shops::getCurrent();
		}

		if($sort_order===null) {
			$sort_order = Category::getFilter_selectedSort();
		}

		$key = $shop->getKey().':'.($only_active?1:0).':'.$sort_order;



		if(!isset(Category::$tree[$key])) {

			$sort = match($sort_order) {
				Category::SORT_NAME => 'name',
				Category::SORT_PRIORITY => 'priority',
			};

			$where = $shop->getWhere('categories_shop_data.');

			if($only_active) {
				$where[] = 'AND';
				$where['categories_shop_data.is_active'] = true;
			}

			$data = Category::dataFetchAll(
				select:[
					'id' => 'id',
					'parent_id' => 'parent_id',
					'priority' => 'priority',
					'name' => 'categories_shop_data.name',
					'is_active' => 'categories_shop_data.is_active',
				],
				where: $where,
				order_by: $sort
			);


			$tree = new Data_Tree();
			$tree->getRootNode()->setId(0);
			$tree->getRootNode()->setLabel('Root');

			$tree->setAdoptOrphans(true);

			$tree->setData( $data );

			Category::$tree[$key] = $tree;
		}

		return Category::$tree[$key];
	}

	public static function actualizeTreeData() : void
	{
		foreach( Shops::getList() as $shop ) {
			$tree = Category::getTree( $shop, Category::SORT_PRIORITY );

			foreach( $tree as $node ) {

				$id = (int)$node->getId();

				if( !$id ) {
					continue;
				}

				$path = [];
				$children = [];
				$all_children = [];

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

					$all_children[] = $p_id;
				}


				$path = implode( ',', $path );
				$children = implode( ',', $children );
				$all_children = implode( ',', $all_children );

				Category::updateData(
					[
						'path' => $path,
						'children' => $children,
						'all_children' => $all_children,
					],
					[
						'id' => $id
					]
				);

			}

			break;
		}


		foreach( Shops::getList() as $shop ) {
			$tree = Category::getTree( $shop, Category::SORT_PRIORITY, true );

			foreach($tree as $node ) {

				$id = (int)$node->getId();

				if(!$id) {
					continue;
				}

				$path = [];
				$children = [];
				$all_children = [];

				foreach( $node->getPathFromRoot() as $p_node ) {
					$p_id = (int)$p_node->getId();
					if(!$p_id) {
						continue;
					}

					$path[] = $p_id;
				}

				foreach( $node->getChildren() as $p_node ) {
					$p_id = (int)$p_node->getId();
					if(!$p_id) {
						continue;
					}

					$children[] = $p_id;
				}


				foreach( $node->getAllChildrenIds() as $p_id ) {
					$p_id = (int)$p_id;
					if(!$p_id) {
						continue;
					}

					$all_children[] = $p_id;
				}


				$path = implode(',', $path);
				$children = implode(',', $children);
				$all_children = implode(',', $all_children);

				Category_ShopData::updateData(
					[
						'path' => $path,
						'children' => $children,
						'all_children' => $all_children,
					],
					[
						'category_id' => $id,
						'AND',
						$shop->getWhere()
					]
				);

			}
		}

		Category_Menu::generateAll();
	}

	public function getPath() : array
	{
		if(!$this->path) {
			return [];
		}

		return explode(',', $this->path );
	}

	protected static ?array $_names = null;

	public function getPathName( bool $as_array=false, ?Shops_Shop $shop=null , string $path_str_glue=' / ' ) : array|string
	{

		if(!$shop) {
			$shop = Shops::getCurrent();
		}

		$result = [];

		if(static::$_names===null) {
			static::$_names = [];

			$names = Category_ShopData::dataFetchAll( select: ['category_id', 'name', 'shop_code', 'locale'] );

			foreach($names as $n) {
				$category_id = (int)$n['category_id'];
				$name = $n['name'];
				$shop_key = $n['shop_code'].'_'.$n['locale'];

				if(!isset(static::$_names[$shop_key])) {
					static::$_names[$shop_key] = [];
				}

				static::$_names[$shop_key][$category_id] = $name;
			}
		}


		$key = $shop->getKey();

		foreach( $this->getPath() as $id ) {
			$result[$id] = static::$_names[$key][$id] ?? '';
		}

		if($as_array) {
			return $result;
		} else {
			return implode($path_str_glue, $result);
		}
	}
	
	public function getId() : int
	{
		return $this->id;
	}

	public function setId( int $id ) : void
	{
		$this->id = $id;
	}

	public function isVisible( ?Shops_Shop $shop=null  ) : bool
	{
		$shop_data = $this->getShopData( $shop );

		if(
			!$shop_data->isActive() ||
			!$shop_data->getNestedVisibleProductsCount()
		) {
			return false;
		}

		return true;
	}

	public function isActive( ?Shops_Shop $shop=null  ) : bool
	{
		return $this->getShopData($shop)->isActive();
	}


	/**
	 *
	 * @return Category[]
	 */
	public function _getChildren() : array
	{
		if($this->_children!==null) {
			return $this->_children;
		}

		$this->_children = [];

		$ch_ids = [];

		if($this->children) {
			$ch_ids = explode(',', $this->children);
		}


		foreach($ch_ids as $id) {
			$ch_c = Category::get($id);
			if( !$ch_c ) {
				continue;
			}

			$this->_children[$ch_c->getId()] = $ch_c;
		}


		return $this->_children;
	}

	public function getNestedVisibleProductsCount( ?Shops_Shop $shop=null  ) : int
	{
		return $this->getShopData($shop)->getNestedVisibleProductsCount();
	}

	public function getVisibleProductsCount( ?Shops_Shop $shop=null  ) : int
	{
		return $this->getShopData($shop)->getVisibleProductsCount();
	}

	public function getName( ?Shops_Shop $shop=null  ) : string
	{
		return $this->getShopData($shop)->getName();
	}

	public function getSecondName( ?Shops_Shop $shop=null  ) : string
	{
		return $this->getShopData($shop)->getSecondName();
	}

	public function getDescription( ?Shops_Shop $shop=null  ) : string
	{
		return $this->getShopData($shop)->getDescription();
	}

	public function getSeoH1( ?Shops_Shop $shop=null  ) : string
	{
		return $this->getShopData($shop)->getSeoH1();
	}

	public function getSeoTitle( ?Shops_Shop $shop=null  ) : string
	{
		return $this->getShopData($shop)->getSeoTitle();
	}

	public function getSeoDescription( ?Shops_Shop $shop=null  ) : string
	{
		return $this->getShopData($shop)->getSeoDescription();
	}

	public function getSeoKeywords( ?Shops_Shop $shop=null  ) : string
	{
		return $this->getShopData($shop)->getSeoKeywords();
	}

	public function getURL( ?Shops_Shop $shop=null  ) : string
	{
		return $this->getShopData($shop)->getURL();
	}

	public function getURLPathPart( ?Shops_Shop $shop=null  ): string
	{
		return $this->getShopData($shop)->getURLPathPart();
	}


	public function getImageMainUrl( ?Shops_Shop $shop=null  ) : string
	{
		return $this->getShopData($shop)->getImageMainUrl();
	}

	public function getImageMainThumbnailUrl( int $max_w, int $max_h, ?Shops_Shop $shop=null  ) : string
	{
		return $this->getShopData($shop)->getImageMainThumbnailUrl( $max_w, $max_h );
	}

	public function getImagePictogramUrl( ?Shops_Shop $shop=null  ) : string
	{
		return $this->getShopData($shop)->getImagePictogramUrl();
	}

	public function getImagePictogramThumbnailUrl( int $max_w, int $max_h, ?Shops_Shop $shop=null  ) : string
	{
		return $this->getShopData($shop)->getImagePictogramThumbnailUrl( $max_w, $max_h );
	}

	public function getProductListing( ?Shops_Shop $shop=null , bool $use_singleton=true ) : ProductListing|null
	{
		if($use_singleton && $this->_product_listing ) {
			return $this->_product_listing;
		}

		/**
		 * @var Category $category
		 */
		$category = $this;

		$listing = new ProductListing( $category, $shop );
		$listing->prepareProductListing( $category->getShopData($shop)->getProductIds() );


		if($use_singleton) {
			$this->_product_listing = $listing;
		}

		return $listing;

	}

	public function getEditURL() : string
	{
		return Category::getCategoryEditURL( $this->id );
	}
	
	public static function getCategoryEditURL( int $id ) : string
	{
		return Category::getManageModule()->getCategoryEditUrl( $id );
	}
	
	public function getAutoAppendProductsFilter() : array
	{
		return $this->auto_append_products_filter;
	}

	public function setAutoAppendProductsFilter( array $auto_append_products_filter ) : void
	{
		$this->auto_append_products_filter = $auto_append_products_filter;
	}


	public function setParentId( int $parent_id, bool $update_priority=true ) : void
	{
		$this->parent_id = $parent_id;

		if($update_priority) {
			$tree = Category::getTree();

			$this->priority = 1;
			foreach( $tree->getNode( $parent_id )->getChildren() as $ch ) {
				$ch_p = $ch->getData()['priority'];

				if($ch_p>=$this->priority) {
					$this->priority = $ch_p+1;
				}
			}
		}

	}

	public function getParentId() : int
	{
		return $this->parent_id;
	}

	public function getPriority() : int
	{
		return $this->priority;
	}

	public function setPriority( int $priority ) : void
	{
		$this->priority = $priority;
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
		if($this->kind_of_product_id == $kind_of_product_id) {
			return;
		}
		$this->kind_of_product_id = $kind_of_product_id;
		
		$this->save();
		//TODO:
		
	}
	
	public function getKindOfProduct() : ?KindOfProduct
	{
		if($this->kind_of_product_id) {
			return KindOfProduct::get($this->kind_of_product_id);
		}
		
		return null;
	}
	
	public function getAutoAppendProducts(): bool
	{
		return $this->auto_append_products;
	}
	
	public function setAutoAppendProducts( bool $auto_append_products ): void
	{
		$this->auto_append_products = $auto_append_products;
	}
	
	

	public function getShopData( ?Shops_Shop $shop=null ) : Category_ShopData
	{
		return $this->shop_data[$shop ? $shop->getKey() : Shops::getCurrent()->getKey()];
	}
	

	public function getAddForm() : Form
	{
		if(!$this->_add_form) {
			$this->_add_form = $this->createForm('add_form');
			$this->_add_form->setCustomTranslatorDictionary( Category::getManageModuleName() );
		}

		return $this->_add_form;
	}



	public function catchAddForm() : bool
	{
		return $this->getAddForm()->catch();
	}



	public function getEditForm() : Form
	{

		if(!$this->_edit_form) {
			$this->_edit_form = $this->createForm('edit_form');
			$this->_edit_form->setCustomTranslatorDictionary( Category::getManageModuleName() );
		}

		return $this->_edit_form;
	}
	
	public function catchEditForm() : bool
	{
		return $this->getEditForm()->catch();
	}


	public function getAutoAppendProductFilterEditForm() : Form|bool
	{
		if($this->_auto_append_product_filter_edit_form===null) {

			$listing = new ProductListing( $this );

			$this->_auto_append_product_filter_edit_form = $listing->getAutoAppendProductFilterEditForm( $this->auto_append_products_filter );

		}

		return $this->_auto_append_product_filter_edit_form;
	}

	public function catchAutoAppendProductFilterEditForm() : bool
	{
		$listing = new ProductListing( $this );

		return $listing->catchAutoAppendProductFilterEditForm( $this->auto_append_products_filter );
	}
	
	public function handleAutoAppendProduct() : void
	{
	
	}
	

	public function afterAdd() : void
	{
		foreach( Shops::getList() as $shop ) {
			$shop_key = $shop->getKey();

			$this->shop_data[$shop_key]->generateURLPathPart();
			$this->shop_data[$shop_key]->save();
		}

		Category::resetTree();
		Category::actualizeTreeData();
		
		Fulltext_Index_Internal_Category::addIndex( $this );
	}

	public function afterUpdate() : void
	{
		Fulltext_Index_Internal_Category::updateIndex( $this );

		Category::actualizeTreeData();
		Category::syncCategories();

		$this->actualizeProductsList();
	}

	public function afterDelete() : void
	{
		Fulltext_Index_Internal_Category::deleteIndex( $this );

		$this->actualizeProductsList();
	}

	public function actualizeProductsList() : void
	{
		$products = Product::getListByCategory( $this->id );
		
		$product_ids = [];
		foreach( Shops::getList() as $shop ) {
			$product_ids[$shop->getKey()] = [];
		}
		
		foreach($products as $product) {
			if(!$product->isActive()) {
				continue;
			}
			
			foreach( Shops::getList() as $shop ) {
				if(
					!$product->getShopData($shop)->isActive() ||
					$product->getShopData($shop)->getFinalPrice()<=0
				) {
					continue;
				}
				
				$product_ids[$shop->getKey()][] = $product->getId();
			}
		}
		
		foreach( Shops::getList() as $shop ) {
			$this->getShopData($shop)->setProductIds( $product_ids[$shop->getKey()], true );
		}
	}



	public static function renderSelectCategoryWidget( string $on_select,
	                                                   int $selected_category_id=0,
	                                                   int $exclude_branch_id=0,
	                                                   bool $only_active=false,
	                                                   string $name='select_category' ) : string
	{
		$view = new MVC_View( MVC::getBase()->getViewsPath() );

		$view->setVar('selected_category_id', $selected_category_id);
		$view->setVar('exclude_branch_id', $exclude_branch_id);
		$view->setVar('on_select', $on_select);
		$view->setVar('name', $name);
		$view->setVar('only_active', $only_active);

		return $view->render('select-category-widget');
	}



	public static function renderIcon( string $type, bool $no_icon_for_regular=false ) : string|UI_icon
	{
		/*
		switch($type) {
			case Category::CATEGORY_TYPE_REGULAR:
				if($no_icon_for_regular) {
					return '';
				}
				$cat_icon = 'folder';
				$cat_icon_title = Tr::_('Regular category', [], Category::getManageModuleName());
				break;
			case Category::CATEGORY_TYPE_TOP:
				$cat_icon = 'caret-square-up';
				$cat_icon_title = Tr::_('TOP category', [], Category::getManageModuleName());
				break;
			case Category::CATEGORY_TYPE_VIRTUAL:
				$cat_icon = 'magic';
				$cat_icon_title = Tr::_('Virtual category', [], Category::getManageModuleName());
				break;
			case Category::CATEGORY_TYPE_LINK:
				$cat_icon = 'external-link-alt';
				$cat_icon_title = Tr::_('Link', [], Category::getManageModuleName());
				break;
			default:
				return '';
		}


		return UI::icon( $cat_icon )->setTitle( $cat_icon_title );
		*/
		//TODO:
		return '';
	}

	public static function addSyncCategory( int $id ) : void
	{
		if(!in_array($id, static::$sync_categories)) {
			static::$sync_categories[] = $id;
		}
	}

	public static function syncCategories() : void
	{
		/**
		 * @var Category[] $sync_categories
		 */
		$sync_categories = [];

		foreach( static::$sync_categories as $id ) {
			$category = Category::get( $id );
			$category->actualizeProductsList();
		}
		

		Category::actualizeTreeData();

		foreach( Shops::getList() as $shop ) {
			$data = Category::dataFetchAll(
				select: [
					'id' => 'id',
					'product_ids' => 'categories_shop_data.product_ids',
					'all_children' => 'categories_shop_data.all_children'
				],
				where: [
					$shop->getWhere('categories_shop_data.'),
					'AND',
					'categories_shop_data.is_active' => true,
				]
			);


			$ids_map = [];
			foreach($data as $i=>$d) {
				$id = (int)$d['id'];

				if($d['product_ids']) {
					$ids_map[$id] = explode(',', $d['product_ids']);
				} else {
					$ids_map[$id] = [];
				}

				if($d['all_children']) {
					$data[$i]['all_children'] = explode(',', $d['all_children']);
				} else {
					$data[$i]['all_children'] = [];
				}
			}

			foreach($data as $d) {
				$id = (int)$d['id'];

				$p_ids = $ids_map[$id];

				$visible_products_count = count( $p_ids );

				foreach( $d['all_children'] as $ch_id ) {
					$ch_ids = $ids_map[$ch_id];

					$p_ids = array_merge( $p_ids, $ch_ids );
				}

				$p_ids = array_unique($p_ids);

				$nested_visible_products_count = count( $p_ids );

				if($id==70) {
					var_dump( $visible_products_count, $nested_visible_products_count);
				}

				Category_ShopData::updateData(
					[
						'visible_products_count' => $visible_products_count,
						'nested_visible_products_count' => $nested_visible_products_count
					],
					[
						'category_id' => $id,
						'AND',
						$shop->getWhere()
					]
				);


			}

		}

	}
	
	public static function getByKindOfProduct( KindOfProduct $kind ) : array
	{
		$ids = Category::dataFetchCol(['id'], ['kind_of_product_id'=>$kind->getId()]);

		if(!$ids) {
			return [];
		}
		
		return Category::fetch(['category'=>['id'=>$ids]]);
	}
	
}