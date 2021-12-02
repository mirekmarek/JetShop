<?php
namespace JetShop;

use Jet\Application_Modules;
use Jet\DataModel;
use Jet\Data_DateTime;
use Jet\DataModel_Definition;
use Jet\Form;
use Jet\DataModel_Fetch_Instances;
use Jet\Form_Field_Date;
use Jet\Form_Field_Select;
use Jet\DataModel_IDController_AutoIncrement;
use Jet\Application_Module;
use Jet\MVC;
use Jet\MVC_View;
use Jet\Tr;

#[DataModel_Definition(
	name: 'products',
	database_table_name: 'products',
	id_controller_class: DataModel_IDController_AutoIncrement::class,
	id_controller_options: ['id_property_name'=>'id']

)]
abstract class Core_Product extends DataModel {
	use Product_Trait_Set;
	use Product_Trait_Variants;
	use Product_Trait_Categories;
	use Product_Trait_Parametrization;
	use Product_Trait_Stickers;

	protected static string $manage_module_name = 'Admin.Catalog.Products';
	
	const PRODUCT_TYPE_REGULAR        = 'regular';
	const PRODUCT_TYPE_VARIANT_MASTER = 'variant_master';
	const PRODUCT_TYPE_VARIANT        = 'variant';
	const PRODUCT_TYPE_SET            = 'set';

	const SET_STRATEGY_CALCULATED     = 'calculated';
	const SET_STRATEGY_FIXED          = 'fixed';
	const SET_STRATEGY_DISCOUNT       = 'discount';


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
	protected array $shop_data = [];



	protected ?Form $_add_form = null;

	protected ?Form $_edit_form = null;

	/**
	 * @var Product[]
	 */
	protected static array $loaded_items = [];


	public static function getProductTypes() : array
	{
		return [
			Product::PRODUCT_TYPE_REGULAR        => Tr::_('Regular', [], Product::getManageModuleName()),
			Product::PRODUCT_TYPE_VARIANT_MASTER => Tr::_('Variant master', [], Product::getManageModuleName()),
			Product::PRODUCT_TYPE_VARIANT        => Tr::_('Variant', [], Product::getManageModuleName()),
			Product::PRODUCT_TYPE_SET            => Tr::_('Set', [], Product::getManageModuleName()),
		];

	}

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
		Product_ShopData::checkShopData( $this, $this->shop_data );
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


	public function getInternalName( ?Shops_Shop $shop=null ) : string
	{
		return $this->getAdminTitle( $shop );
	}


	public function getShopData( ?Shops_Shop $shop=null ) : Product_ShopData
	{
		return $this->shop_data[$shop ? $shop->getKey() : Shops::getCurrent()->getKey()];
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

		$this->_setupForm_stickers( $form );
		$this->_setupForm_set( $form );
		$this->_setupForm_variants( $form );



		foreach(Shops::getList() as $shop) {
			$shop_key = $shop->getKey();

			$vat_rate = new Form_Field_Select('/shop_data/'.$shop_key.'/vat_rate', 'VAT rate:', $this->getShopData($shop)->getVatRate() );

			$vat_rate->setErrorMessages([
				Form_Field_Select::ERROR_CODE_INVALID_VALUE => 'Invalid date'
			]);
			$vat_rate->setCatcher(function( $value ) use ($shop) {
				$this->getShopData($shop)->setVatRate( $value );
			});
			$vat_rate->setSelectOptions( Shops::getVatRatesScope( $shop ) );
			$vat_rate->setIsReadonly( $form->field('/shop_data/'.$shop_key.'/vat_rate')->getIsReadonly() );


			$form->removeField('/shop_data/'.$shop_key.'/vat_rate');
			$form->addField( $vat_rate );


			$form->field('/shop_data/'.$shop_key.'/date_available')->setErrorMessages([
				Form_Field_Date::ERROR_CODE_INVALID_FORMAT => 'Invalid date'
			]);

			$form->field('/shop_data/'.$shop_key.'/action_price_valid_from')->setErrorMessages([
				Form_Field_Date::ERROR_CODE_INVALID_FORMAT => 'Invalid date'
			]);

			$form->field('/shop_data/'.$shop_key.'/action_price_valid_till')->setErrorMessages([
				Form_Field_Date::ERROR_CODE_INVALID_FORMAT => 'Invalid date'
			]);

			$form->field('/shop_data/'.$shop_key.'/delivery_class_code')->setErrorMessages([
				Form_Field_Select::ERROR_CODE_INVALID_VALUE => 'Please select value'
			]);

			$form->field('/shop_data/'.$shop_key.'/delivery_term_code')->setErrorMessages([
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
				$this->_add_form->field( '/shop_data/' . $shop->getKey() . '/vat_rate' )->setDefaultValue( Shops::getDefaultVatRate( $shop ) );
			}
		}

		return $this->_add_form;
	}

	public function catchAddForm() : bool
	{
		$add_form = $this->getAddForm();
		if( !$add_form->catch() ) {
			return false;
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

		}

		return $this->_edit_form;
	}

	public function catchEditForm() : bool
	{
		$edit_form = $this->getEditForm();
		if( !$edit_form->catch() ) {
			return false;
		}

		$this->actualizePrices();

		return true;
	}










	public function afterAdd() : void
	{
		foreach( Shops::getList() as $shop ) {
			$shop_key = $shop->getKey();

			$this->shop_data[$shop_key]->generateURLPathPart();
			$this->shop_data[$shop_key]->save();
		}

		Fulltext::update_Product_afterAdd( $this );

	}

	public function afterUpdate() : void
	{
		/**
		 * @var Product $this
		 */
		$this->actualizeSetItem();
		$this->actualizeVariant();

		Fulltext::update_Product_afterUpdate( $this );
	}

	public function afterDelete() : void
	{
		/**
		 * @var Product $this
		 */
		$this->actualizeSetItem();
		$this->actualizeVariant();

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


	public function getName( ?Shops_Shop $shop=null ) : string
	{
		return $this->getShopData($shop)->getName();
	}

	public function getDescription( ?Shops_Shop $shop=null ): string
	{
		return $this->getShopData($shop)->getDescription();
	}

	public function getSeoH1( ?Shops_Shop $shop=null ): string
	{
		return $this->getShopData($shop)->getSeoH1();
	}

	public function getSeoTitle( ?Shops_Shop $shop=null ): string
	{
		return $this->getShopData($shop)->getSeoTitle();
	}

	public function getSeoDescription( ?Shops_Shop $shop=null ): string
	{
		return $this->getShopData($shop)->getSeoDescription();
	}

	public function getShortDescription( ?Shops_Shop $shop=null ): string
	{
		return $this->getShopData($shop)->getShortDescription();
	}

	public function getFullName( ?Shops_Shop $shop=null ): string
	{
		$name = $this->getName( $shop );
		$variant_name = $this->getVariantName( $shop );
		
		if($variant_name) {
			return $name.' '.$variant_name;
		}
		
		return $name;
	}

	public function getVariantName( ?Shops_Shop $shop=null ): string
	{
		return $this->getShopData($shop)->getVariantName();
	}

	public function getVatRate( ?Shops_Shop $shop=null ): float
	{
		return $this->getShopData($shop)->getVatRate();
	}

	public function getStandardPrice( ?Shops_Shop $shop=null ): float
	{
		return $this->getShopData($shop)->getStandardPrice();
	}

	public function getActionPrice( ?Shops_Shop $shop=null ): float
	{
		return $this->getShopData($shop)->getActionPrice();
	}

	public function getActionPriceValidFrom( ?Shops_Shop $shop=null ): ?Data_DateTime
	{
		return $this->getShopData($shop)->getActionPriceValidFrom();
	}

	public function getActionPriceValidTill( ?Shops_Shop $shop=null ): ?Data_DateTime
	{
		return $this->getShopData($shop)->getActionPriceValidTill();
	}

	public function getSalePrice( ?Shops_Shop $shop=null ): float
	{
		return $this->getShopData($shop)->getSalePrice();
	}

	public function getFinalPrice( ?Shops_Shop $shop=null ): float
	{
		return $this->getShopData($shop)->getFinalPrice();
	}

	public function getDiscountPercentage( ?Shops_Shop $shop=null ): float
	{
		return $this->getShopData($shop)->getDiscountPercentage();
	}

	public function isResetSaleAfterSoldOut( ?Shops_Shop $shop=null ): bool
	{
		return $this->getShopData($shop)->isResetSaleAfterSoldOut();
	}

	public function isDeactivateProductAfterSoldOut( ?Shops_Shop $shop=null ): bool
	{
		return $this->getShopData($shop)->isDeactivateProductAfterSoldOut();
	}

	public function getDeliveryTermCode( ?Shops_Shop $shop=null ): string
	{
		return $this->getShopData($shop)->getDeliveryTermCode();
	}

	public function getDeliveryClassCode( ?Shops_Shop $shop=null ): string
	{
		return $this->getShopData($shop)->getDeliveryClassCode();
	}

	public function getDeliveryClass( ?Shops_Shop $shop=null ) : ?Delivery_Class
	{
		return Delivery_Class::get( $this->getShopData($shop)->getDeliveryClassCode() );
	}

	public function getDateAvailable( ?Shops_Shop $shop=null ): ?Data_DateTime
	{
		return $this->getShopData($shop)->getDateAvailable();
	}

	public function getStockStatus( ?Shops_Shop $shop=null ): int
	{
		return $this->getShopData($shop)->getStockStatus();
	}

	public function getSeoKeywords( ?Shops_Shop $shop=null ): string
	{
		return $this->getShopData($shop)->getSeoKeywords();
	}

	public function getInternalFulltextKeywords( ?Shops_Shop $shop=null ): float
	{
		return $this->getShopData($shop)->getInternalFulltextKeywords();
	}

	public function getURLPathPart( ?Shops_Shop $shop=null ): string
	{
		return $this->getShopData($shop)->getURLPathPart();
	}

	public function getURL( ?Shops_Shop $shop=null ): string
	{
		return $this->getShopData($shop)->getURL();
	}

	public function getImage( int $i = 0, ?Shops_Shop $shop=null  ): string
	{
		return $this->getShopData($shop)->getImage( $i );
	}

	public function getImageUrl( int $i = 0, ?Shops_Shop $shop=null  ): string
	{
		return $this->getShopData($shop)->getImageUrl( $i );
	}

	public function getImageThumbnailUrl( int $max_w, int $max_h, int $i=0, ?Shops_Shop $shop=null  ) : string
	{
		return $this->getShopData($shop)->getImageThumbnailUrl( $max_w, $max_h, $i );
	}

	public static function renderSelectProductWidget( string $on_select,
	                                                   int $selected_product_id=0,
	                                                   array $filter = [],
	                                                   bool $only_active=false,
	                                                   string $name='select_product' ) : string
	{
		$view = new MVC_View( MVC::getBase()->getViewsPath() );

		$view->setVar('selected_product_id', $selected_product_id);
		$view->setVar('filter', $filter);
		$view->setVar('on_select', $on_select);
		$view->setVar('name', $name);
		$view->setVar('only_active', $only_active);

		return $view->render('selectProductWidget');
	}

	public function renderActiveState() : string
	{
		$view = new MVC_View( Product::getManageModule()->getViewsDir() );

		$view->setVar('product', $this );

		return $view->render('active_state');
	}


	public function getAdminTitle( ?Shops_Shop $shop=null ) : string
	{
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

		return $this->getFullName($shop).$codes;
	}


}