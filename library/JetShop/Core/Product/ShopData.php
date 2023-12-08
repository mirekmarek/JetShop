<?php
namespace JetShop;

use Jet\Data_DateTime;
use Jet\DataModel;
use Jet\DataModel_Definition;
use Jet\Form_Definition;
use Jet\Form_Field;
use Jet\Form_Field_Date;
use JetApplication\Category;
use JetApplication\Entity_WithIDAndShopData_ShopData;
use JetApplication\Product;
use JetApplication\Shops;

#[DataModel_Definition(
	name: 'products_shop_data',
	database_table_name: 'products_shop_data',
	parent_model_class: Product::class
)]
abstract class Core_Product_ShopData extends Entity_WithIDAndShopData_ShopData {
	
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
		max_len: 100,
	)]
	#[Form_Definition(
		type: Form_Field::TYPE_INPUT,
		label: 'Name of variant:'
	)]
	protected string $variant_name = '';

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
		label: 'H1:'
	)]
	protected string $seo_h1 = '';

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
		type: DataModel::TYPE_STRING,
		max_len: 999999,
	)]
	#[Form_Definition(
		type: Form_Field::TYPE_TEXTAREA,
		label: 'Keywords for internal fulltext:'
	)]
	protected string $internal_fulltext_keywords = '';
	
	#[DataModel_Definition(
		type: DataModel::TYPE_STRING,
		max_len: 9999999
	)]
	protected string $category_ids = '';
	

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
		label: 'Length of delivery :',
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
	
	#[DataModel_Definition(
		type: DataModel::TYPE_STRING,
		max_len: 20,
	)]
	protected string $set_discount_type = '';
	
	#[DataModel_Definition(
		type: DataModel::TYPE_FLOAT,
	)]
	protected float $set_discount_value = 0.0;
	
	public function activate(): void
	{
		parent::activate();
		Category::productActivated( product_id: $this->entity_id );
	}
	
	public function deactivate(): void
	{
		parent::deactivate();
		Category::productDeactivated( product_id: $this->entity_id );
	}
	
	
	public function getName() : string
	{
		return $this->name;
	}

	public function setName( string $name ) : void
	{
		$this->name = $name;
		$this->generateURLPathPart();
	}

	public function getDescription() : string
	{
		return $this->description;
	}

	public function setDescription( string $description ) : void
	{
		$this->description = $description;
	}

	public function getSeoH1() : string
	{
		return $this->seo_h1;
	}

	public function setSeoH1( string $seo_h1 ) : void
	{
		$this->seo_h1 = $seo_h1;
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

	public function getVariantName() : string
	{
		return $this->variant_name;
	}

	public function setVariantName( string $variant_name ) : void
	{
		$this->variant_name = $variant_name;
	}
	public function getFullName(): string
	{
		$name = $this->getName();
		$variant_name = $this->getVariantName();
		
		if($variant_name) {
			return $name.' '.$variant_name;
		}
		
		return $name;
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
	
	public function setPrice( float $price ): void
	{
		$this->price = $price;
		$this->calcDiscount();
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

	public function getInternalFulltextKeywords() : string
	{
		return $this->internal_fulltext_keywords;
	}

	public function setInternalFulltextKeywords( string $internal_fulltext_keywords ) : void
	{
		$this->internal_fulltext_keywords = $internal_fulltext_keywords;
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
		return Shops::getURL( $this->getShop(), [$this->URL_path_part] );
	}

	public function generateURLPathPart() : void
	{
		if(!$this->entity_id) {
			return;
		}

		$this->URL_path_part = $this->_generateURLPathPart( $this->name, 'p' );
	}
	
	
	public function setCategoryIds( array $value ) : void
	{
		$this->category_ids = implode(',', $value);
	}
	
	public function getCategoryIds() : array
	{
		if(!$this->category_ids) {
			return [];
		}
		
		return explode(',', $this->category_ids);
	}
	
	/**
	 * @return string
	 */
	public function getSetDiscountType(): string
	{
		return $this->set_discount_type;
	}
	
	public function setSetDiscountType( string $set_discount_type ): void
	{
		$this->set_discount_type = $set_discount_type;
	}
	
	public function getSetDiscountValue(): float
	{
		return $this->set_discount_value;
	}
	
	public function setSetDiscountValue( float $set_discount_value ): void
	{
		$this->set_discount_value = $set_discount_value;
	}
	
	
	
}