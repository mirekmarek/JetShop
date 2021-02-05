<?php
namespace JetShop;

use http\Encoding\Stream;
use Jet\DataModel;
use Jet\DataModel_Definition;
use Jet\Form;
use Jet\Data_DateTime;
use Jet\DataModel_IDController_Passive;
use Jet\DataModel_Related_1toN;

#[DataModel_Definition(
	name: 'products_shop_data',
	database_table_name: 'products_shop_data',
	id_controller_class: DataModel_IDController_Passive::class,
	parent_model_class: Product::class
)]
abstract class Core_Product_ShopData extends DataModel_Related_1toN implements CommonEntity_ShopDataInterface {

	use CommonEntity_ShopDataTrait;

	#[DataModel_Definition(
		type: DataModel::TYPE_INT,
		is_id: true,
		related_to: 'main.id',
		form_field_type: false
	)]
	protected int $product_id = 0;

	protected Product|null $product = null;

	#[DataModel_Definition(
		type: DataModel::TYPE_STRING,
		max_len: 100,
		form_field_label: 'Name:'
	)]
	protected string $name = '';

	#[DataModel_Definition(
		type: DataModel::TYPE_STRING,
		max_len: 100,
		form_field_label: 'Name of variant:'
	)]
	protected string $variant_name = '';

	#[DataModel_Definition(
		type: DataModel::TYPE_STRING,
		form_field_type: Form::TYPE_WYSIWYG,
		max_len: 65536,
		form_field_label: 'Short description:'
	)]
	protected string $short_description = '';

	#[DataModel_Definition(
		type: DataModel::TYPE_STRING,
		form_field_type: Form::TYPE_WYSIWYG,
		max_len: 65536,
		form_field_label: 'Description:'
	)]
	protected string $description = '';

	#[DataModel_Definition(
		type: DataModel::TYPE_STRING,
		max_len: 255,
		form_field_label: 'H1:'
	)]
	protected string $seo_h1 = '';

	#[DataModel_Definition(
		type: DataModel::TYPE_STRING,
		max_len: 255,
		form_field_label: 'Title:'
	)]
	protected string $seo_title = '';

	#[DataModel_Definition(
		type: DataModel::TYPE_STRING,
		max_len: 65536,
		form_field_label: 'Description:'
	)]
	protected string $seo_description = '';

	#[DataModel_Definition(
		type: DataModel::TYPE_STRING,
		max_len: 65536,
		form_field_label: 'Keywords:'
	)]
	protected string $seo_keywords = '';

	#[DataModel_Definition(
		type: DataModel::TYPE_STRING,
		max_len: 512,
		form_field_type: false
	)]
	protected string $URL_path_part = '';

	#[DataModel_Definition(
		type: DataModel::TYPE_STRING,
		max_len: 999999,
		form_field_label: 'Keywords words for internal fulltext:'
	)]
	protected string $internal_fulltext_keywords = '';

	#[DataModel_Definition(
		type: DataModel::TYPE_STRING,
		max_len: 255,
		form_field_type: false
	)]
	protected string $image_0 = '';

	#[DataModel_Definition(
		type: DataModel::TYPE_STRING,
		max_len: 255,
		form_field_type: false
	)]
	protected string $image_1 = '';

	#[DataModel_Definition(
		type: DataModel::TYPE_STRING,
		max_len: 255,
		form_field_type: false
	)]
	protected string $image_2 = '';

	#[DataModel_Definition(
		type: DataModel::TYPE_STRING,
		max_len: 255,
		form_field_type: false
	)]
	protected string $image_3 = '';

	#[DataModel_Definition(
		type: DataModel::TYPE_STRING,
		max_len: 255,
		form_field_type: false
	)]
	protected string $image_4 = '';

	#[DataModel_Definition(
		type: DataModel::TYPE_STRING,
		max_len: 255,
		form_field_type: false
	)]
	protected string $image_5 = '';

	#[DataModel_Definition(
		type: DataModel::TYPE_STRING,
		max_len: 255,
		form_field_type: false
	)]
	protected string $image_6 = '';

	#[DataModel_Definition(
		type: DataModel::TYPE_STRING,
		max_len: 255,
		form_field_type: false
	)]
	protected string $image_7 = '';

	#[DataModel_Definition(
		type: DataModel::TYPE_STRING,
		max_len: 255,
		form_field_type: false
	)]
	protected string $image_8 = '';

	#[DataModel_Definition(
		type: DataModel::TYPE_STRING,
		max_len: 255,
		form_field_type: false
	)]
	protected string $image_9 = '';

	#[DataModel_Definition(
		type: DataModel::TYPE_FLOAT,
		form_field_label: 'Tax rate:'
	)]
	protected float $vat_rate = 0.0;

	#[DataModel_Definition(
		type: DataModel::TYPE_FLOAT,
		form_field_label: 'Standard price:'
	)]
	protected float $standard_price = 0.0;

	#[DataModel_Definition(
		type: DataModel::TYPE_FLOAT,
		form_field_label: 'Action price:'
	)]
	protected float $action_price = 0.0;

	#[DataModel_Definition(
		type: DataModel::TYPE_DATE_TIME,
		form_field_label: 'Action price valid from:'
	)]
	protected Data_DateTime|null $action_price_valid_from = null;

	#[DataModel_Definition(
		type: DataModel::TYPE_DATE_TIME,
		form_field_label: 'Action price valid till:'
	)]
	protected Data_DateTime|null $action_price_valid_till = null;

	#[DataModel_Definition(
		type: DataModel::TYPE_FLOAT,
		form_field_label: 'Sale price:'
	)]
	protected float $sale_price = 0.0;

	#[DataModel_Definition(
		type: DataModel::TYPE_FLOAT,
		form_field_type: false
	)]
	protected float $final_price = 0.0;

	#[DataModel_Definition(
		type: DataModel::TYPE_FLOAT,
		form_field_type: false
	)]
	protected float $discount_percentage = 0.0;

	#[DataModel_Definition(
		type: DataModel::TYPE_BOOL,
		is_key: true,
		form_field_label: 'Reset sale after sold out'
	)]
	protected bool $reset_sale_after_sold_out = false;

	#[DataModel_Definition(
		type: DataModel::TYPE_BOOL,
		is_key: true,
		form_field_label: 'Deactivate product after sold out'
	)]
	protected bool $deactivate_product_after_sold_out = false;

	#[DataModel_Definition(
		type: DataModel::TYPE_STRING,
		max_len: 100,
		is_key: true,
		form_field_label: 'Delivery term:',
		form_field_type: Form::TYPE_SELECT,
		form_field_get_select_options_callback: [Delivery_Deadline::class, 'getScope']
	)]
	protected string $delivery_term_code = '';

	#[DataModel_Definition(
		type: DataModel::TYPE_STRING,
		max_len: 100,
		is_key: true,
		form_field_label: 'Delivery class:',
		form_field_type: Form::TYPE_SELECT,
		form_field_get_select_options_callback: [Delivery_Class::class, 'getScope']
	)]
	protected string $delivery_class_code = '';

	#[DataModel_Definition(
		type: DataModel::TYPE_DATE,
		form_field_label: 'Availability date:'
	)]
	protected Data_DateTime|null $date_available = null;

	#[DataModel_Definition(
		type: DataModel::TYPE_INT,
		is_key: true,
		form_field_label: 'Stock status:'
	)]
	protected int $stock_status = 0;

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

	public function getProduct() : Product
	{
		return $this->product;
	}

	public function setParents( Product $product ) : void
	{
		$this->product = $product;
		$this->product_id = $product->getId();
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
		return Shops::getURL( $this->shop_id, [$this->URL_path_part] );
	}

	public function generateURLPathPart() : void
	{
		if(!$this->product_id) {
			return;
		}

		$this->URL_path_part = Shops::generateURLPathPart( $this->name, 'p', $this->product_id, $this->shop_id );
	}

	public function getImageEntity() : string
	{
		return 'product';
	}

	public function getImageObjectId() : int|string
	{
		return $this->product_id;
	}

	public function getPossibleToEditImages() : bool
	{
		return !$this->product->getEditForm()->getIsReadonly();
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
	}

	public function getImage( int $i = 0 ) : string
	{
		return $this->{"image_{$i}"};
	}

	public function setImage( int $i, string $img ) : void
	{
		$this->{"image_{$i}"} = $img;
	}

	public function getImageUrl( int $i = 0 ) : string
	{
		return Images::getUrl( $this->getImage( $i ) );
	}

	public function getImageThumbnailUrl( int $max_w, int $max_h, int $i=0 ) : string
	{
		return Images::getThumbnailUrl( $this->getImage( $i ), $max_w, $max_h );
	}

	public function uploadImages() : void
	{
		$current_images = [];

		for( $i=0; $i<Product::$max_image_count; $i++ ) {
			if($this->getImage( $i )) {
				$current_images[] = $this->getImage( $i );
			}
		}


		$new_images = [];
		foreach( $_FILES['images']['tmp_name'] as $i=>$tmp_name ) {
			if(
				!$tmp_name ||
				!@getimagesize( $tmp_name )
			) {
				continue;
			}

			$new_images[] = $tmp_name;

			if( (count($current_images)+count($new_images))>=Product::$max_image_count ) {
				break;
			}
		}

		$i = 0;
		foreach( $current_images as $current_image ) {
			$this->{"image_{$i}"} = $current_image;
			$i++;
		}

		foreach( $new_images as $new_image ) {
			if($i>=Product::$max_image_count) {
				break;
			}

			Images::uploadImage(
				$new_image,
				$this->shop_id,
				'product',
				$this->product_id,
				'image',
				$this->{"image_{$i}"}
			);

			$i++;
		}

	}

	public function deleteImages( array  $indexes ) : void
	{

		foreach($indexes as $i) {
			$i = (int)$i;

			$property = 'image_'.$i;
			if(!property_exists($this, $property)) {
				break;
			}

			if(!$this->{$property} ) {
				continue;
			}

			Images::deleteImage( $this->{$property} );

			$this->{$property} = '';
		}

		$current_images = [];

		for( $i=0; $i<Product::$max_image_count; $i++ ) {
			if($this->getImage( $i )) {
				$current_images[] = $this->getImage( $i );
			}

			$this->{"image_{$i}"} = '';
		}

		foreach($current_images as $i=>$image) {
			$this->{"image_{$i}"} = $image;
		}

	}

	public function sortImages( array $indexes ) : void
	{

		$current_images = [];

		for( $i=0; $i<Product::$max_image_count; $i++ ) {
			if($this->getImage( $i )) {
				$current_images[] = $this->getImage( $i );
			}
		}

		if(count($indexes)!=count($current_images)) {
			return;
		}


		$images = [];

		foreach($indexes as $i) {
			if(!isset($current_images[$i])) {
				return;
			}

			$images[] = $current_images[$i];
		}

		foreach($images as $i=>$image) {
			$this->{"image_{$i}"} = $image;
		}

	}
}