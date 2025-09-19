<?php
/**
 * @copyright Copyright (c) Miroslav Marek <mirek.marek@web-jet.cz>
 * @license EUPL 1.2  https://eupl.eu/1.2/en/
 * @author Miroslav Marek <mirek.marek@web-jet.cz>
 */
namespace JetShop;


use Jet\DataModel;
use Jet\DataModel_Definition;
use Jet\Form_Definition;
use Jet\Form_Field;
use JetApplication\EShop;
use JetApplication\EShopEntity_HasImageGallery_Interface;
use JetApplication\EShopEntity_HasURL_Interface;
use JetApplication\EShopEntity_HasURL_Trait;
use JetApplication\EShopEntity_Definition;
use JetApplication\Availability;
use JetApplication\DeliveryTerm;
use JetApplication\DeliveryTerm_Info;
use JetApplication\EShopEntity_HasPrice_Interface;
use JetApplication\EShopEntity_WithEShopData_EShopData;
use JetApplication\KindOfProduct;
use JetApplication\KindOfProduct_EShopData;
use JetApplication\MeasureUnit;
use JetApplication\Product;
use JetApplication\Product_EShopData;
use JetApplication\Product_EShopData_Trait_Images;
use JetApplication\Product_EShopData_Trait_Price;
use JetApplication\Product_EShopData_Trait_Set;
use JetApplication\Product_EShopData_Trait_Variants;
use JetApplication\Product_EShopData_Trait_Availability;
use JetApplication\Product_EShopData_Trait_Files;
use JetApplication\Product_EShopData_Trait_Categories;
use JetApplication\Product_EShopData_Trait_Boxes;
use JetApplication\Product_EShopData_Trait_SimilarProducts;
use JetApplication\Product_EShopData_Trait_Accessories;


#[DataModel_Definition(
	name: 'products_eshop_data',
	database_table_name: 'products_eshop_data',
	parent_model_class: Product::class
)]
#[EShopEntity_Definition(
	URL_template: '%NAME%-p-%ID%'
)]
abstract class Core_Product_EShopData extends EShopEntity_WithEShopData_EShopData implements
	EShopEntity_HasPrice_Interface,
	EShopEntity_HasImageGallery_Interface,
	EShopEntity_HasURL_Interface
{
	use EShopEntity_HasURL_Trait;
	
	use Product_EShopData_Trait_Price;
	use Product_EShopData_Trait_Set;
	use Product_EShopData_Trait_Variants;
	use Product_EShopData_Trait_Availability;
	use Product_EShopData_Trait_Files;
	use Product_EShopData_Trait_Categories;
	use Product_EShopData_Trait_Boxes;
	use Product_EShopData_Trait_SimilarProducts;
	use Product_EShopData_Trait_Accessories;
	use Product_EShopData_Trait_Images;
	
	
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
		type: DataModel::TYPE_BOOL,
		is_key: true
	)]
	protected bool $creation_in_progress = false;
	
	#[DataModel_Definition(
		type: DataModel::TYPE_BOOL,
		is_key: true
	)]
	protected bool $archived = false;
	
	
	#[DataModel_Definition(
		type: DataModel::TYPE_STRING,
		max_len: 100,
		is_key: true,
	)]
	protected string $ean = '';
	
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
		type: DataModel::TYPE_STRING,
		max_len: 50,
		is_key: true,
	)]
	protected string $supplier_code = '';
	
	#[DataModel_Definition(
		type: DataModel::TYPE_INT,
		is_key: true,
	)]
	protected int $delivery_class_id = 0;
	
	
	#[DataModel_Definition(
		type: DataModel::TYPE_STRING,
		max_len: 150,
	)]
	#[Form_Definition(
		type: Form_Field::TYPE_INPUT,
		label: 'Name:'
	)]
	#[EShopEntity_Definition(
		is_description: true
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
	#[EShopEntity_Definition(
		is_description: true
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
	#[EShopEntity_Definition(
		is_description: true
	)]
	protected string $description = '';
	
	#[DataModel_Definition(
		type: DataModel::TYPE_STRING,
		max_len: 65536,
	)]
	#[Form_Definition(
		type: Form_Field::TYPE_TEXTAREA,
		label: 'Video URLs:'
	)]
	#[EShopEntity_Definition(
		is_description: true
	)]
	protected string $video_URLs = '';
	

	#[DataModel_Definition(
		type: DataModel::TYPE_STRING,
		max_len: 255,
	)]
	#[Form_Definition(
		type: Form_Field::TYPE_INPUT,
		label: 'Title:'
	)]
	#[EShopEntity_Definition(
		is_description: true
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
	#[EShopEntity_Definition(
		is_description: true
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
	#[EShopEntity_Definition(
		is_description: true
	)]
	protected string $seo_keywords = '';

	#[DataModel_Definition(
		type: DataModel::TYPE_STRING,
		max_len: 512,
	)]
	protected string $URL_path_part = '';
	
	
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
		type: DataModel::TYPE_BOOL,
	)]
	#[Form_Definition(
		type: Form_Field::TYPE_CHECKBOX,
		label: 'Allow to order when sold out'
	)]
	protected bool $allow_to_order_when_sold_out = true;
	
	#[DataModel_Definition(
		type: DataModel::TYPE_BOOL,
		is_key: true
	)]
	protected bool $is_sale = false;
	
	#[DataModel_Definition(
		type: DataModel::TYPE_BOOL,
		is_key: true
	)]
	protected bool $deactivate_after_sell_out = false;
	
	#[DataModel_Definition(
		type: DataModel::TYPE_INT,
		is_key: true
	)]
	protected int $non_sale_product_id = 0;
	
	
	protected KindOfProduct_EShopData|null|bool $kind = null;
	
	public function getEntityTypeForImageGallery() : string
	{
		return 'product';
	}
	
	public function getEntityIdForImageGallery() : string|int
	{
		return $this->getEntityId();
	}
	
	
	public function getURLNameDataSource(): string
	{
		return $this->URL_path_part ? : $this->getFullName();
	}
	
	public function getURLPathPart() : string
	{
		return $this->URL_path_part;
	}
	
	public function setURLPathPart( string $URL_path_part ) : void
	{
		$this->URL_path_part = $URL_path_part;
	}
	
	
	public function _activate(): void
	{
		parent::_activate();
		$id = $this->isVariant() ? $this->getVariantMasterProductId() : $this->getId();
		Product::actualizeReferences( $id );
		
	}
	
	public function _deactivate(): void
	{
		parent::_deactivate();
		$id = $this->isVariant() ? $this->getVariantMasterProductId() : $this->getId();
		Product::actualizeReferences( $id );
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
	
	public function isVirtual() : bool
	{
		return (in_array($this->kind_id, KindOfProduct::getVirtualKidOfProductIds()));
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
	
	
	public function setType( string $type ): void
	{
		$this->type = $type;
	}
	
	public function getAllowToOrderWhenSoldOut(): bool
	{
		return $this->allow_to_order_when_sold_out;
	}
	
	public function setAllowToOrderWhenSoldOut( bool $allow_to_order_when_sold_out ): void
	{
		$this->allow_to_order_when_sold_out = $allow_to_order_when_sold_out;
	}
	
	public function getIsSale(): bool
	{
		return $this->is_sale;
	}
	
	public function setIsSale( bool $is_sale ): void
	{
		$this->is_sale = $is_sale;
	}
	
	public function isDeactivateAfterSellOut(): bool
	{
		return $this->deactivate_after_sell_out;
	}
	
	public function setDeactivateAfterSellOut( bool $deactivate_after_sell_out ): void
	{
		$this->deactivate_after_sell_out = $deactivate_after_sell_out;
	}
	
	public function getNonSaleProductId(): int
	{
		return $this->non_sale_product_id;
	}
	
	public function setNonSaleProductId( int $non_sale_product_id ): void
	{
		$this->non_sale_product_id = $non_sale_product_id;
	}
	
	
	
	
	
	public function getKindId(): int
	{
		return $this->kind_id;
	}
	
	public function getKind() : ?KindOfProduct_EShopData
	{
		if($this->kind===null) {
			$this->kind = KindOfProduct_EShopData::get( $this->kind_id );
			if(!$this->kind) {
				$this->kind = false;
			}
		}
		
		return $this->kind?:null;
	}
	
	public function getCreationInProgress(): bool
	{
		return $this->creation_in_progress;
	}
	
	public function setCreationInProgress( bool $creation_in_progress ): void
	{
		$this->creation_in_progress = $creation_in_progress;
	}
	
	public function getArchived(): bool
	{
		return $this->archived;
	}
	
	public function setArchived( bool $archived ): void
	{
		$this->archived = $archived;
	}
	
	
	
	public function getMeasureUnit() : ?MeasureUnit
	{
		return $this->getKind()?->getMeasureUnit();
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
	
	public function getSupplierCode(): string
	{
		return $this->supplier_code;
	}
	
	public function setSupplierCode( string $supplier_code ): void
	{
		$this->supplier_code = $supplier_code;
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
	
	public function getSeoKeywords() : string
	{
		return $this->seo_keywords;
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
	
	
	public function setSeoKeywords( string $seo_keywords ) : void
	{
		$this->seo_keywords = $seo_keywords;
	}
	
	public function getReviewCount(): int
	{
		return $this->review_count;
	}

	public function getReviewRank(): int
	{
		return $this->review_rank;
	}
	
	public function getQuestionCount(): int
	{
		return $this->question_count;
	}
	
	public function setVideoURLs( string $video_URLs ): void
	{
		$this->video_URLs = $video_URLs;
	}
	
	
	public function getVideoURLs( bool $as_array=false ) : array|string
	{
		if(!$as_array) {
			return $this->video_URLs;
		}
		
		$_videos = explode("\n", $this->video_URLs);
		$videos = [];
		
		foreach($_videos as $_video) {
			$video = trim($_video);
			if($video) {
				$videos[] = $video;
			}
		}
		
		return $videos;
	}
	
	
	public function getDeliveryInfo( float $units_required=1, ?Availability $availability=null ) : DeliveryTerm_Info
	{
		return DeliveryTerm::getInfo( $this, $units_required, $availability );
	}
	
	public static function getActiveProductsIds( EShop $eshop, array $product_ids ): array
	{
		$ids = Product_EShopData::dataFetchCol(
			select:['entity_id'],
			where: [
				'entity_id' => $product_ids,
				'AND',
				Product_EShopData::getActiveQueryWhere( $eshop ),
				'AND',
				'type' => [
					Product::PRODUCT_TYPE_REGULAR,
					Product::PRODUCT_TYPE_SET,
					Product::PRODUCT_TYPE_VARIANT_MASTER
				]
			]
		);
		
		return $ids;
	}
}