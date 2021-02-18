<?php
namespace JetShop;

use Jet\DataModel;
use Jet\DataModel_Definition;
use Jet\Form;
use Jet\DataModel_IDController_Passive;
use Jet\DataModel_Related_1toN;
use Jet\Tr;

#[DataModel_Definition(
	name: 'stickers_shop_data',
	database_table_name: 'stickers_shop_data',
	id_controller_class: DataModel_IDController_Passive::class,
	parent_model_class: Sticker::class
)]
abstract class Core_Sticker_ShopData extends DataModel_Related_1toN implements Images_ShopDataInterface, CommonEntity_ShopDataInterface {

	use CommonEntity_ShopDataTrait;
	use Images_ShopDataTrait;

	const IMG_PICTOGRAM_FILTER = 'pictogram_filter';
	const IMG_PICTOGRAM_PRODUCT_DETAIL = 'pictogram_product_detail';
	const IMG_PICTOGRAM_PRODUCT_LISTING = 'pictogram_product_listing';

	#[DataModel_Definition(
		type: DataModel::TYPE_INT,
		is_id: true,
		related_to: 'main.id',
		form_field_type: false
	)]
	protected int $sticker_id = 0;

	protected ?Sticker $sticker = null;

	#[DataModel_Definition(
		type: DataModel::TYPE_STRING,
		max_len: 100,
		form_field_label: 'Name:'
	)]
	protected string $name = '';

	#[DataModel_Definition(
		type: DataModel::TYPE_STRING,
		max_len: 100,
		form_field_label: 'Alternative name:'
	)]
	protected string $second_name = '';

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
		type: DataModel::TYPE_BOOL,
		form_field_label: 'Disable canonical URL'
	)]
	protected bool $seo_disable_canonical = false;

	#[DataModel_Definition(
		type: DataModel::TYPE_STRING,
		max_len: 255,
		form_field_type: false
	)]
	protected string $url_param = '';

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
	protected string $image_pictogram_filter = '';
	
	#[DataModel_Definition(
		type: DataModel::TYPE_STRING,
		max_len: 255,
		form_field_type: false
	)]
	protected string $image_pictogram_product_detail = '';

	#[DataModel_Definition(
		type: DataModel::TYPE_STRING,
		max_len: 255,
		form_field_type: false
	)]
	protected string $image_pictogram_product_listing = '';


	public function getSticker() : Sticker
	{
		return $this->sticker;
	}

	public function setParents( Sticker $sticker ) : void
	{
		$this->sticker = $sticker;
		$this->sticker_id = $sticker->getId();
	}

	public function getName() : string
	{
		return $this->name;
	}

	public function setName( string $name ) : void
	{
		$this->name = $name;
		$this->generateUrlParam();
	}

	public function getSecondName() : string
	{
		return $this->second_name;
	}

	public function setSecondName( string $second_name ) : void
	{
		$this->second_name = $second_name;
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

	public function getUrlParam() : string
	{
		return $this->url_param;
	}

	public function getURL() : string
	{
		return Shops::getURL( $this->shop_code, [$this->url_param] );
	}

	public function generateUrlParam() : void
	{
		if(!$this->sticker_id) {
			return;
		}

		$this->url_param = Shops::generateURLPathPart( $this->name, '', $this->sticker_id, $this->shop_code );
	}

	public function getImageEntity() : string
	{
		return 'sticker';
	}

	public function getImageObjectId() : int|string
	{
		return $this->sticker_id;
	}

	public static function getImageClasses() : array
	{
		return [
			Sticker_ShopData::IMG_PICTOGRAM_FILTER => Tr::_('Pictogram - Filter', [], Sticker::getManageModuleName() ),
			Sticker_ShopData::IMG_PICTOGRAM_PRODUCT_DETAIL => Tr::_('Pictogram - Product detail', [], Sticker::getManageModuleName() ),
			Sticker_ShopData::IMG_PICTOGRAM_PRODUCT_LISTING => Tr::_('Pictogram - Product listing', [], Sticker::getManageModuleName() ),
		];
	}

	public function getPossibleToEditImages() : bool
	{
		return !$this->sticker->getEditForm()->getIsReadonly();
	}


	public function setImageFilter( string $image ) : void
	{
		$this->setImage( Sticker_ShopData::IMG_PICTOGRAM_FILTER, $image);
	}

	public function getImageFilter() : string
	{
		return $this->getImage( Sticker_ShopData::IMG_PICTOGRAM_FILTER );
	}

	public function getImageFilterUrl() : string
	{
		return $this->getImageUrl( Sticker_ShopData::IMG_PICTOGRAM_FILTER );
	}

	public function getImageFilterThumbnailUrl( int $max_w, int $max_h ) : string
	{
		return $this->getImageThumbnailUrl( Sticker_ShopData::IMG_PICTOGRAM_FILTER, $max_w, $max_h );
	}

	public function setImageProductDetail( string $image ) : void
	{
		$this->setImage( Sticker_ShopData::IMG_PICTOGRAM_PRODUCT_DETAIL, $image);
	}

	public function getImageProductDetail() : string
	{
		return $this->getImage( Sticker_ShopData::IMG_PICTOGRAM_PRODUCT_DETAIL );
	}

	public function getImageProductDetailUrl() : string
	{
		return $this->getImageUrl( Sticker_ShopData::IMG_PICTOGRAM_PRODUCT_DETAIL );
	}

	public function getImageProductDetailThumbnailUrl( int $max_w, int $max_h ) : string
	{
		return $this->getImageThumbnailUrl( Sticker_ShopData::IMG_PICTOGRAM_PRODUCT_DETAIL, $max_w, $max_h );
	}

	public function setImageProductListing( string $image ) : void
	{
		$this->setImage( Sticker_ShopData::IMG_PICTOGRAM_PRODUCT_LISTING, $image);
	}

	public function getImageProductListing() : string
	{
		return $this->getImage( Sticker_ShopData::IMG_PICTOGRAM_PRODUCT_LISTING );
	}

	public function getImageProductListingUrl() : string
	{
		return $this->getImageUrl( Sticker_ShopData::IMG_PICTOGRAM_PRODUCT_LISTING );
	}

	public function getImageProductListingThumbnailUrl( int $max_w, int $max_h ) : string
	{
		return $this->getImageThumbnailUrl( Sticker_ShopData::IMG_PICTOGRAM_PRODUCT_LISTING, $max_w, $max_h );
	}
	
}