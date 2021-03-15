<?php
namespace JetShop;

use Jet\Application_Modules;
use Jet\DataModel;
use Jet\Data_Tree;
use Jet\DataModel_Definition;
use Jet\Form;
use Jet\Form_Field_Hidden;
use Jet\Form_Field_Select;
use Jet\DataModel_IDController_AutoIncrement;
use Jet\Mvc;
use Jet\Session;
use Jet\Tr;
use Jet\Mvc_View;
use Jet\UI;
use Jet\UI_icon;
use Jet\Application_Module;

#[DataModel_Definition(
	name: 'categories',
	database_table_name: 'categories',
	id_controller_class: DataModel_IDController_AutoIncrement::class,
	id_controller_options: ['id_property_name'=>'id']
)]
abstract class Core_Category extends DataModel {
	protected static string $manage_module_name = 'Admin.Catalog.Categories';

	const SORT_NAME = 'name';
	const SORT_PRIORITY = 'priority';

	const PARAMETER_STRATEGY_TAKES_OVER_FROM_PARENT = 'takes_over_from_parent';
	const PARAMETER_STRATEGY_TAKES_OVER_FROM_OTHER_CATEGORY = 'takes_over_from_other_category';
	const PARAMETER_STRATEGY_INHERITED_FROM_PARENT = 'inherited_from_parent';
	const PARAMETER_STRATEGY_INHERITED_FROM_OTHER_CATEGORY = 'inherited_from_other_category';
	const PARAMETER_STRATEGY_DEFINES = 'defines';


	const CATEGORY_TYPE_REGULAR = 'regular';
	const CATEGORY_TYPE_TOP     = 'top';
	const CATEGORY_TYPE_VIRTUAL = 'virtual';
	const CATEGORY_TYPE_LINK    = 'link';

	protected static array $category_type_options = [
		Category::CATEGORY_TYPE_REGULAR => 'Regular category',
		Category::CATEGORY_TYPE_TOP     => 'Top category',
		Category::CATEGORY_TYPE_VIRTUAL => 'Virtual category',
		Category::CATEGORY_TYPE_LINK    => 'Link',

	];

	protected static array $parameter_strategy_options = [
		Category::PARAMETER_STRATEGY_DEFINES                          => 'Defines its own set of parameters',
		Category::PARAMETER_STRATEGY_TAKES_OVER_FROM_PARENT           => 'Takes a parameters from the parent completely',
		Category::PARAMETER_STRATEGY_TAKES_OVER_FROM_OTHER_CATEGORY   => 'Takes a parameters from another category completely',
		Category::PARAMETER_STRATEGY_INHERITED_FROM_PARENT            => 'Inherits parameters from the parent (additional / custom parameters can be defined)',
		Category::PARAMETER_STRATEGY_INHERITED_FROM_OTHER_CATEGORY    => 'Inherits parameters from another category (additional / custom parameters can be defined)',
	];


	#[DataModel_Definition(
		type: DataModel::TYPE_ID_AUTOINCREMENT,
		is_id: true
	)]
	protected int $id = 0;

	#[DataModel_Definition(
		type: DataModel::TYPE_INT,
		is_key: true,
		form_field_type: false
	)]
	protected int $parent_id = 0;

	#[DataModel_Definition(
		type: DataModel::TYPE_STRING,
		max_len: 65536,
		form_field_type: false
	)]
	protected string $path = '';

	#[DataModel_Definition(
		type: DataModel::TYPE_STRING,
		max_len: 65536,
		form_field_type: false
	)]
	protected string $children = '';

	#[DataModel_Definition(
		type: DataModel::TYPE_STRING,
		max_len: 65536,
		form_field_type: false
	)]
	protected string $all_children = '';

	#[DataModel_Definition(
		type: DataModel::TYPE_INT,
		form_field_type: false
	)]
	protected int $priority = 0;

	#[DataModel_Definition(
		type: DataModel::TYPE_STRING,
		max_len: 100,
		is_key: true,
		form_field_type: false
	)]
	protected string $type = Category::CATEGORY_TYPE_REGULAR;

	#[DataModel_Definition(
		type: DataModel::TYPE_STRING,
		max_len: 100,
		form_field_type: Form::TYPE_SELECT,
		form_field_label: 'Parameter strategy:',
		form_field_get_select_options_callback: [ Category::class,'getParameterStrategyOptions']
	)]
	protected string $parameter_strategy = Category::PARAMETER_STRATEGY_DEFINES;

	#[DataModel_Definition(
		type: DataModel::TYPE_INT,
		is_key: true
	)]
	protected int $parameter_inherited_category_id = 0;

	#[DataModel_Definition(
		type: DataModel::TYPE_INT,
		is_key: true,
		form_field_type: Form::TYPE_HIDDEN
	)]
	protected int $target_category_id = 0;

	protected Category|null $_link_target_category = null;

	#[DataModel_Definition(
		type: DataModel::TYPE_CUSTOM_DATA,
		form_field_type: false
	)]
	protected array $target_filter = [];

	#[DataModel_Definition(
		type: DataModel::TYPE_DATA_MODEL,
		data_model_class: Category_ShopData::class
	)]
	protected $shop_data;

	/**
	 * @var Parametrization_Group[]
	 */
	#[DataModel_Definition(
		type: DataModel::TYPE_DATA_MODEL,
		data_model_class: Parametrization_Group::class,
		form_field_type: false
	)]
	protected $parametrization_groups;

	/**
	 *
	 * @var Parametrization_Group[]
	 */
	protected array|null $_parametrization_groups = null;

	/**
	 * @var Parametrization_Property[]
	 */
	#[DataModel_Definition(
		type: DataModel::TYPE_DATA_MODEL,
		data_model_class: Parametrization_Property::class,
		form_field_type: false
	)]
	protected $parametrization_properties;

	/**
	 * @var Parametrization_Property[]
	 */
	protected array|null $_parametrization_properties = null;


	/**
	 * @var Data_Tree[]
	 */
	protected static array $tree = [];

	protected static ?Session $filter_session = null;

	protected ?Form $_add_form = null;

	protected ?Form $_edit_form = null;

	protected Form|null|bool $_target_filter_edit_form = null;

	protected ?Form $_parametrization_strategy_form = null;

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

	public static function getCategoryTypeOptions() : array
	{
		$options = [];

		foreach( self::$category_type_options as $option=>$label ) {
			$options[$option] = Tr::_($label, [], Category::getManageModuleName() );
		}

		return $options;
	}

	public static function getParameterStrategyOptions() : array
	{
		$types = [];

		foreach( self::$parameter_strategy_options as $option=>$label ) {
			$types[$option] = Tr::_($label, [], Category::getManageModuleName() );
		}

		return $types;
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
		foreach( Shops::getList() as $shop ) {
			$shop_code = $shop->getCode();

			if(!isset($this->shop_data[$shop_code])) {

				$sh = new Category_ShopData();
				$sh->setShopCode($shop_code);

				$this->shop_data[$shop_code] = $sh;
			}

			/** @noinspection PhpParamsInspection */
			$this->shop_data[$shop_code]->setParents( $this );
		}
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

	public static function getTree( string|null $shop_code=null, string|null $sort_order=null, bool|null $only_active=false ) : Data_Tree
	{

		if(!$shop_code) {
			$shop_code = Shops::getCurrentCode();
		}

		if($sort_order===null) {
			$sort_order = Category::getFilter_selectedSort();
		}

		$key = $shop_code.':'.($only_active?1:0).':'.$sort_order;



		if(!isset(Category::$tree[$key])) {

			$sort = match($sort_order) {
				Category::SORT_NAME => 'name',
				Category::SORT_PRIORITY => 'priority',
			};

			$where = [
				'categories_shop_data.shop_code'=>$shop_code
			];

			if($only_active) {
				$where[] = 'AND';
				$where['categories_shop_data.is_active'] = true;
			}

			$data = Category::fetchData(
				[
					'id' => 'id',
					'parent_id' => 'parent_id',
					'priority' => 'priority',
					'name' => 'categories_shop_data.name',
					'is_active' => 'categories_shop_data.is_active',
					'type' => 'type',
					'target_category_id' => 'target_category_id',
				],
				$where,
				$sort
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
			$shop_code = $shop->getCode();

			$tree = Category::getTree( $shop_code, Category::SORT_PRIORITY );

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
			$shop_code = $shop->getCode();

			$tree = Category::getTree( $shop_code, Category::SORT_PRIORITY, true );

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
						'shop_code' => $shop_code
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

	public function _getPathName( bool $as_array=false, string|null $shop_code=null, string $path_str_glue=' / ' ) : array|string
	{

		$result = [];

		if(static::$_names===null) {
			static::$_names = [];

			$names = Category_ShopData::fetchData(['category_id', 'name', 'shop_code'], []);

			foreach($names as $n) {
				$category_id = (int)$n['category_id'];
				$name = $n['name'];
				$s_code = $n['shop_code'];

				if(!isset(static::$_names[$s_code])) {
					static::$_names[$s_code] = [];
				}

				static::$_names[$s_code][$category_id] = $name;
			}
		}

		if(!$shop_code) {
			$shop_code = Shops::getCurrentCode();
		}

		foreach( $this->getPath() as $id ) {
			$result[$id] = static::$_names[$shop_code][$id] ?? '';
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

	public function isVisible( string|null $shop_code=null ) : bool
	{
		$shop_data = $this->getShopData( $shop_code );

		if(
			!$shop_data->isActive() ||
			!$shop_data->getNestedVisibleProductsCount()
		) {
			return false;
		}

		return true;
	}

	public function isActive( string|null $shop_code=null ) : bool
	{
		return $this->getShopData($shop_code)->isActive();
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

	public function getNestedVisibleProductsCount( string|null $shop_code=null ) : int
	{
		return $this->getShopData($shop_code)->getNestedVisibleProductsCount();
	}

	public function getVisibleProductsCount( string|null $shop_code=null ) : int
	{
		return $this->getShopData($shop_code)->getVisibleProductsCount();
	}

	public function getName( string|null $shop_code=null ) : string
	{
		return $this->getShopData($shop_code)->getName();
	}

	public function getSecondName( string|null $shop_code=null ) : string
	{
		return $this->getShopData($shop_code)->getSecondName();
	}

	public function getDescription( string|null $shop_code=null ) : string
	{
		return $this->getShopData($shop_code)->getDescription();
	}

	public function getSeoH1( string|null $shop_code=null ) : string
	{
		return $this->getShopData($shop_code)->getSeoH1();
	}

	public function getSeoTitle( string|null $shop_code=null ) : string
	{
		return $this->getShopData($shop_code)->getSeoTitle();
	}

	public function getSeoDescription( string|null $shop_code=null ) : string
	{
		return $this->getShopData($shop_code)->getSeoDescription();
	}

	public function getSeoKeywords( string|null $shop_code=null ) : string
	{
		return $this->getShopData($shop_code)->getSeoKeywords();
	}

	public function isSeoDisableCanonical( string|null $shop_code=null ) : bool
	{
		return $this->getShopData($shop_code)->isSeoDisableCanonical();
	}

	public function getURL( string|null $shop_code=null ) : string
	{
		return $this->getShopData($shop_code)->getURL();
	}

	public function getURLPathPart( string|null $shop_code=null ): string
	{
		return $this->getShopData($shop_code)->getURLPathPart();
	}


	public function getImageMainUrl( string|null $shop_code=null ) : string
	{
		return $this->getShopData($shop_code)->getImageMainUrl();
	}

	public function getImageMainThumbnailUrl( int $max_w, int $max_h, string|null $shop_code=null ) : string
	{
		return $this->getShopData($shop_code)->getImageMainThumbnailUrl( $max_w, $max_h );
	}

	public function getImagePictogramUrl( string|null $shop_code=null ) : string
	{
		return $this->getShopData($shop_code)->getImagePictogramUrl();
	}

	public function getImagePictogramThumbnailUrl( int $max_w, int $max_h, string|null $shop_code=null ) : string
	{
		return $this->getShopData($shop_code)->getImagePictogramThumbnailUrl( $max_w, $max_h );
	}

	public function getProductListing( string|null $shop_code=null, bool $use_singleton=true ) : ProductListing|null
	{
		if($use_singleton && $this->_product_listing ) {
			return $this->_product_listing;
		}

		$listing = null;

		/**
		 * @var Category $category
		 */
		$category = $this;

		$listing = new ProductListing( $shop_code );
		$listing->setCategory( $category );
		if($this->type==Category::CATEGORY_TYPE_VIRTUAL) {
			$listing->initByTargetFilter( $this->target_filter );
		} else {
			$listing->init();
		}

		$listing->prepare( $category->getShopData($shop_code)->getProductIds() );


		if($use_singleton) {
			$this->_product_listing = $listing;
		}

		return $listing;

	}

	public function getEditURL() : string
	{
		return Category::getCategoryEditURL( $this->id );
	}

	public function getParametrizationEditUrl() : string
	{
		return Category::getManageModule()->getParametrizationEditUrl( $this->id );
	}

	public function getParametrizationGroupEditUrl( int $group_id ) : string
	{
		return Category::getManageModule()->getParametrizationGroupEditUrl( $this->id, $group_id );
	}

	public function getParametrizationPropertyEditUrl( int $group_id, int $property_id ) : string
	{
		return Category::getManageModule()->getParametrizationPropertyEditUrl( $this->id, $group_id, $property_id );
	}

	public function getParametrizationOptionEditUrl( int $group_id, int $property_id, int $option_id ) : string
	{
		return Category::getManageModule()->getParametrizationOptionEditUrl( $this->id, $group_id, $property_id, $option_id );
	}

	public static function getCategoryEditURL( int $id ) : string
	{
		return Category::getManageModule()->getCategoryEditUrl( $id );
	}

	public function getType() : string
	{
		return $this->type;
	}

	public function getTypeTitle() : string
	{
		return Tr::_( Category::$category_type_options[$this->type], [], Category::getManageModuleName() );
	}

	public function setType( string $type ) : void
	{
		$this->type = $type;
	}

	public function getParameterStrategy() : string
	{
		return $this->parameter_strategy;
	}

	public function setParameterStrategy( string $parameter_strategy ) : void
	{
		$this->parameter_strategy = $parameter_strategy;
	}

	public function getParameterInheritedCategoryId() : int
	{
		return $this->parameter_inherited_category_id;
	}

	public function setParameterInheritedCategoryId( int $parameter_inherited_category_id ) : void
	{
		$this->parameter_inherited_category_id = $parameter_inherited_category_id;
	}

	public function getTargetCategoryId() : int
	{
		return $this->target_category_id;
	}

	public function setTargetCategoryId( int $target_category_id ) : void
	{
		$this->target_category_id = $target_category_id;
	}

	public function getTargetCategory() : Category|null
	{
		if(!$this->_link_target_category) {
			$this->_link_target_category = Category::get($this->target_category_id);
		}

		return $this->_link_target_category;
	}

	public function getTargetFilter() : array
	{
		return $this->target_filter;
	}

	public function setTargetFilter( array $target_filter ) : void
	{
		$this->target_filter = $target_filter;
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

	public function getShopData( string|null $shop_code=null ) : Category_ShopData
	{
		if(!$shop_code) {
			$shop_code = Shops::getCurrentCode();
		}

		return $this->shop_data[$shop_code];
	}

	public static function getAllowedCreateTypes( Category $parent_category=null ) : array
	{

		if(
			$parent_category &&
			$parent_category->getEditForm()->getIsReadonly()
		) {
			return [];
		}

		$types = [
			Category::CATEGORY_TYPE_REGULAR  => Tr::_('regular category', [], Category::getManageModuleName() ),
			Category::CATEGORY_TYPE_TOP      => Tr::_('top category', [], Category::getManageModuleName() ),
			Category::CATEGORY_TYPE_VIRTUAL  => Tr::_('virtual category', [], Category::getManageModuleName() ),
			Category::CATEGORY_TYPE_LINK     => Tr::_('link', [], Category::getManageModuleName() )
		];

		if($parent_category) {
			switch($parent_category->getType()) {
				case Category::CATEGORY_TYPE_REGULAR:
					break;
				case Category::CATEGORY_TYPE_TOP:
					unset($types[Category::CATEGORY_TYPE_REGULAR]);
					break;
				case Category::CATEGORY_TYPE_VIRTUAL:
					unset($types[Category::CATEGORY_TYPE_REGULAR]);
					unset($types[Category::CATEGORY_TYPE_TOP]);
					break;
				case Category::CATEGORY_TYPE_LINK:
					$types = [];
					break;
			}
		}

		return $types;
	}


	public function getAddForm() : Form
	{
		if(!$this->_add_form) {
			$this->_add_form = $this->{"getAddForm_{$this->type}"}();
			$this->_add_form->setCustomTranslatorNamespace( Category::getManageModuleName() );
		}

		return $this->_add_form;
	}


	public function getAddForm_regular() : Form
	{
		$form = $this->getCommonForm('add_form');

		$form->removeField('parameter_strategy');
		$form->removeField('parameter_inherited_category_id');
		$form->removeField('target_category_id');

		foreach( Shops::getList() as $shop ) {
			$shop_code = $shop->getCode();

			$form->removeField('/shop_data/'.$shop_code.'/seo_disable_canonical');
		}

		return $form;
	}

	public function getAddForm_top() : Form
	{
		$form = $this->getCommonForm('add_form');

		$form->removeField('parameter_strategy');
		$form->removeField('parameter_inherited_category_id');
		$form->removeField('target_category_id');

		foreach( Shops::getList() as $shop ) {
			$shop_code = $shop->getCode();

			$form->removeField('/shop_data/'.$shop_code.'/seo_disable_canonical');
		}

		return $form;
	}

	public function getAddForm_virtual() : Form
	{
		$form = $this->getCommonForm('add_form');

		$form->removeField('parameter_strategy');
		$form->removeField('parameter_inherited_category_id');

		return $form;
	}

	public function getAddForm_link() : Form
	{
		$form = $this->getCommonForm('add_form');

		$form->removeField('parameter_strategy');
		$form->removeField('parameter_inherited_category_id');

		foreach( Shops::getList() as $shop ) {
			$shop_code = $shop->getCode();

			$form->removeField('/shop_data/'.$shop_code.'/seo_disable_canonical');
			$form->removeField('/shop_data/'.$shop_code.'/description');
			$form->removeField('/shop_data/'.$shop_code.'/seo_disable_canonical');
			$form->removeField('/shop_data/'.$shop_code.'/seo_h1');
			$form->removeField('/shop_data/'.$shop_code.'/seo_title');
			$form->removeField('/shop_data/'.$shop_code.'/seo_description');
			$form->removeField('/shop_data/'.$shop_code.'/seo_keywords');
			$form->removeField('/shop_data/'.$shop_code.'/internal_fulltext_keywords');
		}



		return $form;
	}

	public function catchAddForm() : bool
	{
		$add_form = $this->getAddForm();
		if(
			!$add_form->catchInput() ||
			!$add_form->validate()
		) {
			return false;
		}

		$add_form->catchData();

		return true;
	}



	public function getEditForm() : Form
	{

		if(!$this->_edit_form) {
			$this->_edit_form = $this->{"getEditForm_{$this->type}"}();
			$this->_edit_form->setCustomTranslatorNamespace( Category::getManageModuleName() );
		}

		return $this->_edit_form;
	}

	public function getEditForm_regular() : Form
	{
		$form = $this->getCommonForm('edit_form');

		$form->removeField('parameter_strategy');
		$form->removeField('target_category_id');

		foreach( Shops::getList() as $shop ) {
			$shop_code = $shop->getCode();

			$form->removeField('/shop_data/'.$shop_code.'/seo_disable_canonical');
		}

		return $form;
	}

	public function getEditForm_top() : Form
	{
		$form = $this->getCommonForm('edit_form');

		$form->removeField('parameter_strategy');
		$form->removeField('target_category_id');

		foreach( Shops::getList() as $shop ) {
			$shop_code = $shop->getCode();

			$form->removeField('/shop_data/'.$shop_code.'/seo_disable_canonical');
		}

		return $form;
	}

	public function getEditForm_virtual() : Form
	{
		$form = $this->getCommonForm('edit_form');

		$form->removeField('parameter_strategy');
		$form->removeField('parameter_inherited_category_id');

		return $form;
	}

	public function getEditForm_link() : Form
	{
		$form = $this->getCommonForm('edit_form');

		$form->removeField('parameter_strategy');
		$form->removeField('parameter_inherited_category_id');

		return $form;
	}


	public function catchEditForm() : bool
	{
		$edit_form = $this->getEditForm();
		if(
			!$edit_form->catchInput() ||
			!$edit_form->validate()
		) {
			return false;
		}

		$edit_form->catchData();

		return true;
	}


	public function getTargetFilterEditForm() : Form|bool
	{
		if($this->_target_filter_edit_form===null) {

			$target_category = $this->getTargetCategory();

			if($target_category) {
				$listing = new ProductListing();
				$listing->setCategory( $target_category );
				$listing->init();

				$this->_target_filter_edit_form = $listing->getTargetFilterEditForm( $this->target_filter );
			} else {
				$this->_target_filter_edit_form = false;
			}

		}

		return $this->_target_filter_edit_form;
	}

	public function catchTargetFilterEditForm() : bool
	{
		$target_category = $this->getTargetCategory();
		if(!$target_category) {
			return false;
		}

		$listing = new ProductListing();
		$listing->setCategory( $target_category );
		$listing->init();

		return $listing->catchTargetFilterEditForm( $this->target_filter );
	}

	public function getParametrizationStrategyForm() : Form
	{
		if(!$this->_parametrization_strategy_form) {

			$parameter_strategy = new Form_Field_Select('parameter_strategy', 'Parameter strategy:', $this->parameter_strategy);
			$parameter_strategy->setErrorMessages([
				Form_Field_Select::ERROR_CODE_INVALID_VALUE => 'Please select strategy',
			]);

			$options = [];
			foreach(Category::$parameter_strategy_options as $option=>$label) {
				$options[$option] = Tr::_($label);
			}
			$parameter_strategy->setSelectOptions( $options );

			$parameter_strategy->setCatcher( function($value) {
				$this->parameter_strategy = $value;
			} );

			$parameter_inherited_category_id = new Form_Field_Hidden('parameter_inherited_category_id', '', $this->parameter_inherited_category_id );
			$parameter_inherited_category_id->setCatcher( function($value) {
				$this->parameter_inherited_category_id = $value;
			} );

			$form = new Form('parametrization_strategy_form', [
				$parameter_strategy,
				$parameter_inherited_category_id
			]);

			$this->_parametrization_strategy_form = $form;
		}

		return $this->_parametrization_strategy_form;
	}

	public function catchParametrizationStrategyForm() : bool
	{
		$form = $this->getParametrizationStrategyForm();
		if(!$form->catchInput() || !$form->validate()) {
			return false;
		}

		$form->catchData();

		return true;
	}






	public function afterAdd() : void
	{
		foreach( Shops::getList() as $shop ) {
			$shop_code = $shop->getCode();

			$this->shop_data[$shop_code]->generateURLPathPart();
			$this->shop_data[$shop_code]->save();
		}

		Category::resetTree();
		Category::actualizeTreeData();

		/** @noinspection PhpParamsInspection */
		Fulltext::update_Category_afterAdd( $this );
	}

	public function afterUpdate() : void
	{
		/** @noinspection PhpParamsInspection */
		Fulltext::update_Category_afterUpdate( $this );

		Category::actualizeTreeData();
		Category::syncCategories();

		$this->actualizeReferences();
		$this->actualizeProductsList();
	}

	public function afterDelete() : void
	{
		/** @noinspection PhpParamsInspection */
		Fulltext::update_Category_afterDelete( $this );

		$this->actualizeReferences();
		$this->actualizeProductsList();
	}

	public function actualizeProductsList() : void
	{
		switch($this->type) {
			case Category::CATEGORY_TYPE_REGULAR:
				$this->actualizeProductsList_regular();
				break;
			case Category::CATEGORY_TYPE_TOP:
				$this->actualizeProductsList_top();
				break;
			case Category::CATEGORY_TYPE_VIRTUAL:
				$this->actualizeProductsList_virtual();
				break;
			case Category::CATEGORY_TYPE_LINK:
				$this->actualizeProductsList_link();
				break;
		}
	}

	protected function actualizeProductsList_regular() : void
	{
		$products = Product::getListByCategory( $this->id );

		$product_ids = [];
		foreach( Shops::getList() as $shop ) {
			$shop_code = $shop->getCode();

			$product_ids[$shop_code] = [];
		}

		foreach($products as $product) {
			if(!$product->isActive()) {
				continue;
			}

			foreach( Shops::getList() as $shop ) {
				$shop_code = $shop->getCode();

				if(
					!$product->getShopData($shop_code)->isActive() ||
					$product->getShopData($shop_code)->getFinalPrice()<=0
				) {
					continue;
				}

				$product_ids[$shop_code][] = $product->getId();
			}
		}

		foreach( Shops::getList() as $shop ) {
			$shop_code = $shop->getCode();

			$this->getShopData($shop_code)->setProductIds( $product_ids[$shop_code], true );
		}
	}

	protected function actualizeProductsList_virtual() : void
	{
		$target_category = $this->getTargetCategory();
		if(!$target_category) {
			foreach( Shops::getList() as $shop ) {
				$shop_code = $shop->getCode();

				$this->getShopData($shop_code)->setProductIds( [], true );

			}

			return;
		}

		foreach( Shops::getList() as $shop ) {
			$shop_code = $shop->getCode();

			$listing = new ProductListing( $shop_code );
			$listing->setCategory( $target_category );
			$listing->initByTargetFilter( $this->target_filter );

			$listing->prepare( $target_category->getShopData($shop_code)->getProductIds() );

			$ids = $listing->getFilteredProductIds();
			$this->getShopData($shop_code)->setProductIds( $ids, true );
		}
	}

	protected function actualizeProductsList_top() : void
	{
	}

	protected function actualizeProductsList_link() : void
	{

		$target_category = $this->getTargetCategory();
		if(!$target_category) {
			foreach( Shops::getList() as $shop ) {
				$shop_code = $shop->getCode();

				$this->getShopData($shop_code)->setProductIds( [], true );

			}
			return;
		}

		foreach( Shops::getList() as $shop ) {
			$shop_code = $shop->getCode();

			$listing = new ProductListing( $shop_code );
			$listing->setCategory( $target_category );
			$listing->initByTargetFilter( $this->target_filter );

			$listing->prepare( $target_category->getShopData($shop_code)->getProductIds() );

			$ids = $listing->getFilteredProductIds();
			$this->getShopData($shop_code)->setProductIds( $ids, true );

		}

	}

	public function actualizeReferences() : void
	{
		switch($this->type) {
			case Category::CATEGORY_TYPE_REGULAR:
				foreach($this->getReferences() as $r) {
					if($r->getType()==Category::CATEGORY_TYPE_LINK) {
						$r->actualizeLinkTargetUrl();
					}
				}
			break;
			case Category::CATEGORY_TYPE_LINK:
				$this->actualizeLinkTargetUrl();
			break;
			case Category::CATEGORY_TYPE_VIRTUAL:
				break;
			/** @noinspection PhpDuplicateSwitchCaseBodyInspection */
			case Category::CATEGORY_TYPE_TOP:
			break;
		}
	}

	public function actualizeLinkTargetUrl() : void
	{
		if($this->type!=Category::CATEGORY_TYPE_LINK) {
			return;
		}

		$target = $this->getTargetCategory();
		if(!$target) {
			return;
		}


		foreach( Shops::getList() as $shop ) {
			$shop_code = $shop->getCode();

			$listing = new ProductListing( $shop_code );
			$listing->setCategory( $target );
			$listing->initByTargetFilter( $this->target_filter );

			$target_url = $listing->generateCategoryTargetUrl();

			$this->getShopData($shop_code)->setURLPathPart( $target_url, true );

		}

	}

	/**
	 * @return Parametrization_Group[]
	 */
	public function getParametrizationGroups() : array
	{
		if($this->_parametrization_groups==null) {
			$this->_parametrization_groups = [];

			if(
				($inherited_category = $this->getParamInheritedCategory())
			) {
				foreach($inherited_category->getParametrizationGroups() as $group ) {
					$group = clone $group;
					$group->setIsInherited(true);
					/** @noinspection PhpParamsInspection */
					$group->setParents( $this );
					$this->_parametrization_groups[ $group->getId() ] = $group;
				}
			}


			if( $this->getCanDefineProperties() ) {
				foreach( $this->parametrization_groups  as $group ) {
					/** @noinspection PhpParamsInspection */
					$group->setParents( $this );
					$this->_parametrization_groups[ $group->getId() ] = $group;
				}
			}

		}

		return $this->_parametrization_groups;
	}

	public function getParametrizationGroup( int $id ) : Parametrization_Group|null
	{
		$this->getParametrizationGroups();

		if( !isset($this->_parametrization_groups[$id]) ) {
			return null;
		}

		return $this->_parametrization_groups[$id];
	}

	public function addParametrizationGroup( Parametrization_Group $group ) : void
	{
		$priority = 0;
		foreach( $this->parametrization_groups as $_g ) {
			if($_g->getPriority()>$priority) {
				$priority = $_g->getPriority();
			}
		}
		$priority++;
		$group->setPriority( $priority );

		/** @noinspection PhpParamsInspection */
		$group->setParents( $this );
		$this->parametrization_groups[] = $group;
	}


	/**
	 *
	 * @param int|null $group_id
	 *
	 * @return Parametrization_Property[]
	 */
	public function getParametrizationProperties( int|null $group_id=null ) : array
	{
		if($this->_parametrization_properties==null) {
			$this->_parametrization_properties = [];

			if( ($inherited_category = $this->getParamInheritedCategory()) ) {
				foreach($inherited_category->getParametrizationProperties() as $property ) {
					$property = clone $property;
					$property->setIsInherited(true);
					/** @noinspection PhpParamsInspection */
					$property->setParents( $this, $this->getParametrizationGroup($property->getGroupId()) );

					$this->_parametrization_properties[ $property->getId() ] = $property;
				}
			}


			if( $this->getCanDefineProperties() ) {
				foreach( $this->parametrization_properties  as $property ) {

					$group = $this->getParametrizationGroup($property->getGroupId());
					if(!$group) {
						continue;
					}

					/** @noinspection PhpParamsInspection */
					$property->setParents( $this,  $group);

					$this->_parametrization_properties[ $property->getId() ] = $property;
				}
			}

		}



		if(!$group_id) {
			return $this->_parametrization_properties;
		}

		$properties = [];
		foreach($this->_parametrization_properties as $id=>$property) {
			if($property->getGroupId()==$group_id) {
				$properties[$id] = $property;
			}
		}

		return $properties;
	}

	public function getParametrizationProperty( int $id ) : Parametrization_Property|null
	{
		$this->getParametrizationProperties();
		if( !isset($this->_parametrization_properties[$id]) ) {
			return null;
		}

		return $this->_parametrization_properties[$id];
	}

	public function addParametrizationProperty( int $group_id, Parametrization_Property $property ) : void
	{
		$group = $this->getParametrizationGroup($group_id);
		if(!$group) {
			return;
		}

		/** @noinspection PhpParamsInspection */
		$property->setParents( $this, $group );

		$priority = 0;
		foreach( $this->parametrization_properties as $_p ) {
			if(
				$_p->getGroupId()==$group_id &&
				$_p->getPriority()>$priority
			) {
				$priority = $_p->getPriority();
			}
		}

		$priority++;
		$property->setPriority( $priority );

		$this->parametrization_properties[] = $property;
	}


	public function getParamInheritedCategoryId() : int
	{

		if(
			$this->parameter_strategy == Category::PARAMETER_STRATEGY_TAKES_OVER_FROM_PARENT ||
			$this->parameter_strategy == Category::PARAMETER_STRATEGY_INHERITED_FROM_PARENT
		) {
			return $this->parent_id;
		}

		if(
			$this->parameter_strategy == Category::PARAMETER_STRATEGY_TAKES_OVER_FROM_OTHER_CATEGORY ||
			$this->parameter_strategy == Category::PARAMETER_STRATEGY_INHERITED_FROM_OTHER_CATEGORY
		) {
			return $this->parameter_inherited_category_id;
		}

		return 0;
	}

	public function getParamInheritedCategory() : Category|null
	{
		$id = $this->getParamInheritedCategoryId();
		if(!$id) {
			return null;
		}

		return Category::get($id);
	}

	public function getCanDefineProperties() : bool
	{
		return in_array( $this->parameter_strategy, [
			Category::PARAMETER_STRATEGY_DEFINES,
			Category::PARAMETER_STRATEGY_INHERITED_FROM_OTHER_CATEGORY,
			Category::PARAMETER_STRATEGY_INHERITED_FROM_PARENT
		] );
	}


	public static function renderSelectCategoryWidget( string $on_select,
	                                                   int $selected_category_id=0,
	                                                   int $exclude_branch_id=0,
	                                                   bool $only_active=false,
	                                                   string $name='select_category' ) : string
	{
		$view = new Mvc_View( Mvc::getCurrentSite()->getViewsPath() );

		$view->setVar('selected_category_id', $selected_category_id);
		$view->setVar('exclude_branch_id', $exclude_branch_id);
		$view->setVar('on_select', $on_select);
		$view->setVar('name', $name);
		$view->setVar('only_active', $only_active);

		return $view->render('selectCategoryWidget');
	}


	/**
	 *
	 * @return Category[]
	 */
	public function getReferences() : array
	{

		$res = [];
		if($this->type==Category::CATEGORY_TYPE_REGULAR) {
			foreach(static::fetchInstances(['target_category_id'=>$this->id]) as $cat) {
				$res[] = $cat;
			}
		}

		return $res;
	}

	public static function renderIcon( string $type, bool $no_icon_for_regular=false ) : string|UI_icon
	{
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
	}

	public static function addSyncCategory( int $id ) : void
	{
		$id = (int)$id;

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
			$sync_categories[$id] = $category;

			if($category->getType()==Category::CATEGORY_TYPE_REGULAR) {
				foreach($category->getReferences() as $r) {
					$sync_categories[$r->getId()] = $r;
				}
			}
		}


		foreach( $sync_categories as $category ) {
			if($category->getType()==Category::CATEGORY_TYPE_REGULAR) {
				$category->actualizeReferences();
				$category->actualizeProductsList();
			}
		}

		foreach( $sync_categories as $category ) {
			if($category->getType()==Category::CATEGORY_TYPE_VIRTUAL) {
				$category->actualizeReferences();
				$category->actualizeProductsList();
			}
		}

		foreach( $sync_categories as $category ) {
			if($category->getType()==Category::CATEGORY_TYPE_LINK) {
				$category->actualizeReferences();
				$category->actualizeProductsList();
			}
		}

		foreach( $sync_categories as $category ) {
			if($category->getType()==Category::CATEGORY_TYPE_TOP) {
				$category->actualizeReferences();
				$category->actualizeProductsList();
			}
		}


		Category::actualizeTreeData();

		foreach( Shops::getList() as $shop ) {
			$shop_code = $shop->getCode();


			$data = Category::fetchData(
				[
					'id' => 'id',
					'product_ids' => 'categories_shop_data.product_ids',
					'all_children' => 'categories_shop_data.all_children'
				],
				[
					'categories_shop_data.shop_code'=>$shop_code,
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
						'shop_code' => $shop_code,
					]
				);


			}

		}

	}

}