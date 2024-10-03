<?php
namespace JetShop;

use Jet\DataModel;
use Jet\DataModel_Definition;
use Jet\Form_Definition;
use Jet\Form_Field;
use Jet\DataModel_IDController_AutoIncrement;
use Jet\DataModel_Query;

use Jet\Form_Field_Select;
use JetApplication\Admin_Managers;
use JetApplication\Availabilities;
use JetApplication\Category;
use JetApplication\Delivery_Class;
use JetApplication\Entity_HasPrice_Interface;
use JetApplication\Entity_WithShopData;
use JetApplication\FulltextSearch_IndexDataProvider;
use JetApplication\KindOfProduct;

use JetApplication\Managers;
use JetApplication\MeasureUnit;
use JetApplication\Pricelists;
use JetApplication\Product;
use JetApplication\Product_Availability;
use JetApplication\Product_Price;
use JetApplication\Product_ShopData;
use JetApplication\Product_Trait_Availability;
use JetApplication\Product_Trait_Images;
use JetApplication\Product_Trait_Files;
use JetApplication\Product_Trait_Price;
use JetApplication\Product_Trait_Set;
use JetApplication\Product_Trait_Variants;
use JetApplication\Product_Trait_Categories;
use JetApplication\Product_Trait_Parameters;
use JetApplication\Product_Trait_Stickers;
use JetApplication\Product_Trait_Similar;
use JetApplication\Product_Trait_Boxes;
use JetApplication\Product_VirtualProductHandler;
use JetApplication\Shop_Managers;
use JetApplication\Shops;
use JetApplication\Shops_Shop;
use JetApplication\Brand;
use JetApplication\Supplier;


#[DataModel_Definition(
	name: 'product',
	database_table_name: 'products',
	id_controller_class: DataModel_IDController_AutoIncrement::class,
	id_controller_options: ['id_property_name'=>'id'],
	relation: [
		'related_to_class_name' => Product_Price::class,
		'join_by_properties' => [
			'id' => 'product_id'
		],
		'join_type' => DataModel_Query::JOIN_TYPE_LEFT_JOIN
	]

)]
abstract class Core_Product extends Entity_WithShopData implements FulltextSearch_IndexDataProvider, Entity_HasPrice_Interface {
	
	use Product_Trait_Availability;
	use Product_Trait_Price;
	use Product_Trait_Images;
	use Product_Trait_Files;
	use Product_Trait_Set;
	use Product_Trait_Variants;
	use Product_Trait_Categories;
	use Product_Trait_Parameters;
	use Product_Trait_Stickers;
	use Product_Trait_Similar;
	use Product_Trait_Boxes;

	public const PRODUCT_TYPE_REGULAR        = 'regular';
	public const PRODUCT_TYPE_VARIANT_MASTER = 'variant_master';
	public const PRODUCT_TYPE_VARIANT        = 'variant';
	public const PRODUCT_TYPE_SET            = 'set';
	
	public const SET_DISCOUNT_NONE    = '';
	public const SET_DISCOUNT_NOMINAL = 'nominal';
	public const SET_DISCOUNT_PERCENT = 'percent';
	
	
	#[DataModel_Definition(
		type: DataModel::TYPE_STRING,
		max_len: 100,
		is_key: true,
	)]
	protected string $type = Product::PRODUCT_TYPE_REGULAR;
	
	#[DataModel_Definition(
		type: DataModel::TYPE_INT,
		is_key: true
	)]
	protected int $kind_id = 0;

	#[DataModel_Definition(
		type: DataModel::TYPE_STRING,
		max_len: 100,
		is_key: true,
	)]
	#[Form_Definition(
		type: Form_Field::TYPE_INPUT,
		label: 'EAN:'
	)]
	protected string $ean = '';

	#[DataModel_Definition(
		type: DataModel::TYPE_STRING,
		max_len: 100,
		is_key: true,
	)]
	protected string $erp_id = '';

	#[DataModel_Definition(
		type: DataModel::TYPE_INT,
		is_key: true,
	)]
	#[Form_Definition(
		type: Form_Field::TYPE_SELECT,
		label: 'Brand:',
		select_options_creator: [Brand::class,'getOptionsScope'],
		error_messages: [
			Form_Field_Select::ERROR_CODE_INVALID_VALUE => 'Invalid value'
		]
	)]
	protected int $brand_id = 0;

	#[DataModel_Definition(
		type: DataModel::TYPE_INT,
		is_key: true,
	)]
	#[Form_Definition(
		type: Form_Field::TYPE_SELECT,
		label: 'Supplier:',
		select_options_creator: [Supplier::class,'getOptionsScope'],
		error_messages: [
			Form_Field_Select::ERROR_CODE_INVALID_VALUE => 'Invalid value'
		]
	)]
	protected int $supplier_id = 0;
	
	#[DataModel_Definition(
		type: DataModel::TYPE_STRING,
		max_len: 50,
		is_key: true,
	)]
	#[Form_Definition(
		type: Form_Field::TYPE_INPUT,
		label: 'Supplier code:',
	)]
	protected string $supplier_code = '';
	
	#[DataModel_Definition(
		type: DataModel::TYPE_INT,
		is_key: true,
	)]
	#[Form_Definition(
		type: Form_Field::TYPE_SELECT,
		label: 'Delivery class:',
		select_options_creator: [Delivery_Class::class,'getOptionsScope'],
		error_messages: [
			Form_Field_Select::ERROR_CODE_INVALID_VALUE => 'Invalid value'
		]
	)]
	protected int $delivery_class_id = 0;
	
	
	/**
	 * @var Product_ShopData[]
	 */
	#[DataModel_Definition(
		type: DataModel::TYPE_DATA_MODEL,
		data_model_class: Product_ShopData::class
	)]
	#[Form_Definition(is_sub_forms: true)]
	protected array $shop_data = [];
	
	public function activate(): void
	{
		parent::activate();
		$id = $this->isVariant() ? $this->getVariantMasterProductId() : $this->getId();
		Product::actualizeReferences( product_id: $id );
	}
	
	public function deactivate(): void
	{
		parent::deactivate();
		$id = $this->isVariant() ? $this->getVariantMasterProductId() : $this->getId();
		Product::actualizeReferences( product_id: $id );
	}
	
	public function delete(): void
	{
		parent::delete();
		Category::productDeleted( $this->id );
	}
	
	public function getType() : string
	{
		return $this->type;
	}
	
	public function isVirtual() : bool
	{
		return (bool)$this->getKind()?->getIsVirtualProduct();
	}
	
	public function isSet() : bool
	{
		return $this->type==Product::PRODUCT_TYPE_SET;
	}
	
	public function isVariant() : bool
	{
		return $this->type==Product::PRODUCT_TYPE_VARIANT;
	}
	
	public function isVariantMaster() : bool
	{
		return $this->type==Product::PRODUCT_TYPE_VARIANT_MASTER;
	}
	
	public function isRegular() : bool
	{
		return $this->type==Product::PRODUCT_TYPE_REGULAR;
	}
	
	public function isPhysicalProduct() : bool
	{
		if(
			$this->isVirtual() ||
			$this->isVariantMaster() ||
			$this->isSet()
		) {
			return false;
		}
		
		return true;
	}
	
	public static function getProductType( int $product_id ) : ?string
	{
		$product_type = static::dataFetchOne(select:['type'], where:['id'=>$product_id]);
		
		return $product_type?:null;
	}

	public function setType( string $type ) : void
	{
		$this->type = $type;
		
		foreach(Shops::getList() as $shop) {
			$this->shop_data[$shop->getKey()]->setType( $this->type );
		}
	}
	
	public function getKindId(): int
	{
		return $this->kind_id;
	}
	
	public function getKind() : ?KindOfProduct
	{
		return KindOfProduct::get( $this->kind_id );
	}
	
	public function setKindId( int $kind_id ): void
	{
		$this->kind_id = $kind_id;
		
		foreach(Shops::getList() as $shop) {
			$this->shop_data[$shop->getKey()]->setKindId( $this->kind_id );
		}
		
	}
	
	public function getDeliveryClassId(): int
	{
		return $this->delivery_class_id;
	}
	
	public function setDeliveryClassId( int $delivery_class_id ): void
	{
		$this->delivery_class_id = $delivery_class_id;
		
		foreach(Shops::getList() as $shop) {
			$this->shop_data[$shop->getKey()]->setDeliveryClassId( $this->delivery_class_id );
		}
	}
	
	

	public function getEan() : string
	{
		return $this->ean;
	}

	public function setEan( string $ean ) : void
	{
		$this->ean = $ean;
		
		foreach(Shops::getList() as $shop) {
			$this->shop_data[$shop->getKey()]->setEan( $this->ean );
		}
		
	}

	public function getErpId() : string
	{
		return $this->erp_id;
	}

	public function setErpId( string $erp_id ) : void
	{
		$this->erp_id = $erp_id;
		
		foreach(Shops::getList() as $shop) {
			$this->shop_data[$shop->getKey()]->setErpId( $this->erp_id );
		}
	}

	public function getBrandId() : int
	{
		return $this->brand_id;
	}

	public function setBrandId( int $brand_id ) : void
	{
		$this->brand_id = $brand_id;
		foreach(Shops::getList() as $shop) {
			$this->shop_data[$shop->getKey()]->setBrandId( $this->brand_id );
		}
	}

	public function getSupplierId() : int
	{
		return $this->supplier_id;
	}

	public function setSupplierId( int $supplier_id ) : void
	{
		$this->supplier_id = $supplier_id;
		foreach(Shops::getList() as $shop) {
			$this->shop_data[$shop->getKey()]->setSupplierId( $this->supplier_id );
		}
	}
	
	public function getSupplierCode(): string
	{
		return $this->supplier_code;
	}
	
	public function setSupplierCode( string $supplier_code ): void
	{
		$this->supplier_code = $supplier_code;
		foreach(Shops::getList() as $shop) {
			$this->shop_data[$shop->getKey()]->setSupplierCode( $this->supplier_id );
		}
	}
	
	


	public function getInternalName() : string
	{
		return $this->internal_name;
	}

	public function setInternalName( string $internal_name ): void
	{
		$this->internal_name = $internal_name;
	}
	
	
	public function getShopData( ?Shops_Shop $shop=null ) : Product_ShopData
	{
		/** @noinspection PhpIncompatibleReturnTypeInspection */
		return $this->_getShopData( $shop );
	}
	
	
	public static function getIdsByKind( KindOfProduct $kind ) : array
	{
		$_ids = static::dataFetchCol(['id'], ['kind_id'=>$kind->getId()]);
		$ids = [];
		
		foreach($_ids as $id) {
			$ids[] = (int)$id;
		}

		return $ids;
	}
	
	public function getAdminTitle() : string
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
		
		$internal_name = $this->internal_name;
		
		if($this->type==static::PRODUCT_TYPE_VARIANT) {
			$internal_name.= ' / '.$this->getInternalNameOfVariant();
		}
		
		if($this->internal_name_of_variant) {
			$internal_name .= ' - '.$this->internal_name_of_variant;
		}
		
		return $internal_name.$codes;
	}
	
	public function afterAdd(): void
	{
		foreach( Shops::getList() as $shop ) {
			$shop_key = $shop->getKey();
			
			$this->shop_data[$shop_key]->generateURLPathPart();
			$this->shop_data[$shop_key]->save();
		}
		
		parent::afterAdd();
	}
	
	public function afterUpdate(): void
	{
		switch($this->getType()) {
			case Product::PRODUCT_TYPE_REGULAR:         $this->actualizeSetItem(); break;
			case Product::PRODUCT_TYPE_VARIANT_MASTER:  $this->actualizeVariantMaster(); break;
			case Product::PRODUCT_TYPE_SET:             $this->actualizeSet(); break;
		}
		
		parent::afterUpdate();
	}
	
	
	public function getFulltextObjectType(): string
	{
		return $this->getType();
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
		return [$this->internal_name, $this->internal_code, $this->ean, $this->internal_name_of_variant];
	}
	
	public function getShopFulltextTexts( Shops_Shop $shop ) : array
	{
		$shop_data = $this->getShopData( $shop );
		if(
			!$shop_data->isActiveForShop() ||
			$shop_data->isVariant()
		) {
			return [];
		}
		
		$texts = [];
		$texts[] = $shop_data->getName();
		$texts[] = $shop_data->getInternalCode();
		$texts[] = $shop_data->getEan();
		
		return $texts;
	}
	
	public function updateFulltextSearchIndex() : void
	{
		Admin_Managers::FulltextSearch()->updateIndex( $this );
		Shop_Managers::FulltextSearch()->updateIndex( $this );
	}
	
	public function removeFulltextSearchIndex() : void
	{
		Admin_Managers::FulltextSearch()->deleteIndex( $this );
		Shop_Managers::FulltextSearch()->deleteIndex( $this );
	}
	
	public static function updateReviews( int $product_id, int $count, int $rank ) : void
	{
		Product_ShopData::updateData(
			data: [
				'review_count' => $count,
				'review_rank' => $rank
			],
			where: [
				'entity_id' => $product_id
			]
		);
	}
	
	public static function updateQuestions( int $product_id, int $count ) : void
	{
		Product_ShopData::updateData(
			data: [
				'question_count' => $count,
			],
			where: [
				'entity_id' => $product_id
			]
		);
	}
	
	public static function actualizeReferences( int $product_id ) : void
	{
		foreach(Availabilities::getList() as $availability) {
			Product_Availability::get( $availability, $product_id )->actualizeReferences();
		}
		
		foreach(Pricelists::getList() as $pricelist) {
			Product_Price::get( $pricelist, $product_id )->actualizeReferences();
		}
		
		Category::actualizeProductAssoc( product_id: $product_id );
	}
	
	public static function getProductMeasureUnit( int $product_id ) : ?MeasureUnit
	{
		$kind_id = static::dataFetchOne(['kind_id'], where: ['id'=>$product_id]);
		if(!$kind_id) {
			return null;
		}
		
		$measure_unit = KindOfProduct::dataFetchOne( ['measure_unit'], where: ['id'=>$kind_id] );
		if(!$measure_unit) {
			return null;
		}
		
		return MeasureUnit::get( $measure_unit );
	}
	
	
	/**
	 * @return Product_VirtualProductHandler[]
	 */
	public static function getVirtualProductHandlers() : array
	{
		return Managers::findManagers(Product_VirtualProductHandler::class, 'VirtualProductHandler.');
	}
	
	public static function getVirtualProductHandlersScope() : array
	{
		$scope = [];
		
		foreach(static::getVirtualProductHandlers() as $module) {
			$manifest = $module->getModuleManifest();
			
			$scope[$manifest->getName()] = $manifest->getLabel().' ('.$manifest->getName().')';
		}
		
		return $scope;
	}
	
	public static function getVirtualProductHandlersOptionsScope() : array
	{
		return [''=>'']+static::getVirtualProductHandlersScope();
	}
	
}