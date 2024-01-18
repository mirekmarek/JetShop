<?php
namespace JetShop;

use Jet\Data_DateTime;
use Jet\DataModel;
use Jet\DataModel_Definition;
use Jet\Form_Definition;
use Jet\Form_Field;
use Jet\Form_Field_Date;
use JetApplication\Category;
use JetApplication\Category_Product;
use JetApplication\Category_ShopData;
use JetApplication\Entity_WithShopData_ShopData;
use JetApplication\Product;
use JetApplication\Product_PriceHistory;
use JetApplication\Product_ShopData_Trait_Set;
use JetApplication\Product_ShopData_Trait_Variants;
use JetApplication\Product_ShopData_Trait_Images;

#[DataModel_Definition(
	name: 'products_shop_data',
	database_table_name: 'products_shop_data',
	parent_model_class: Product::class
)]
abstract class Core_Product_ShopData extends Entity_WithShopData_ShopData {
	use Product_ShopData_Trait_Set;
	use Product_ShopData_Trait_Variants;
	use Product_ShopData_Trait_Images;
	
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
	protected int $brand_id = 0;
	
	#[DataModel_Definition(
		type: DataModel::TYPE_INT,
		is_key: true,
	)]
	protected int $supplier_id = 0;
	
	#[DataModel_Definition(
		type: DataModel::TYPE_INT,
		is_key: true,
	)]
	protected int $delivery_class_id = 0;
	
	
	#[DataModel_Definition(
		type: DataModel::TYPE_STRING,
		max_len: 100,
	)]
	#[Form_Definition(
		type: Form_Field::TYPE_INPUT,
		label: 'Name:'
	)]
	protected string $name = '';

	#[DataModel_Definition(
		type: DataModel::TYPE_STRING,
		max_len: 65536,
	)]
	#[Form_Definition(
		type: Form_Field::TYPE_WYSIWYG,
		label: 'Short description:'
	)]
	protected string $short_description = '';

	#[DataModel_Definition(
		type: DataModel::TYPE_STRING,
		max_len: 65536,
	)]
	#[Form_Definition(
		type: Form_Field::TYPE_WYSIWYG,
		label: 'Description:'
	)]
	protected string $description = '';

	#[DataModel_Definition(
		type: DataModel::TYPE_STRING,
		max_len: 255,
	)]
	#[Form_Definition(
		type: Form_Field::TYPE_INPUT,
		label: 'Title:'
	)]
	protected string $seo_title = '';

	#[DataModel_Definition(
		type: DataModel::TYPE_STRING,
		max_len: 65536,
	)]
	#[Form_Definition(
		type: Form_Field::TYPE_TEXTAREA,
		label: 'Description:'
	)]
	protected string $seo_description = '';

	#[DataModel_Definition(
		type: DataModel::TYPE_STRING,
		max_len: 65536,
	)]
	#[Form_Definition(
		type: Form_Field::TYPE_TEXTAREA,
		label: 'Keywords:'
	)]
	protected string $seo_keywords = '';

	#[DataModel_Definition(
		type: DataModel::TYPE_STRING,
		max_len: 512,
	)]
	protected string $URL_path_part = '';

	
	#[DataModel_Definition(
		type: DataModel::TYPE_FLOAT,
	)]
	#[Form_Definition(
		type: Form_Field::TYPE_FLOAT,
		label: 'Tax rate:'
	)]
	protected float $vat_rate = 0.0;

	#[DataModel_Definition(
		type: DataModel::TYPE_FLOAT,
	)]
	#[Form_Definition(
		type: Form_Field::TYPE_FLOAT,
		label: 'Standard price:',
	)]
	protected float $standard_price = 0.0;

	#[DataModel_Definition(
		type: DataModel::TYPE_FLOAT,
	)]
	#[Form_Definition(
		type: Form_Field::TYPE_FLOAT,
		label: 'Price:',
	)]
	protected float $price = 0.0;

	#[DataModel_Definition(
		type: DataModel::TYPE_FLOAT,
	)]
	protected float $discount_percentage = 0.0;

	#[DataModel_Definition(
		type: DataModel::TYPE_INT,
	)]
	#[Form_Definition(
		type: Form_Field::TYPE_INT,
		label: 'Length of delivery:',
	)]
	protected int $length_of_delivery = 0;

	#[DataModel_Definition(
		type: DataModel::TYPE_DATE,
	)]
	#[Form_Definition(
		type: Form_Field::TYPE_DATE,
		label: 'Available from:',
		error_messages: [
			Form_Field_Date::ERROR_CODE_INVALID_FORMAT => 'Invalid date'
		]
	)]
	protected Data_DateTime|null $available_from = null;

	#[DataModel_Definition(
		type: DataModel::TYPE_INT,
		is_key: true,
	)]
	#[Form_Definition(
		type: Form_Field::TYPE_INT,
		label: 'In stock quantity:',
	)]
	protected int $in_stock_qty = 0;

	#[DataModel_Definition(
		type: DataModel::TYPE_INT,
	)]
	protected int $review_count = 0;

	#[DataModel_Definition(
		type: DataModel::TYPE_INT,
	)]
	protected int $review_rank = 0;

	#[DataModel_Definition(
		type: DataModel::TYPE_INT,
	)]
	protected int $question_count = 0;
	
	
	protected ?array $category_ids = null;
	
	protected ?array $categories = null;
	
	
	public function activate(): void
	{
		parent::activate();
		if($this->isVariant()) {
			$master = Product::load( $this->getVariantMasterProductId() );
			$master?->actualizeVariantMaster();
			
			Category::productActivated( product_id: $this->getVariantMasterProductId() );
		} else {
			Category::productActivated( product_id: $this->entity_id );
		}
	}
	
	public function deactivate(): void
	{
		parent::deactivate();
		if($this->isVariant()) {
			$master = Product::load( $this->getVariantMasterProductId() );
			$master?->actualizeVariantMaster();
			
			Category::productActivated( product_id: $this->getVariantMasterProductId() );
		} else {
			Category::productDeactivated( product_id: $this->entity_id );
		}
	}
	
	
	public function getName() : string
	{
		return $this->name;
	}
	
	public function getFullName() : string
	{
		$name = $this->name;
		
		if($this->type==Product::PRODUCT_TYPE_VARIANT) {
			$name .= ' / '.$this->variant_name;
		}
		
		return $name;
	}

	public function setName( string $name ) : void
	{
		if($this->name==$name) {
			return;
		}
		
		$this->name = $name;
		$this->generateURLPathPart();
	}
	
	public function getType(): string
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
	
	public function setType( string $type ): void
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
	
	public function getEan(): string
	{
		return $this->ean;
	}
	
	public function setEan( string $ean ): void
	{
		$this->ean = $ean;
	}
	
	public function getErpId(): string
	{
		return $this->erp_id;
	}
	
	public function setErpId( string $erp_id ): void
	{
		$this->erp_id = $erp_id;
	}
	
	public function getBrandId(): int
	{
		return $this->brand_id;
	}
	
	public function setBrandId( int $brand_id ): void
	{
		$this->brand_id = $brand_id;
	}
	
	public function getSupplierId(): int
	{
		return $this->supplier_id;
	}
	
	public function setSupplierId( int $supplier_id ): void
	{
		$this->supplier_id = $supplier_id;
	}
	
	public function getDeliveryClassId(): int
	{
		return $this->delivery_class_id;
	}
	
	public function setDeliveryClassId( int $delivery_class_id ): void
	{
		$this->delivery_class_id = $delivery_class_id;
	}

	public function getDescription() : string
	{
		return $this->description;
	}

	public function setDescription( string $description ) : void
	{
		$this->description = $description;
	}

	public function getSeoTitle() : string
	{
		return $this->seo_title;
	}

	public function setSeoTitle( string $seo_title ) : void
	{
		$this->seo_title = $seo_title;
	}

	public function getSeoDescription() : string
	{
		return $this->seo_description;
	}

	public function setSeoDescription( string $seo_description ) : void
	{
		$this->seo_description = $seo_description;
	}

	public function getShortDescription() : string
	{
		return $this->short_description;
	}

	public function setShortDescription( string $short_description ) : void
	{
		$this->short_description = $short_description;
	}
	
	
	public function getVatRate() : float
	{
		return $this->vat_rate;
	}

	public function setVatRate( float $vat_rate ) : void
	{
		$this->vat_rate = $vat_rate;
	}


	public function getStandardPrice() : float
	{
		return $this->standard_price;
	}

	public function setStandardPrice( float $standard_price ) : void
	{
		$this->standard_price = $standard_price;
		$this->calcDiscount();
	}
	
	public function getPrice(): float
	{
		return $this->price;
	}
	
	/**
	 * @return Product_PriceHistory[]
	 */
	public function getPriceHistory() : array
	{
		$where = $this->getShop()->getWhere();
		$where[] = 'AND';
		$where['product_id'] = $this->getId();
		$_history = Product_PriceHistory::fetchInstances( $where );
		$_history->getQuery()->setOrderBy('-id');
		
		$history = [];
		foreach($_history as $hi) {
			$history[] = $hi;
		}
		
		return $history;
	}
	
	public function setPrice( float $price ): void
	{
		if($this->price==$price) {
			return;
		}
		
		$this->price = $price;
		$this->calcDiscount();
		$this->save();
		
		Product_PriceHistory::newRecord( $this );
	}
	
	public function actualizePriceReferences() : void
	{
		switch($this->getType()) {
			case Product::PRODUCT_TYPE_REGULAR:
				foreach($this->getSetIds() as $set_id ) {
					$set = static::get( $set_id, $this->getShop() );
					
					$set?->actualizeSet();
				}
				
				break;
			case Product::PRODUCT_TYPE_VARIANT:
				$this->getVariantMasterProduct()?->actualizeVariantMaster();
				
				break;
		}
		
	}
	
	protected function calcDiscount() : void
	{
		if($this->standard_price>0) {
			$this->discount_percentage = round(100-( ($this->price * 100) / $this->standard_price) );
		} else {
			$this->discount_percentage = 0;
		}
	}
	
	public function getDiscountPercentage() : float
	{
		return $this->discount_percentage;
	}
	
	
	public function getInStockQty() : int
	{
		return $this->in_stock_qty;
	}

	public function setInStockQty( int $in_stock_qty ) : void
	{
		$this->in_stock_qty = $in_stock_qty;
	}

	public function getSeoKeywords() : string
	{
		return $this->seo_keywords;
	}
	
	public function getLengthOfDelivery(): int
	{
		return $this->length_of_delivery;
	}
	
	public function setLengthOfDelivery( int $length_of_delivery ): void
	{
		$this->length_of_delivery = $length_of_delivery;
	}
	
	public function getAvailableFrom(): ?Data_DateTime
	{
		return $this->available_from;
	}
	
	public function setAvailableFrom( Data_DateTime|string|null $available_from ): void
	{
		$this->available_from = Data_DateTime::catchDateTime( $available_from );
	}
	
	
	

	public function setSeoKeywords( string $seo_keywords ) : void
	{
		$this->seo_keywords = $seo_keywords;
	}

	public function getURLPathPart() : string
	{
		return $this->URL_path_part;
	}

	public function setURLPathPart( string $URL_path_part ) : void
	{
		$this->URL_path_part = $URL_path_part;
	}

	public function getURL() : string
	{
		return $this->getShop()->getURL( [$this->URL_path_part] );
	}

	public function generateURLPathPart() : void
	{
		if(!$this->entity_id) {
			return;
		}

		$this->URL_path_part = $this->_generateURLPathPart( $this->getFullName(), 'p' );
		
		$where = $this->getShop()->getWhere();
		$where[] = 'AND';
		$where['entity_id'] = $this->entity_id;
		
		static::updateData(
			['URL_path_part'=>$this->URL_path_part],
			$where
		);
		
	}
	
	public function afterAdd(): void
	{
		$this->generateURLPathPart();
	}
	
	public function getCategoryIds() : array
	{
		if($this->category_ids===null) {
			if($this->type==Product::PRODUCT_TYPE_VARIANT) {
				$id = $this->variant_master_product_id;
			} else {
				$id = $this->entity_id;
			}
			
			$this->category_ids = Category_Product::dataFetchCol(
				select: ['category_id'],
				where:['product_id'=>$id]
			);
		}
		
		return $this->category_ids;
	}
	
	/**
	 * @return Category_ShopData[]
	 */
	public function getCategories() : array
	{
		if($this->categories===null) {
			$this->categories = Category_ShopData::getActiveList( $this->getCategoryIds() );
		}
		
		return $this->categories;
	}
	
}