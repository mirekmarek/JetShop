<?php
namespace JetShop;

use Jet\Application_Modules;
use Jet\DataModel;
use Jet\Data_DateTime;
use Jet\DataModel_Definition;
use Jet\Form;
use Jet\DataModel_Fetch_Instances;
use Jet\Form_Field_Checkbox;
use Jet\Form_Field_Date;
use Jet\Form_Field_Input;
use Jet\Form_Field_MultiSelect;
use Jet\Form_Field_Select;
use Jet\DataModel_IDController_AutoIncrement;
use Jet\Application_Module;
use Jet\Mvc;
use Jet\Mvc_View;

/**
 *
 *
 */
#[DataModel_Definition(
	name: 'products',
	database_table_name: 'products',
	id_controller_class: DataModel_IDController_AutoIncrement::class,
	id_controller_options: ['id_property_name'=>'id']

)]
abstract class Core_Product extends DataModel {
	protected static string $manage_module_name = 'Admin.Catalog.Products';
	
	const PRODUCT_TYPE_REGULAR        = 'regular';
	const PRODUCT_TYPE_VARIANT_MASTER = 'variant_master';
	const PRODUCT_TYPE_VARIANT        = 'variant';
	const PRODUCT_TYPE_SET            = 'set';

	#[DataModel_Definition(
		type: DataModel::TYPE_ID_AUTOINCREMENT,
		is_id: true
	)]
	protected int $id = 0;

	#[DataModel_Definition(
		type: DataModel::TYPE_STRING,
		max_len: 100,
		is_key: true,
		form_field_type: false
	)]
	protected string $type = Product::PRODUCT_TYPE_REGULAR;

	#[DataModel_Definition(
		type: DataModel::TYPE_BOOL,
		form_field_label: 'Is active',
		is_key: true
	)]
	protected bool $is_active = true;

	#[DataModel_Definition(
		type: DataModel::TYPE_STRING,
		max_len: 100,
		is_key: true,
		form_field_label: 'EAN:'
	)]
	protected string $ean = '';

	#[DataModel_Definition(
		type: DataModel::TYPE_STRING,
		max_len: 100,
		is_key: true,
		form_field_label: 'Internal code:'
	)]
	protected string $internal_code = '';

	#[DataModel_Definition(
		type: DataModel::TYPE_STRING,
		max_len: 100,
		is_key: true,
		form_field_type: false
	)]
	protected string $erp_id = '';

	#[DataModel_Definition(
		type: DataModel::TYPE_DATE_TIME,
		form_field_type: false
	)]
	protected ?Data_DateTime $added_date_time = null;

	#[DataModel_Definition(
		type: DataModel::TYPE_INT,
		is_key: true,
		form_field_label: 'Brand:',
		form_field_type: Form::TYPE_SELECT,
		form_field_get_select_options_callback: [Brand::class,'getScope']
	)]
	protected int $brand_id = 0;

	#[DataModel_Definition(
		type: DataModel::TYPE_INT,
		is_key: true,
		form_field_label: 'Supplier:',
		form_field_type: Form::TYPE_SELECT,
		form_field_get_select_options_callback: [Supplier::class,'getScope']
	)]
	protected int $supplier_id = 0;

	#[DataModel_Definition(
		type: DataModel::TYPE_INT,
		is_key: true,
		form_field_type: false
	)]
	protected int $variant_master_product_id = 0;

	#[DataModel_Definition(
		type: DataModel::TYPE_STRING,
		max_len: 512,
		form_field_type: false
	)]
	protected int|string $variant_ids = '';

	/**
	 * @var Product[]
	 */
	protected array|null $_variants = null;

	#[DataModel_Definition(
		type: DataModel::TYPE_STRING,
		max_len: 512,
		form_field_type: false
	)]
	protected string $variant_control_property_ids = '';


	/**
	 * @var Parametrization_Property[]
	 */
	protected array|null $variant_control_properties = null;

	#[DataModel_Definition(
		type: DataModel::TYPE_BOOL,
		form_field_label: 'Synchronize categories'
	)]
	protected bool $variant_sync_categories = true;

	#[DataModel_Definition(
		type: DataModel::TYPE_BOOL,
		form_field_label: 'Synchronize descriptions'
	)]
	protected bool $variant_sync_descriptions = true;

	#[DataModel_Definition(
		type: DataModel::TYPE_BOOL,
		form_field_label: 'Synchronize stickers'
	)]
	protected bool $variant_sync_stickers = true;

	#[DataModel_Definition(
		type: DataModel::TYPE_BOOL,
		form_field_label: 'Synchronize prices'
	)]
	protected bool $variant_sync_prices = true;

	#[DataModel_Definition(
		type: DataModel::TYPE_INT,
		is_key: true,
		form_field_type: false
	)]
	protected int $main_category_id = 0;

	#[DataModel_Definition(
		type: DataModel::TYPE_STRING,
		max_len: 65536,
		form_field_type: false
	)]
	protected string $category_ids = '';

	#[DataModel_Definition(
		type: DataModel::TYPE_INT,
		form_field_type: false
	)]
	protected int $review_count = 0;

	#[DataModel_Definition(
		type: DataModel::TYPE_INT,
		form_field_type: false
	)]
	protected int $review_rank = 0;

	#[DataModel_Definition(
		type: DataModel::TYPE_INT,
		form_field_type: false
	)]
	protected int $question_count = 0;


	/**
	 * @var Product_ShopData[]
	 */
	#[DataModel_Definition(
		type: DataModel::TYPE_DATA_MODEL,
		data_model_class: Product_ShopData::class
	)]
	protected $shop_data = null;


	/**
	 * @var Product_Category[]
	 */
	#[DataModel_Definition(
		type: DataModel::TYPE_DATA_MODEL,
		data_model_class: Product_Category::class,
		form_field_type: false
	)]
	protected $categories = null;

	/**
	 * @var Category[]
	 */
	protected array|null $_categories = null;

	/**
	 * @var Product_Sticker[]
	 */
	#[DataModel_Definition(
		type: DataModel::TYPE_DATA_MODEL,
		data_model_class: Product_Sticker::class,
		form_field_type: false
	)]
	protected $stickers;

	/**
	 * @var Sticker[]
	 */
	protected array|null $_stickers = null;


	/**
	 * @var Product_SetItem[]
	 */
	#[DataModel_Definition(
		type: DataModel::TYPE_DATA_MODEL,
		data_model_class: Product_SetItem::class,
		form_field_type: false
	)]
	protected $set_items;

	/**
	 * @var Product_ParametrizationValue[]
	 */
	#[DataModel_Definition(
		type: DataModel::TYPE_DATA_MODEL,
		data_model_class: Product_ParametrizationValue::class,
		form_field_type: false
	)]
	protected $parametrization_values;


	protected ?Form $_add_form = null;

	protected ?Form $_edit_form = null;

	protected ?Form $_parametrization_edit_form = null;

	protected ?Form $_variant_setup_form = null;

	protected ?Form $_variant_add_form = null;

	protected ?Form $_update_variants_form = null;

	/**
	 * @var Product[]
	 */
	protected static array $loaded_items = [];

	public static function getManageModuleName() : string
	{
		return self::$manage_module_name;
	}

	public static function getManageModule() : Product_ManageModuleInterface|Application_Module
	{
		return Application_Modules::moduleInstance( Product::getManageModuleName() );
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

				$sh = new Product_ShopData();
				$sh->setShopCode($shop_code);

				$this->shop_data[$shop_code] = $sh;
			}

			/** @noinspection PhpParamsInspection */
			$this->shop_data[$shop_code]->setParents( $this );
		}

		foreach( $this->categories as $c ) {
			/** @noinspection PhpParamsInspection */
			$c->setParents( $this );
		}

	}

	public static function get( int $id ) : Product|null
	{
		if(isset(static::$loaded_items[$id])) {
			return static::$loaded_items[$id];
		}

		$cache_key = 'product:'.$id;

		static::$loaded_items[$id] = Cache::load( $cache_key );

		if(!static::$loaded_items[$id]) {
			static::$loaded_items[$id] = Product::load( $id );

			if( static::$loaded_items[$id] ) {
				Cache::save( $cache_key, static::$loaded_items[$id]);
			}
		}

		return static::$loaded_items[$id];
	}


	/**
	 *
	 * @param array $where
	 *
	 * @return DataModel_Fetch_Instances|Product[]
	 */
	public static function getList( array $where=[] ) : DataModel_Fetch_Instances|array
	{
		$list = static::fetchInstances( $where );

		$list->getQuery()->setOrderBy( 'products_shop_data.name' );

		return $list;
	}

	/**
	 *
	 * @param int $category_id
	 *
	 * @return DataModel_Fetch_Instances|Product[]
	 */
	public static function getListByCategory( int $category_id ) : DataModel_Fetch_Instances|array
	{
		return static::fetchInstances( [
			'products_categories.category_id' => $category_id
		] );

	}


	public function getId() : int
	{
		return $this->id;
	}

	public function setId( int $id ) : void
	{
		$this->id = $id;
	}

	public function getType() : string
	{
		return $this->type;
	}

	public function setType( string $type ) : void
	{
		$this->type = $type;
	}

	public function isActive() : bool
	{
		return $this->is_active;
	}

	public function setIsActive( bool $is_active ) : void
	{
		$this->is_active = $is_active;
	}

	public function getEan() : string
	{
		return $this->ean;
	}

	public function setEan( string $ean ) : void
	{
		$this->ean = $ean;
	}

	public function getInternalCode() : string
	{
		return $this->internal_code;
	}

	public function setInternalCode( string $internal_code ) : void
	{
		$this->internal_code = $internal_code;
	}

	public function getErpId() : string
	{
		return $this->erp_id;
	}

	public function setErpId( string $erp_id ) : void
	{
		$this->erp_id = $erp_id;
	}

	public function getAddedDateTime() : Data_DateTime|null
	{
		return $this->added_date_time;
	}

	public function setAddedDateTime( Data_DateTime $added_date_time ) : void
	{
		$this->added_date_time = $added_date_time;
	}

	public function getBrandId() : int
	{
		return $this->brand_id;
	}

	public function setBrandId( int $brand_id ) : void
	{
		$this->brand_id = $brand_id;
	}

	public function getSupplierId() : int
	{
		return $this->supplier_id;
	}

	public function setSupplierId( int $supplier_id ) : void
	{
		$this->supplier_id = $supplier_id;
	}

	public function getVariantMasterProductId() : int
	{
		return $this->variant_master_product_id;
	}

	public function setVariantMasterProductId( int $variant_master_product_id ) : void
	{
		$this->variant_master_product_id = $variant_master_product_id;
	}

	public function getVariantControlPropertyIds() : array
	{
		if(!$this->variant_control_property_ids) {
			return [];
		} else {
			return explode(',', $this->variant_control_property_ids);
		}
	}

	/**
	 * @return Parametrization_Property[]
	 */
	public function getVariantControlProperties() : array
	{
		if($this->variant_control_properties===null) {
			$variant_control_property_ids = $this->getVariantControlPropertyIds();
			foreach($this->getCategories() as $category) {
				foreach($category->getParametrizationProperties() as $property) {
					if(
						$property->isVariantSelector() &&
						!in_array($property->getId(), $variant_control_property_ids)
					) {
						$variant_control_property_ids[] = $property->getId();
					}
				}
			}

			$this->variant_control_properties = [];

			foreach($this->getCategories() as $category) {
				foreach($category->getParametrizationProperties() as $property) {
					if(
						in_array($property->getId(), $variant_control_property_ids)
					) {
						$this->variant_control_properties[$property->getId()] = $property;
					}
				}
			}
		}

		return $this->variant_control_properties;

	}

	public function setVariantControlPropertyIds( array $variant_control_property_ids ) : void
	{
		$this->variant_control_property_ids = implode(',', $variant_control_property_ids);
	}

	public function isVariantSyncCategories() : bool
	{
		return $this->variant_sync_categories;
	}

	public function setVariantSyncCategories( bool $variant_sync_categories ) : void
	{
		$this->variant_sync_categories = $variant_sync_categories;
	}

	public function isVariantSyncDescriptions() : bool
	{
		return $this->variant_sync_descriptions;
	}

	public function setVariantSyncDescriptions( bool $variant_sync_descriptions ) : void
	{
		$this->variant_sync_descriptions = $variant_sync_descriptions;
	}

	public function isVariantSyncStickers() : bool
	{
		return $this->variant_sync_stickers;
	}

	public function setVariantSyncStickers( bool $variant_sync_stickers ) : void
	{
		$this->variant_sync_stickers = $variant_sync_stickers;
	}

	public function isVariantSyncPrices() : bool
	{
		return $this->variant_sync_prices;
	}

	public function setVariantSyncPrices( bool $variant_sync_prices ) : void
	{
		$this->variant_sync_prices = $variant_sync_prices;
	}

	public function getInternalName( string|null $shop_code=null ) : string
	{
		if(!$shop_code) {
			$shop_code = Shops::getCurrentCode();
		}

		$name = $this->getShopData( $shop_code )->getName();

		$name .= ' ('.$this->internal_code.')';

		return $name;
	}

	public function getShopData( string|null $shop_code=null ) : Product_ShopData
	{
		if(!$shop_code) {
			$shop_code = Shops::getCurrentCode();
		}

		return $this->shop_data[$shop_code];
	}

	public function getEditURL() : string
	{
		return Product::getProductEditURL( $this->id );
	}

	public static function getProductEditURL( int $id ) : string
	{
		/**
		 * @var Product_ManageModuleInterface $module
		 */
		$module = Application_Modules::moduleInstance( Product::getManageModuleName() );

		return $module->getProductEditUrl( $id );
	}

	protected function _setupForm( Form $form ) : void
	{
		$form->field('brand_id')->setErrorMessages([
			Form_Field_Select::ERROR_CODE_INVALID_VALUE => 'Please select value'
		]);
		$form->field('supplier_id')->setErrorMessages([
			Form_Field_Select::ERROR_CODE_INVALID_VALUE => 'Please select value'
		]);

		$form->removeField('variant_sync_categories');
		$form->removeField('variant_sync_descriptions');
		$form->removeField('variant_sync_stickers');
		$form->removeField('variant_sync_prices');


		foreach(Sticker::getList() as $sticker) {
			$s_id = $sticker->getId();

			$form->addField( new Form_Field_Checkbox('/sticker/'.$s_id, $sticker->getShopData()->getName(), isset($this->stickers[$s_id]) ) );
		}


		foreach(Shops::getList() as $shop) {
			$shop_code = $shop->getCode();

			$vat_rate = new Form_Field_Select('/shop_data/'.$shop_code.'/vat_rate', 'VAT rate:', $this->getShopData($shop_code)->getVatRate() );

			$vat_rate->setErrorMessages([
				Form_Field_Select::ERROR_CODE_INVALID_VALUE => 'Invalid date'
			]);
			$vat_rate->setCatcher(function( $value ) use ($shop_code) {
				$this->getShopData($shop_code)->setVatRate( $value );
			});
			$vat_rate->setSelectOptions( Shops::getVatRatesScope( $shop_code ) );


			$form->removeField('/shop_data/'.$shop_code.'/vat_rate');
			$form->addField( $vat_rate );


			$form->field('/shop_data/'.$shop_code.'/date_available')->setErrorMessages([
				Form_Field_Date::ERROR_CODE_INVALID_FORMAT => 'Invalid date'
			]);

			$form->field('/shop_data/'.$shop_code.'/action_price_valid_from')->setErrorMessages([
				Form_Field_Date::ERROR_CODE_INVALID_FORMAT => 'Invalid date'
			]);

			$form->field('/shop_data/'.$shop_code.'/action_price_valid_till')->setErrorMessages([
				Form_Field_Date::ERROR_CODE_INVALID_FORMAT => 'Invalid date'
			]);

			$form->field('/shop_data/'.$shop_code.'/delivery_class_code')->setErrorMessages([
				Form_Field_Select::ERROR_CODE_INVALID_VALUE => 'Please select value'
			]);

			$form->field('/shop_data/'.$shop_code.'/delivery_term_code')->setErrorMessages([
				Form_Field_Select::ERROR_CODE_INVALID_VALUE => 'Please select value'
			]);
		}

		$form->setCustomTranslatorNamespace( Product::getManageModuleName() );

	}

	public function getAddForm() : Form
	{
		if(!$this->_add_form) {
			$this->_add_form = $this->getCommonForm('add_form');
			$this->_add_form->setCustomTranslatorNamespace( Brand::getManageModuleName() );

			$this->_setupForm( $this->_add_form );

			foreach(Shops::getList() as $shop) {
				$shop_code = $shop->getCode();

				$this->_add_form->field( '/shop_data/' . $shop_code . '/vat_rate' )->setDefaultValue( Shops::getDefaultVatRate( $shop_code ) );
			}
		}

		return $this->_add_form;
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

		foreach(Sticker::getList() as $sticker) {
			$s_id = $sticker->getId();
			if($add_form->field('/sticker/'.$s_id)->getValue()) {
				$this->addSticker( $s_id );
			} else {
				$this->removeSticker( $s_id );
			}
		}


		$this->actualizePrices();

		return true;
	}


	public function getEditForm() : Form
	{
		if(!$this->_edit_form) {
			$this->_edit_form = $this->getCommonForm('edit_form');
			$this->_edit_form->setCustomTranslatorNamespace( Brand::getManageModuleName() );

			$this->_setupForm( $this->_edit_form );

			if($this->type==Product::PRODUCT_TYPE_VARIANT) {
				$this->_edit_form->field('brand_id')->setIsReadonly(true);
				$this->_edit_form->field('supplier_id')->setIsReadonly(true);

				foreach(Shops::getList() as $shop) {
					$shop_code = $shop->getCode();

					$this->_edit_form->field('/shop_data/'.$shop_code.'/vat_rate')->setIsReadonly(true);
					$this->_edit_form->field('/shop_data/'.$shop_code.'/delivery_term_id')->setIsReadonly(true);
					$this->_edit_form->field('/shop_data/'.$shop_code.'/date_available')->setIsReadonly(true);

				}

				$master = Product::get($this->variant_master_product_id);

				if($master) {
					if($master->variant_sync_prices) {
						foreach(Shops::getList() as $shop) {
							$shop_code = $shop->getCode();

							$this->_edit_form->field('/shop_data/'.$shop_code.'/standard_price')->setIsReadonly(true);
							$this->_edit_form->field('/shop_data/'.$shop_code.'/action_price')->setIsReadonly(true);
							$this->_edit_form->field('/shop_data/'.$shop_code.'/action_price_valid_from')->setIsReadonly(true);
							$this->_edit_form->field('/shop_data/'.$shop_code.'/action_price_valid_till')->setIsReadonly(true);
						}
					}
					if($master->variant_sync_descriptions) {
						foreach(Shops::getList() as $shop) {
							$shop_code = $shop->getCode();

							$this->_edit_form->field('/shop_data/'.$shop_code.'/name')->setIsReadonly(true);
							$this->_edit_form->field('/shop_data/'.$shop_code.'/description')->setIsReadonly(true);
							$this->_edit_form->field('/shop_data/'.$shop_code.'/short_description')->setIsReadonly(true);
							$this->_edit_form->field('/shop_data/'.$shop_code.'/seo_title')->setIsReadonly(true);
							$this->_edit_form->field('/shop_data/'.$shop_code.'/seo_h1')->setIsReadonly(true);
							$this->_edit_form->field('/shop_data/'.$shop_code.'/seo_description')->setIsReadonly(true);
							$this->_edit_form->field('/shop_data/'.$shop_code.'/seo_keywords')->setIsReadonly(true);
							$this->_edit_form->field('/shop_data/'.$shop_code.'/internal_fulltext_keywords')->setIsReadonly(true);
						}
					}

					if($master->variant_sync_stickers) {
						foreach(Sticker::getList() as $sticker) {
							$this->_edit_form->field('/sticker/'.$sticker->getId())->setIsReadonly(true);
						}
					}

				}
			}
		}

		return $this->_edit_form;
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

		foreach(Sticker::getList() as $sticker) {
			$s_id = $sticker->getId();
			if($edit_form->field('/sticker/'.$s_id)->getValue()) {
				$this->addSticker( $s_id );
			} else {
				$this->removeSticker( $s_id );
			}
		}


		$this->actualizePrices();

		return true;
	}

	/**
	 * @return Category[]
	 */
	public function getCategories() : array
	{
		if($this->_categories===null) {
			$this->_categories = [];
			foreach($this->categories as $c) {
				$category = Category::get($c->getCategoryId());
				if($category) {
					$this->_categories[ $category->getId() ] = $category;
				}
			}
		}

		return $this->_categories;
	}

	public function hasCategory( int $category_id ) : bool
	{
		return isset($this->categories[$category_id]);
	}

	public function addCategory( int $category_id ) : bool
	{
		$category = Category::get( (int)$category_id );
		if(!$category) {
			return false;
		}

		if(isset($this->categories[$category->getId()])) {
			return false;
		}

		$_category = new Product_Category();
		$_category->setProductId( $this->id );
		$_category->setCategoryId( $category->getId() );

		$this->categories[] = $_category;

		if(count($this->categories)==1) {
			$this->main_category_id = $category->getId();
		}

		Category::addSyncCategory( $category_id );


		return true;
	}

	public function removeCategory( int $category_id ) : bool
	{
		$category_id = (int)$category_id;

		if(!isset($this->categories[$category_id])) {
			return false;
		}

		unset($this->categories[$category_id]);
		if( $this->_categories ) {
			unset($this->_categories[$category_id]);
		}

		if($category_id==$this->main_category_id) {
			$this->main_category_id = 0;
			foreach( $this->categories as $c ) {
				$this->main_category_id = $c->getCategoryId();
				break;
			}
		}

		Category::addSyncCategory( $category_id );

		return true;
	}

	public function setMainCategory( int $category_id ) : bool
	{
		$category_id = (int)$category_id;

		if(!isset($this->categories[$category_id])) {
			return false;
		}

		$this->main_category_id = $category_id;

		Category::addSyncCategory( $category_id );

		return true;
	}

	public function getMainCategoryId() : int
	{
		return $this->main_category_id;
	}

	public function getMainCategory() : Category|null
	{
		if(!$this->main_category_id) {
			return null;
		}

		return Category::get($this->main_category_id);
	}




	/**
	 * @return Sticker[]
	 */
	public function getStickers() : array
	{
		if($this->_stickers===null) {
			$this->_stickers = [];
			foreach($this->stickers as $s) {
				$sticker = Category::get($s->getStickerId());
				if($sticker) {
					$this->_stickers[ $sticker->getId() ] = $sticker;
				}
			}
		}

		return $this->_stickers;
	}

	public function hasSticker( int $sticker_id ) : bool
	{
		return isset($this->stickers[$sticker_id]);
	}

	public function addSticker( int $sticker_id ) : bool
	{
		$sticker = Sticker::get( (int)$sticker_id );
		if(!$sticker) {
			return false;
		}

		if(isset($this->stickers[$sticker->getId()])) {
			return false;
		}

		$_sticker = new Product_Sticker();
		$_sticker->setProductId( $this->id );
		$_sticker->setStickerId( $sticker->getId() );

		$this->stickers[] = $_sticker;

		return true;
	}

	public function removeSticker( int $sticker_id ) : bool
	{
		$sticker_id = (int)$sticker_id;

		if(!isset($this->stickers[$sticker_id])) {
			return false;
		}

		unset($this->stickers[$sticker_id]);
		if( $this->_stickers ) {
			unset($this->_stickers[$sticker_id]);
		}

		return true;
	}




	public function getParametrizationEditForm() : Form
	{
		if(!$this->_parametrization_edit_form) {

			$fields = [];

			$enable_only = null;
			if($this->type==Product::PRODUCT_TYPE_VARIANT) {
				$variant_master = Product::get($this->variant_master_product_id);

				$enable_only = $variant_master->getVariantControlPropertyIds();
			}

			foreach( $this->getCategories() as $category ) {
				foreach($category->getParametrizationProperties() as $property) {
					$property_id = $property->getId();

					if(!isset($this->parametrization_values[$property_id])) {
						$pv = new Product_ParametrizationValue();
						$this->parametrization_values[$property_id] = $pv;
					} else {
						$pv = $this->parametrization_values[$property_id];
					}

					/** @noinspection PhpParamsInspection */
					$pv->setParents( $this );
					$pv->setProperty( $property );

					$disabled = false;
					if(
						$enable_only!==null &&
						!in_array($property_id, $enable_only)
					) {
						$disabled = true;
					}

					foreach( $pv->getValueEditForm()->getFields() as $field ) {
						$field->setName('/'.$category->getId().'/'.$property->getGroupId().'/'.$property->getId().'/'.$field->getName());
						if($disabled) {
							$field->setIsReadonly(true);
						}
						$fields[] = $field;
					}
				}
			}

			$form = new Form('parametrization_edit_form', $fields);
			$form->setDoNotTranslateTexts(true);

			$this->_parametrization_edit_form = $form;
		}

		return $this->_parametrization_edit_form;
	}

	public function catchParametrizationEditForm() : bool
	{
		$edit_form = $this->getParametrizationEditForm();
		if(
			!$edit_form->catchInput() ||
			!$edit_form->validate()
		) {
			return false;
		}

		$edit_form->catchData();

		foreach($this->getCategories() as $c) {
			Category::addSyncCategory( $c->getId() );
		}


		return true;

	}


	public function getVariantSetupForm() : Form
	{
		if(!$this->_variant_setup_form) {
			$this->_variant_setup_form = $this->getForm('variant_setup_form', [
				'variant_sync_categories',
				'variant_sync_descriptions',
				'variant_sync_stickers',
				'variant_sync_prices',
			]);


			$variant_control_property_ids = new Form_Field_MultiSelect('variant_control_property_ids', 'Distinguish variants by:', $this->getVariantControlPropertyIds());
			$variant_control_property_ids->setCatcher(function($value) {
				$this->setVariantControlPropertyIds($value);
			});
			$variant_control_property_ids->setErrorMessages([
				Form_Field_MultiSelect::ERROR_CODE_INVALID_VALUE => 'Invalid value'
			]);

			$properties = [];

			foreach($this->getCategories() as $category) {
				foreach($category->getParametrizationProperties() as $property) {
					$properties[$property->getId()] = $property->getGroup()->getShopData()->getLabel().' - '.$property->getShopData()->getLabel();
				}
			}


			$variant_control_property_ids->setSelectOptions( $properties );


			$this->_variant_setup_form->addField($variant_control_property_ids);
		}

		return $this->_variant_setup_form;
	}

	public function catchVariantSetupForm() : bool
	{
		$edit_form = $this->getVariantSetupForm();
		if(
			!$edit_form->catchInput() ||
			!$edit_form->validate()
		) {
			return false;
		}

		$edit_form->catchData();

		foreach($this->getCategories() as $c) {
			Category::addSyncCategory( $c->getId() );
		}

		return true;
	}


	public function getAddVariantForm() : Form
	{

		if(!$this->_variant_add_form) {
			$this->_variant_add_form = $this->getCommonForm('variant_add_form');

			foreach($this->_variant_add_form->getFields() as $field) {
				$keep = false;

				if(
					$field->getName()=='ean' ||
					$field->getName()=='internal_code'
				) {
					$keep = true;
				}

				if(!$keep) {
					foreach( Shops::getList() as $shop ) {
						$shop_code = $shop->getCode();

						if( $field->getName()=='/shop_data/'.$shop_code.'/variant_name' ) {
							$keep = true;
							break;
						}
					}
				}

				if(!$keep) {
					$this->_variant_add_form->removeField($field->getName());
				}
			}
		}

		return $this->_variant_add_form;
	}

	public function catchAddVariantForm( Product $new_variant ) : bool
	{
		$edit_form = $new_variant->getAddVariantForm();
		if(
			!$edit_form->catchInput() ||
			!$edit_form->validate()
		) {
			return false;
		}

		$edit_form->catchData();

		$this->addVariant( $new_variant );

		return true;
	}

	public function getUpdateVariantsForm() : Form
	{
		if(!$this->_update_variants_form) {
			$fields = [];

			$variant_control_properties = $this->getVariantControlProperties();

			foreach($this->getVariants() as $variant) {

				foreach(Shops::getList() as $shop) {
					$shop_code = $shop->getCode();

					$variant_name = new Form_Field_Input('/'.$variant->getId().'/'.$shop_code.'/variant_name', 'Variant name', $variant->getShopData($shop_code)->getVariantName() );

					$variant_name->setCatcher( function( $value ) use ($variant, $shop_code) {
						$variant->getShopData(  $shop_code )->setVariantName( $value );
					} );

					$fields[] = $variant_name;

				}


				foreach($variant_control_properties as $property) {
					$property_id = $property->getId();


					if(!isset($variant->parametrization_values[$property_id])) {
						$pv = new Product_ParametrizationValue();
						$variant->parametrization_values[$property_id] = $pv;
					} else {
						$pv = $variant->parametrization_values[$property_id];
					}

					$pv->setParents( $variant );
					$pv->setProperty( $property );

					foreach( $pv->getValueEditForm()->getFields() as $field ) {
						$field->setName('/'.$variant->getId().'/'.$property->getId().'/'.$field->getName());
						$fields[] = $field;
					}
				}

			}

			$this->_update_variants_form = new Form('update_variants_form', $fields);
		}

		return $this->_update_variants_form;
	}


	public function catchUpdateVariantsForm(): bool
	{
		$edit_form = $this->getUpdateVariantsForm();
		if(
			!$edit_form->catchInput() ||
			!$edit_form->validate()
		) {
			return false;
		}

		$edit_form->catchData();

		foreach($this->getVariants() as $variant) {
			$variant->save();

			foreach($variant->getCategories() as $c) {
				Category::addSyncCategory( $c->getId() );
			}
		}

		return true;
	}


	public function getVariantIds() : array
	{
		if(!$this->variant_ids) {
			return [];
		} else {
			return explode(',', $this->variant_ids);
		}
	}

	public function setVariantIds( array $ids ) : void
	{
		$this->variant_ids = implode(',', $ids);
		$this->_variants = null;
	}

	/**
	 * @return Product[]
	 */
	public function getVariants() : array
	{
		if($this->_variants===null) {
			$this->_variants=[];
			foreach($this->getVariantIds() as $id) {
				$p = Product::get($id);
				if($p) {
					$this->_variants[$p->getId()] = $p;
				}
			}
		}

		return $this->_variants;
	}

	public function addVariant( Product $variant ) : void
	{

		$variant_ids = $this->getVariantIds();
		if(
			$variant->getId()>0 &&
			in_array($variant->getId(), $variant_ids)
		) {
			return;
		}

		$this->type = Product::PRODUCT_TYPE_VARIANT_MASTER;

		$this->syncVariant( $variant );

		$variant_ids[] = $variant->getId();


		$this->setVariantIds($variant_ids);
	}

	public function syncVariants() : void
	{
		if($this->type==Product::PRODUCT_TYPE_VARIANT_MASTER) {
			foreach($this->getVariants() as $v) {
				$this->syncVariant( $v );
			}
		}
	}

	public function syncVariant( Product $variant ) : void
	{
		$variant->variant_master_product_id = $this->getId();

		$variant->type = Product::PRODUCT_TYPE_VARIANT;

		$variant->brand_id = $this->brand_id;
		$variant->supplier_id = $this->supplier_id;

		foreach( Shops::getList() as $shop ) {
			$shop_code = $shop->getCode();

			$v_sd = $variant->getShopData( $shop_code );
			$sd = $this->getShopData();

			$v_sd->setDateAvailable( $sd->getDateAvailable() );
			$v_sd->setDeliveryTermCode( $sd->getDeliveryTermCode() );
			$v_sd->setDeliveryClassCode( $sd->getDeliveryClassCode() );
		}


		foreach($this->getCategories() as $c) {
			Category::addSyncCategory( $c->getId() );
		}
		foreach($variant->getCategories() as $c) {
			Category::addSyncCategory( $c->getId() );
		}




		if($variant->variant_sync_categories || $variant->getIsNew()) {
			foreach($this->getCategories() as $c) {
				$variant->addCategory($c->getId());
			}

			foreach($variant->getCategories() as $c) {
				if(!$this->hasCategory($c->getId())) {
					$variant->removeCategory( $c->getId() );
				}
			}
		}



		if($this->variant_sync_descriptions || $variant->getIsNew() ) {
			foreach( Shops::getList() as $shop ) {
				$shop_code = $shop->getCode();

				$v_sd = $variant->getShopData( $shop_code );
				$sd = $this->getShopData();

				$v_sd->setName( $sd->getName() );
				$v_sd->setDescription( $sd->getDescription() );
				$v_sd->setShortDescription( $sd->getShortDescription() );
				$v_sd->setSeoTitle( $sd->getSeoTitle() );
				$v_sd->setSeoDescription( $sd->getSeoDescription() );
				$v_sd->setSeoKeywords( $sd->getSeoKeywords() );
				$v_sd->setSeoH1( $sd->getSeoH1() );
				$v_sd->setInternalFulltextKeywords( $sd->getInternalFulltextKeywords() );
			}
		}




		$skip_properties = $this->getVariantControlPropertyIds();

		foreach($this->parametrization_values as $pv) {
			$p_id = $pv->getPropertyId();
			if(in_array($p_id, $skip_properties)) {
				continue;
			}

			if(!isset($variant->parametrization_values[$p_id])) {
				$variant->parametrization_values[$p_id] = new Product_ParametrizationValue();
				$variant->parametrization_values[$p_id]->setPropertyId( $pv->getPropertyId() );
			}

			$variant->parametrization_values[$p_id]->setRawValue( $pv->getRawValue() );
			$variant->parametrization_values[$p_id]->setInformationIsNotAvailable( $pv->isInformationIsNotAvailable() );
		}



		if($this->variant_sync_stickers || $variant->getIsNew() ) {
			foreach($this->getStickers() as $c) {
				$variant->addSticker($c->getId());
			}

			foreach($variant->getStickers() as $c) {
				if(!$this->hasSticker($c->getId())) {
					$variant->removeSticker( $c->getId() );
				}
			}
		}

		if($this->variant_sync_prices || $variant->getIsNew() ) {
			foreach( Shops::getList() as $shop ) {
				$shop_code = $shop->getCode();

				$v_sd = $variant->getShopData( $shop_code );
				$t_sd = $this->getShopData( $shop_code );

				$v_sd->setStandardPrice( $t_sd->getStandardPrice() );
				$v_sd->setActionPrice( $t_sd->getActionPrice() );
				$v_sd->setActionPriceValidFrom( $t_sd->getActionPriceValidFrom() );
				$v_sd->setActionPriceValidTill( $t_sd->getActionPriceValidTill() );

				$v_sd->actualizePrice();
			}
		}


		$variant->save();
	}


	public function afterAdd() : void
	{
		foreach( Shops::getList() as $shop ) {
			$shop_code = $shop->getCode();

			$this->shop_data[$shop_code]->generateURLPathPart();
			$this->shop_data[$shop_code]->save();
		}

		/** @noinspection PhpParamsInspection */
		Fulltext::update_Product_afterAdd( $this );

	}

	public function afterUpdate() : void
	{
		/** @noinspection PhpParamsInspection */
		Fulltext::update_Product_afterUpdate( $this );
	}

	public function afterDelete() : void
	{
		/** @noinspection PhpParamsInspection */
		Fulltext::update_Product_afterDelete( $this );

		foreach($this->getCategories() as $c) {
			Category::addSyncCategory( $c->getId() );
		}

		Category::syncCategories();
	}

	public function actualizePrices() : void
	{
		foreach($this->shop_data as $sd) {
			$sd->actualizePrice();
		}
	}



































	public function getName( string|null $shop_code=null ) : string
	{
		return $this->getShopData($shop_code)->getName();
	}

	public function getDescription( string|null $shop_code=null ): string
	{
		return $this->getShopData($shop_code)->getDescription();
	}

	public function getSeoH1( string|null $shop_code=null ): string
	{
		return $this->getShopData($shop_code)->getSeoH1();
	}

	public function getSeoTitle( string|null $shop_code=null ): string
	{
		return $this->getShopData($shop_code)->getSeoTitle();
	}

	public function getSeoDescription( string|null $shop_code=null ): string
	{
		return $this->getShopData($shop_code)->getSeoDescription();
	}

	public function getShortDescription( string|null $shop_code=null ): string
	{
		return $this->getShopData($shop_code)->getShortDescription();
	}

	public function getFullName( string|null $shop_code=null ): string
	{
		$name = $this->getName( $shop_code );
		$variant_name = $this->getVariantName( $shop_code );
		
		if($variant_name) {
			return $name.' '.$variant_name;
		}
		
		return $name;
	}

	public function getVariantName( string|null $shop_code=null ): string
	{
		return $this->getShopData($shop_code)->getVariantName();
	}

	public function getVatRate( string|null $shop_code=null ): float
	{
		return $this->getShopData($shop_code)->getVatRate();
	}

	public function getStandardPrice( string|null $shop_code=null ): float
	{
		return $this->getShopData($shop_code)->getStandardPrice();
	}

	public function getActionPrice( string|null $shop_code=null ): float
	{
		return $this->getShopData($shop_code)->getActionPrice();
	}

	public function getActionPriceValidFrom( string|null $shop_code=null ): ?Data_DateTime
	{
		return $this->getShopData($shop_code)->getActionPriceValidFrom();
	}

	public function getActionPriceValidTill( string|null $shop_code=null ): ?Data_DateTime
	{
		return $this->getShopData($shop_code)->getActionPriceValidTill();
	}

	public function getSalePrice( string|null $shop_code=null ): float
	{
		return $this->getShopData($shop_code)->getSalePrice();
	}

	public function getFinalPrice( string|null $shop_code=null ): float
	{
		return $this->getShopData($shop_code)->getFinalPrice();
	}

	public function getDiscountPercentage( string|null $shop_code=null ): float
	{
		return $this->getShopData($shop_code)->getDiscountPercentage();
	}

	public function isResetSaleAfterSoldOut( string|null $shop_code=null ): bool
	{
		return $this->getShopData($shop_code)->isResetSaleAfterSoldOut();
	}

	public function isDeactivateProductAfterSoldOut( string|null $shop_code=null ): bool
	{
		return $this->getShopData($shop_code)->isDeactivateProductAfterSoldOut();
	}

	public function getDeliveryTermCode( string|null $shop_code=null ): string
	{
		return $this->getShopData($shop_code)->getDeliveryTermCode();
	}

	public function getDeliveryClassCode( string|null $shop_code=null ): string
	{
		return $this->getShopData($shop_code)->getDeliveryClassCode();
	}

	public function getDeliveryClass( string|null $shop_code=null ) : ?Delivery_Class
	{
		return Delivery_Class::get( $this->getShopData($shop_code)->getDeliveryClassCode() );
	}

	public function getDateAvailable( string|null $shop_code=null ): ?Data_DateTime
	{
		return $this->getShopData($shop_code)->getDateAvailable();
	}

	public function getStockStatus( string|null $shop_code=null ): int
	{
		return $this->getShopData($shop_code)->getStockStatus();
	}

	public function getSeoKeywords( string|null $shop_code=null ): string
	{
		return $this->getShopData($shop_code)->getSeoKeywords();
	}

	public function getInternalFulltextKeywords( string|null $shop_code=null ): float
	{
		return $this->getShopData($shop_code)->getInternalFulltextKeywords();
	}

	public function getURLPathPart( string|null $shop_code=null ): string
	{
		return $this->getShopData($shop_code)->getURLPathPart();
	}

	public function getURL( string|null $shop_code=null ): string
	{
		return $this->getShopData($shop_code)->getURL();
	}

	public function getImage( int $i = 0, string|null $shop_code=null  ): string
	{
		return $this->getShopData($shop_code)->getImage( $i );
	}

	public function getImageUrl( int $i = 0, string|null $shop_code=null  ): string
	{
		return $this->getShopData($shop_code)->getImageUrl( $i );
	}

	public function getImageThumbnailUrl( int $max_w, int $max_h, int $i=0, string|null $shop_code=null  ) : string
	{
		return $this->getShopData($shop_code)->getImageThumbnailUrl( $max_w, $max_h, $i );
	}

	public static function renderSelectProductWidget( string $on_select,
	                                                   int $selected_product_id=0,
	                                                   array $filter = [],
	                                                   bool $only_active=false,
	                                                   string $name='select_product' ) : string
	{
		$view = new Mvc_View( Mvc::getCurrentSite()->getViewsPath() );

		$view->setVar('selected_product_id', $selected_product_id);
		$view->setVar('filter', $filter);
		$view->setVar('on_select', $on_select);
		$view->setVar('name', $name);
		$view->setVar('only_active', $only_active);

		return $view->render('selectProductWidget');
	}


	public function getAdminTitle( ?string $shop_code=null ) : string
	{
		$sd = $this->getShopData($shop_code);

		$codes = [];
		if($this->getInternalCode()) {
			$codes[] = $this->getInternalCode();
		}
		if($this->getEan()) {
			$codes[] = $this->getEan();
		}

		if($codes) {
			$codes = ' ('.implode(', ', $codes).')';
		} else {
			$codes = '';
		}

		return $sd->getName().$codes;
	}
}