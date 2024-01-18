<?php
namespace JetShop;

use Jet\DataModel;
use Jet\DataModel_Definition;
use Jet\Form_Definition;
use Jet\Form_Field;
use Jet\DataModel_IDController_AutoIncrement;

use Jet\Form_Field_Select;
use JetApplication\Category;
use JetApplication\Delivery_Class;
use JetApplication\Entity_WithShopData;
use JetApplication\KindOfProduct;

use JetApplication\Product;
use JetApplication\Product_ShopData;
use JetApplication\Product_Trait_Images;
use JetApplication\Product_Trait_Set;
use JetApplication\Product_Trait_Variants;
use JetApplication\Product_Trait_Categories;
use JetApplication\Product_Trait_Parameters;
use JetApplication\Product_Trait_Stickers;
use JetApplication\Shops;
use JetApplication\Shops_Shop;
use JetApplication\Brand;
use JetApplication\Supplier;


#[DataModel_Definition(
	name: 'product',
	database_table_name: 'products',
	id_controller_class: DataModel_IDController_AutoIncrement::class,
	id_controller_options: ['id_property_name'=>'id']

)]
abstract class Core_Product extends Entity_WithShopData {
	
	//TODO: add product weight
	
	use Product_Trait_Images;
	use Product_Trait_Set;
	use Product_Trait_Variants;
	use Product_Trait_Categories;
	use Product_Trait_Parameters;
	use Product_Trait_Stickers;

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
		Category::productActivated( product_id: $this->id );
	}
	
	public function deactivate(): void
	{
		parent::deactivate();
		Category::productDeactivated( product_id: $this->id );
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
		return $this->shop_data[$shop ? $shop->getKey() : Shops::getCurrent()->getKey()];
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
}