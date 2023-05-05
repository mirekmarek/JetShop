<?php
namespace JetShop;

use Jet\DataModel;
use Jet\DataModel_Definition;
use Jet\Form_Definition;
use Jet\Form_Field;
use Jet\Data_DateTime;

use JetApplication\Delivery_Class;
use JetApplication\Delivery_Deadline;
use JetApplication\Product;
use JetApplication\CommonEntity_ShopData;
use JetApplication\Shops;

#[DataModel_Definition(
	name: 'products_shop_data',
	database_table_name: 'products_shop_data',
	parent_model_class: Product::class
)]
abstract class Core_Product_ShopData extends CommonEntity_ShopData {

	#[DataModel_Definition(
		type: DataModel::TYPE_INT,
		is_id: true,
		related_to: 'main.id',
	)]
	protected int $product_id = 0;

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
		label: 'Standard price:'
	)]
	protected float $standard_price = 0.0;

	#[DataModel_Definition(
		type: DataModel::TYPE_FLOAT,
	)]
	#[Form_Definition(
		type: Form_Field::TYPE_FLOAT,
		label: 'Action price:'
	)]
	protected float $action_price = 0.0;

	#[DataModel_Definition(
		type: DataModel::TYPE_DATE_TIME,
	)]
	#[Form_Definition(
		type: Form_Field::TYPE_DATE_TIME,
		label: 'Action price valid from:'
	)]
	protected Data_DateTime|null $action_price_valid_from = null;

	#[DataModel_Definition(
		type: DataModel::TYPE_DATE_TIME,
	)]
	#[Form_Definition(
		type: Form_Field::TYPE_DATE_TIME,
		label: 'Action price valid till:'
	)]
	protected Data_DateTime|null $action_price_valid_till = null;

	#[DataModel_Definition(
		type: DataModel::TYPE_FLOAT,
	)]
	#[Form_Definition(
		type: Form_Field::TYPE_FLOAT,
		label: 'Sale price:'
	)]
	protected float $sale_price = 0.0;

	#[DataModel_Definition(
		type: DataModel::TYPE_FLOAT,
	)]
	protected float $final_price = 0.0;

	#[DataModel_Definition(
		type: DataModel::TYPE_FLOAT,
	)]
	protected float $discount_percentage = 0.0;

	#[DataModel_Definition(
		type: DataModel::TYPE_BOOL,
		is_key: true,
	)]
	#[Form_Definition(
		type: Form_Field::TYPE_CHECKBOX,
		label: 'Reset sale after sold out'
	)]
	protected bool $reset_sale_after_sold_out = false;

	#[DataModel_Definition(
		type: DataModel::TYPE_BOOL,
		is_key: true,
	)]
	#[Form_Definition(
		type: Form_Field::TYPE_CHECKBOX,
		label: 'Deactivate product after sold out'
	)]
	protected bool $deactivate_product_after_sold_out = false;

	#[DataModel_Definition(
		type: DataModel::TYPE_STRING,
		max_len: 100,
		is_key: true,
	)]
	#[Form_Definition(
		label: 'Delivery term:',
		type: Form_Field::TYPE_SELECT,
		select_options_creator: [Delivery_Deadline::class, 'getScope']
	)]
	protected string $delivery_term_code = '';

	#[DataModel_Definition(
		type: DataModel::TYPE_STRING,
		max_len: 100,
		is_key: true,
	)]
	#[Form_Definition(
		type: Form_Field::TYPE_SELECT,
		label: 'Delivery class:',
		select_options_creator: [Delivery_Class::class, 'getScope']
	)]
	protected string $delivery_class_code = '';

	#[DataModel_Definition(
		type: DataModel::TYPE_DATE,
	)]
	#[Form_Definition(
		type: Form_Field::TYPE_DATE,
		label: 'Availability date:'
	)]
	protected Data_DateTime|null $date_available = null;

	#[DataModel_Definition(
		type: DataModel::TYPE_INT,
		is_key: true,
	)]
	protected int $stock_status = 0;

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
	}

	public function getActionPrice() : float
	{
		return $this->action_price;
	}

	public function setActionPrice( float $action_price ) : void
	{
		$this->action_price = $action_price;
	}

	public function getActionPriceValidFrom() : Data_DateTime|null
	{
		return $this->action_price_valid_from;
	}

	public function setActionPriceValidFrom( Data_DateTime|null $action_price_valid_from ) : void
	{
		$this->action_price_valid_from = $action_price_valid_from;
	}

	public function getActionPriceValidTill() : Data_DateTime|null
	{
		return $this->action_price_valid_till;
	}

	public function setActionPriceValidTill( Data_DateTime|null $action_price_valid_till ) : void
	{
		$this->action_price_valid_till = $action_price_valid_till;
	}

	public function getSalePrice() : float
	{
		return $this->sale_price;
	}

	public function setSalePrice( float $sale_price ) : void
	{
		$this->sale_price = $sale_price;
	}

	public function getFinalPrice() : float
	{
		return $this->final_price;
	}

	public function setFinalPrice( float $final_price ) : void
	{
		$this->final_price = $final_price;
	}

	public function getDiscountPercentage() : float
	{
		return $this->discount_percentage;
	}

	public function isResetSaleAfterSoldOut() : bool
	{
		return $this->reset_sale_after_sold_out;
	}

	public function setResetSaleAfterSoldOut( bool $reset_sale_after_sold_out ) : void
	{
		$this->reset_sale_after_sold_out = $reset_sale_after_sold_out;
	}

	public function isDeactivateProductAfterSoldOut() : bool
	{
		return $this->deactivate_product_after_sold_out;
	}

	public function setDeactivateProductAfterSoldOut( bool $deactivate_product_after_sold_out ) : void
	{
		$this->deactivate_product_after_sold_out = $deactivate_product_after_sold_out;
	}

	public function getDeliveryTermCode() : string
	{
		return $this->delivery_term_code;
	}

	public function setDeliveryTermCode( string $delivery_term_code ) : void
	{
		$this->delivery_term_code = $delivery_term_code;
	}

	public function getDeliveryClassCode(): string
	{
		return $this->delivery_class_code;
	}

	public function setDeliveryClassCode( string $delivery_class_code ): void
	{
		$this->delivery_class_code = $delivery_class_code;
	}

	public function getDateAvailable() : Data_DateTime|null
	{
		return $this->date_available;
	}

	public function setDateAvailable( Data_DateTime|null $date_available ) : void
	{
		$this->date_available = $date_available;
	}

	public function getStockStatus() : int
	{
		return $this->stock_status;
	}

	public function setStockStatus( int $stock_status ) : void
	{
		$this->stock_status = $stock_status;
	}

	public function getSeoKeywords() : string
	{
		return $this->seo_keywords;
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
		if(!$this->product_id) {
			return;
		}

		$this->URL_path_part = Shops::generateURLPathPart( $this->name, 'p', $this->product_id, $this->getShop() );
	}


	public function actualizePrice() : void
	{
		$this->final_price = $this->standard_price;
		$this->discount_percentage = 0;

		if(
			$this->action_price>0 &&
			($this->action_price_valid_from===null || $this->action_price_valid_from<=Data_DateTime::now()) &&
			($this->action_price_valid_till===null || $this->action_price_valid_till>=Data_DateTime::now())
		) {
			$this->final_price = $this->action_price;
			$this->discount_percentage = round(100-( ($this->action_price * 100) / $this->standard_price), 2);
		}

		if($this->sale_price>0) {
			$this->final_price = $this->sale_price;
			$this->discount_percentage = round(100-( ($this->sale_price * 100) / $this->standard_price), 2);
		}
		
		//TODO: variant
		//TODO: set
	}
	
}