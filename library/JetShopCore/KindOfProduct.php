<?php
/**
 *
 */

namespace JetShop;

use Jet\Application_Module;
use Jet\Application_Modules;
use Jet\DataModel;
use Jet\DataModel_Definition;
use Jet\DataModel_Fetch_Instances;
use Jet\DataModel_IDController_AutoIncrement;
use Jet\Form;
use Jet\Form_Field;
use Jet\Form_Definition;
use Jet\MVC;
use Jet\MVC_View;

/**
 *
 */
#[DataModel_Definition(
	name: 'kind_of_product',
	database_table_name: 'kind_of_product',
	id_controller_class: DataModel_IDController_AutoIncrement::class,
	id_controller_options: [
		'id_property_name' => 'id'
	]
)]
abstract class Core_KindOfProduct extends DataModel
{
	
	protected static string $manage_module_name = 'Admin.Catalog.KindsOfProduct';
	
	
	/**
	 * @var int
	 */
	#[DataModel_Definition(
		type: DataModel::TYPE_ID_AUTOINCREMENT,
		is_id: true
	)]
	protected int $id = 0;
	
	/**
	 * @var ?Form
	 */
	protected ?Form $_form_edit = null;
	
	/**
	 * @var ?Form
	 */
	protected ?Form $_form_add = null;
	
	
	#[DataModel_Definition(
		type: DataModel::TYPE_BOOL,
		is_key: true
	)]
	#[Form_Definition(
		type: Form_Field::TYPE_CHECKBOX,
		label: 'Is active',
		is_required: false,
		error_messages: [
		]
	)]
	protected bool $is_active = true;
	
	/**
	 * @var string
	 */
	#[DataModel_Definition(
		type: DataModel::TYPE_STRING,
		max_len: 100
	)]
	#[Form_Definition(
		type: Form_Field::TYPE_INPUT,
		label: 'Internal name:',
		is_required: false,
		error_messages: [
		]
	)]
	protected string $internal_name = '';
	
	/**
	 * @var string
	 */
	#[DataModel_Definition(
		type: DataModel::TYPE_STRING,
		database_column_name: '65536',
		max_len: 255
	)]
	#[Form_Definition(
		type: Form_Field::TYPE_TEXTAREA,
		label: 'Internal notes:',
		is_required: false,
		error_messages: [
		]
	)]
	protected string $internal_notes = '';
	
	/**
	 * @var KindOfProduct_FilterGroup[]
	 */
	#[DataModel_Definition(
		type: DataModel::TYPE_DATA_MODEL,
		data_model_class: KindOfProduct_FilterGroup::class
	)]
	protected array $filter_groups = [];
	
	/**
	 * @var KindOfProduct_DetailGroup[]
	 */
	#[DataModel_Definition(
		type: DataModel::TYPE_DATA_MODEL,
		data_model_class: KindOfProduct_DetailGroup::class
	)]
	protected array $detail_groups = [];
	
	
	/**
	 * @var KindOfProduct_HiddenProperty[]
	 */
	#[DataModel_Definition(
		type: DataModel::TYPE_DATA_MODEL,
		data_model_class: KindOfProduct_HiddenProperty::class
	)]
	protected array $hidden_properties = [];
	
	/**
	 * @var KindOfProduct_ShopData[]
	 */
	#[DataModel_Definition(
		type: DataModel::TYPE_DATA_MODEL,
		data_model_class: KindOfProduct_ShopData::class
	)]
	#[Form_Definition(
		is_sub_forms: true
	)]
	protected array $shop_data = [];
	
	public static function getManageModuleName() : string
	{
		return self::$manage_module_name;
	}
	
	public static function getManageModule() : KindOfProduct_ManageModuleInterface|Application_Module
	{
		return Application_Modules::moduleInstance( KindOfProduct::getManageModuleName() );
	}
	
	
	public function __construct() {
		parent::__construct();
		
		$this->afterLoad();
	}
	
	public function afterLoad() : void
	{
		KindOfProduct_ShopData::checkShopData($this, $this->shop_data);
	}
	
	public function afterAdd(): void
	{
		/**
		 * @var KindOfProduct $this
		 */
		Fulltext_Index_Internal_KindOfProduct::addIndex( $this );
	}
	
	public function afterUpdate(): void
	{
		/**
		 * @var KindOfProduct $this
		 */
		Fulltext_Index_Internal_KindOfProduct::updateIndex( $this );
	}
	
	public function afterDelete(): void
	{
		/**
		 * @var KindOfProduct $this
		 */
		Fulltext_Index_Internal_KindOfProduct::deleteIndex( $this );
	}
	
	
	public function getEditForm() : Form
	{
		if(!$this->_form_edit) {
			$this->_form_edit = $this->createForm('edit_form');
		}
		
		return $this->_form_edit;
	}
	
	/**
	 * @return bool
	 */
	public function catchEditForm() : bool
	{
		return $this->getEditForm()->catch();
	}
	
	/**
	 * @return Form
	 */
	public function getAddForm() : Form
	{
		if(!$this->_form_add) {
			$this->_form_add = $this->createForm('add_form');
		}
		
		return $this->_form_add;
	}
	
	/**
	 * @return bool
	 */
	public function catchAddForm() : bool
	{
		return $this->getAddForm()->catch();
	}
	
	/**
	 * @param int|string $id
	 * @return static|null
	 */
	public static function get( int|string $id ) : static|null
	{
		return static::load( $id );
	}
	
	/**
	 * @noinspection PhpDocSignatureInspection
	 * @return static[]|DataModel_Fetch_Instances
	 */
	public static function getList() : iterable
	{
		$where = [];
		
		return static::fetchInstances( $where );
	}
	
	/**
	 * @return int
	 */
	public function getId() : int
	{
		return $this->id;
	}
	
	/**
	 * @param string $value
	 */
	public function setInternalName( string $value ) : void
	{
		$this->internal_name = $value;
	}
	
	/**
	 * @return string
	 */
	public function getInternalName() : string
	{
		return $this->internal_name;
	}
	
	/**
	 * @param string $value
	 */
	public function setInternalNotes( string $value ) : void
	{
		$this->internal_notes = $value;
	}
	
	/**
	 * @return string
	 */
	public function getInternalNotes() : string
	{
		return $this->internal_notes;
	}
	
	
	/**
	 * @return KindOfProduct_FilterGroup[]
	 */
	public function getFilterGroups() : iterable
	{
		return $this->filter_groups;
	}
	
	public function getFilterGroup( int $id ) : ?KindOfProduct_FilterGroup
	{
		if(!isset($this->filter_groups[$id])) {
			return null;
		}
		
		return $this->filter_groups[$id];
	}
	
	/**
	 * @return KindOfProduct_DetailGroup[]
	 */
	public function getDetailGroups() : iterable
	{
		return $this->detail_groups;
	}
	
	public function getDetailGroup( int $id ) : ?KindOfProduct_DetailGroup
	{
		if(!isset($this->detail_groups[$id])) {
			return null;
		}
		
		return $this->detail_groups[$id];
	}
	
	/**
	 * @param bool $value
	 */
	public function setIsActive( bool $value ) : void
	{
		$this->is_active = $value;
	}
	
	/**
	 * @return bool
	 */
	public function isActive() : bool
	{
		return $this->is_active;
	}
	
	public function getShopData( ?Shops_Shop $shop=null ) : KindOfProduct_ShopData
	{
		return $this->shop_data[$shop ? $shop->getKey() : Shops::getCurrent()->getKey()];
	}
	
	
	public function getEditURL() : string
	{
		return KindOfProduct::getKindOfProductEditURL( $this->id );
	}
	
	public static function getKindOfProductEditURL( int $id ) : string
	{
		return static::getManageModule()->getKindsOfProductEditUrl( $id );
	}
	
	public static function renderSelectKindOfProductWidget( string $on_select,
	                                                        int $selected_kind_of_product_id=0,
	                                                        bool $only_active=false,
	                                                        string $name='select_kind_of_product' ) : string
	{
		$view = new MVC_View( MVC::getBase()->getViewsPath() );
		
		$view->setVar('selected_kind_of_product_id', $selected_kind_of_product_id);
		$view->setVar('on_select', $on_select);
		$view->setVar('name', $name);
		$view->setVar('only_active', $only_active);
		
		return $view->render('select-kind-of-product-widget');
	}
	
	
	public function addDetailPropertyGroup( int $group_id ) : bool
	{
		if(isset( $this->detail_groups[$group_id] )) {
			return false;
		}
		
		$group = PropertyGroup::get($group_id);
		if(!$group) {
			return false;
		}
		
		$detail_group = new KindOfProduct_DetailGroup();
		$detail_group->setKindOfProductId( $this->id );
		$detail_group->setGroupId($group_id);
		$detail_group->setPriority( count($this->detail_groups)+1 );

		$detail_group->save();
		
		$this->detail_groups[$group_id] = $detail_group;
		
		return true;
	}
	
	public function removeDetailPropertyGroup( int $group_id ) : bool
	{
		if(!isset( $this->detail_groups[$group_id] )) {
			return false;
		}
		
		$this->detail_groups[$group_id]->delete();
		unset( $this->detail_groups[$group_id]);
		
		$priority = 0;
		foreach($this->detail_groups as $group) {
			$priority++;
			
			$group->setPriority( $priority );
			$group->save();
		}
		
		return true;
	}
	
	public function sortDetailGroups( array $sort ) : bool
	{
		if(count($sort)!=count($this->detail_groups)) {
			return false;
		}
		foreach($sort as $id) {
			if(!isset($this->detail_groups[$id])) {
				return false;
			}
		}
		
		$priority = 0;
		foreach($sort as $id) {
			$priority++;
			$this->detail_groups[$id]->setPriority( $priority );
			$this->detail_groups[$id]->save();
		}
		
		uasort( $this->detail_groups, function( KindOfProduct_DetailGroup $a, KindOfProduct_DetailGroup $b ) {
			if($a->getPriority()<$b->getPriority()) {
				return -1;
			}
			if($a->getPriority()>$b->getPriority()) {
				return 1;
			}
			
			return 0;
		} );
		
		return true;
	}
	
	
	public function addFilterPropertyGroup( int $group_id ) : bool
	{
		if(isset( $this->filter_groups[$group_id] )) {
			return false;
		}
		
		$group = PropertyGroup::get($group_id);
		if(!$group) {
			return false;
		}
		
		$filter_group = new KindOfProduct_FilterGroup();
		$filter_group->setKindOfProductId( $this->id );
		$filter_group->setGroupId($group_id);
		$filter_group->setPriority( count($this->filter_groups)+1 );
		
		$filter_group->save();
		
		$this->filter_groups[$group_id] = $filter_group;
		
		return true;
	}
	
	public function removeFilterPropertyGroup( int $group_id ) : bool
	{
		if(!isset( $this->filter_groups[$group_id] )) {
			return false;
		}
		
		$this->filter_groups[$group_id]->delete();
		unset( $this->filter_groups[$group_id]);
		
		$priority = 0;
		foreach($this->filter_groups as $group) {
			$priority++;
			
			$group->setPriority( $priority );
			$group->save();
		}
		
		return true;
	}
	
	public function sortFilterGroups( array $sort ) : bool
	{
		if(count($sort)!=count($this->filter_groups)) {
			return false;
		}
		foreach($sort as $id) {
			if(!isset($this->filter_groups[$id])) {
				return false;
			}
		}
		
		$priority = 0;
		foreach($sort as $id) {
			$priority++;
			$this->filter_groups[$id]->setPriority( $priority );
			$this->filter_groups[$id]->save();
		}
		
		uasort( $this->filter_groups, function( KindOfProduct_FilterGroup $a, KindOfProduct_FilterGroup $b ) {
			if($a->getPriority()<$b->getPriority()) {
				return -1;
			}
			if($a->getPriority()>$b->getPriority()) {
				return 1;
			}
			
			return 0;
		} );
		
		return true;
	}
	
	/**
	 * @return Property[]
	 */
	public function getAllProperties() : array
	{
		$properties = [];
		
		foreach($this->getDetailGroups() as $group) {
			foreach($group->getProperties() as $p) {
				$property = $p->getProperty();
				$properties[$property->getId()] = $property;
			}
		}
		
		foreach($this->getFilterGroups() as $group) {
			foreach($group->getProperties() as $p) {
				$property = $p->getProperty();
				if(!isset($properties[$property->getId()])) {
					$properties[$property->getId()] = $property;
				}
			}
		}
		
		foreach($this->hidden_properties as $p) {
			$property = $p->getProperty();
			$properties[$property->getId()] = $property;
		}
		
		return $properties;
	}
	
	/**
	 * @return Property[]
	 */
	public function getVariantSelectorProperties() : array
	{
		$properties = [];
		
		foreach($this->getDetailGroups() as $group) {
			foreach($group->getProperties() as $p) {
				if($p->getIsVariantSelector()) {
					$property = $p->getProperty();
					$properties[$property->getId()] = $property;
				}
			}
		}
		
		return $properties;
	}
	
	public function setDetailByFilter() : bool
	{
		foreach($this->detail_groups as $g) {
			$g->delete();
		}
		$this->detail_groups = [];
		
		foreach($this->filter_groups as $f_g) {
			$this->addDetailPropertyGroup($f_g->getGroupId());
			
			$d_g = $this->detail_groups[$f_g->getGroupId()];
			
			foreach($f_g->getProperties() as $f_p) {
				$d_g->addProperty( $f_p->getPropertyId() );
			}
		}
		
		//TODO: sync
		
		return true;
	}
	
	public function setFilterByDetail() : bool
	{
		foreach($this->filter_groups as $g) {
			$g->delete();
		}
		$this->filter_groups = [];
		
		foreach($this->detail_groups as $d_g) {
			$this->addFilterPropertyGroup($d_g->getGroupId());
			
			$f_g = $this->filter_groups[$d_g->getGroupId()];
			
			foreach($d_g->getProperties() as $d_p) {
				$f_g->addProperty( $d_p->getPropertyId() );
			}
		}
		
		//TODO: sync
		
		return true;
	}
	
	public function setPropertyIsVariantMaster( int $property_id, bool $state ) : bool
	{
		$property = null;
		foreach($this->detail_groups as $group) {
			$property = $group->getProperty( $property_id );
			if($property) {
				break;
			}
		}
		
		if(!$property) {
			return false;
		}
		
		$property->setIsVariantSelector( $state );
		$property->save();
		
		return true;
	}
	
	
	/**
	 * @return KindOfProduct[]
	 */
	public static function getByProperty( Property $property ) : array
	{
		$res = [];
		
		$detail_ids = KindOfProduct_DetailGroup_Property::dataFetchCol(
			select: ['kind_of_product_id'],
			where: ['property_id'=>$property->getId()]
		);
		$filter_ids = KindOfProduct_FilterGroup_Property::dataFetchCol(
			select: ['kind_of_product_id'],
			where: ['property_id'=>$property->getId()]
		);
		$hidden_ids = KindOfProduct_HiddenProperty::dataFetchCol(
			select: ['kind_of_product_id'],
			where: ['property_id'=>$property->getId()]
		);
		
		$ids = array_unique(array_merge($detail_ids, $filter_ids, $hidden_ids));
		
		if(!$ids) {
			return [];
		}
		
		return KindOfProduct::fetch(['kind_of_product'=>['id'=>$ids]]);
	}
	
	
	/**
	 * @return KindOfProduct[]
	 */
	public static function getByPropertyGroup( PropertyGroup $property_group ) : array
	{
		$res = [];
		
		$detail_ids = KindOfProduct_DetailGroup::dataFetchCol(
			select: ['kind_of_product_id'],
			where: ['group_id'=>$property_group->getId()]
		);
		$filter_ids = KindOfProduct_FilterGroup::dataFetchCol(
			select: ['kind_of_product_id'],
			where: ['group_id'=>$property_group->getId()]
		);
		
		$ids = array_unique(array_merge($detail_ids, $filter_ids));
		
		if(!$ids) {
			return [];
		}
		
		return KindOfProduct::fetch(['kind_of_product'=>['id'=>$ids]]);
	}
	
	public function isItPossibleToDelete( array|null &$used_by_products=[], array|null &$used_by_categories=[] ) : bool
	{
		$used_by_products = Product::getByKind( $this );
		$used_by_categories = Category::getByKindOfProduct( $this );
		
		return count($used_by_products)==0 && count($used_by_categories)==0;
	}
	
	/**
	 * @return KindOfProduct_HiddenProperty[]
	 */
	public function getHiddenProperties(): array
	{
		return $this->hidden_properties;
	}

	public function addHiddenProperty( int $property_id ) : bool
	{
		if(isset( $this->hidden_properties[$property_id] )) {
			return false;
		}
		
		$group = Property::get($property_id);
		if(!$group) {
			return false;
		}
		
		$hidden_property = new KindOfProduct_HiddenProperty();
		$hidden_property->setKindOfProductId( $this->id );
		$hidden_property->setPropertyId($property_id);
		$hidden_property->setPriority( count($this->hidden_properties)+1 );
		
		$hidden_property->save();
		
		$this->hidden_properties[$property_id] = $hidden_property;
		
		return true;
		
	}
	
	public function removeHiddenProperty( int $property_id ) : bool
	{
		if(!isset( $this->hidden_properties[$property_id] )) {
			return false;
		}
		
		$this->hidden_properties[$property_id]->delete();
		unset( $this->hidden_properties[$property_id]);
		
		$priority = 0;
		foreach($this->hidden_properties as $property) {
			$priority++;
			
			$property->setPriority( $priority );
			$property->save();
		}
		
		return true;
	}
	
	public function sortHiddenProperties( array $sort ) : bool
	{
		if(count($sort)!=count($this->hidden_properties)) {
			return false;
		}
		foreach($sort as $id) {
			if(!isset($this->hidden_properties[$id])) {
				return false;
			}
		}
		
		$priority = 0;
		foreach($sort as $id) {
			$priority++;
			$this->hidden_properties[$id]->setPriority( $priority );
			$this->hidden_properties[$id]->save();
		}
		
		uasort( $this->hidden_properties, function( KindOfProduct_HiddenProperty $a, KindOfProduct_HiddenProperty $b ) {
			if($a->getPriority()<$b->getPriority()) {
				return -1;
			}
			if($a->getPriority()>$b->getPriority()) {
				return 1;
			}
			
			return 0;
		} );
		
		return true;
	}
	
}
