<?php
namespace JetShop;

use Jet\DataModel;
use Jet\DataModel_Definition;
use Jet\Form_Definition;
use Jet\Form_Field;
use Jet\DataModel_IDController_AutoIncrement;

use Jet\Form_Field_Select;
use JetApplication\Category;
use JetApplication\Entity_WithIDAndShopData;
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
abstract class Core_Product extends Entity_WithIDAndShopData {
	
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
	#[Form_Definition(
		type: Form_Field::TYPE_INPUT,
		label: 'Internal code:'
	)]
	protected string $internal_code = '';

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
		select_options_creator: [Brand::class,'getScope'],
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
		select_options_creator: [Supplier::class,'getScope'],
		error_messages: [
			Form_Field_Select::ERROR_CODE_INVALID_VALUE => 'Invalid value'
		]
	)]
	protected int $supplier_id = 0;
	
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
	
	public static function getProductType( int $product_id ) : ?string
	{
		$product_type = Product::dataFetchOne(select:['type'], where:['id'=>$product_id]);
		
		return $product_type?:null;
	}

	public function setType( string $type ) : void
	{
		$this->type = $type;
	}
	
	public function getKindId(): int
	{
		return $this->kind_id;
	}
	
	public function setKindId( int $kind_id ): void
	{
		$this->kind_id = $kind_id;
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

	public static function getIdsByKind( KindOfProduct $kind ) : array
	{
		$_ids = Product::dataFetchCol(['id'], ['kind_id'=>$kind->getId()]);
		$ids = [];
		
		foreach($_ids as $id) {
			$ids[] = (int)$id;
		}

		return $ids;
	}
}