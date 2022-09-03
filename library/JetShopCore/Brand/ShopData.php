<?php
namespace JetShop;

use Jet\DataModel;
use Jet\DataModel_Definition;
use Jet\Form_Definition;
use Jet\Form_Field;
use Jet\DataModel_IDController_Passive;
use Jet\Tr;

/**
 *
 */
#[DataModel_Definition(
	name: 'brands_shop_data',
	database_table_name: 'brands_shop_data',
	id_controller_class: DataModel_IDController_Passive::class,
	parent_model_class: Brand::class
)]
abstract class Core_Brand_ShopData extends CommonEntity_ShopData implements Images_ShopDataInterface {

	use Images_ShopDataTrait;

	const IMG_LOGO = 'logo';
	const IMG_BIG_LOGO = 'big_logo';
	const IMG_TITLE = 'title';

	#[DataModel_Definition(
		type: DataModel::TYPE_INT,
		is_id: true,
		related_to: 'main.id',
	)]
	protected int $brand_id = 0;
	
	protected ?Brand $brand = null;

	#[DataModel_Definition(
		type: DataModel::TYPE_STRING,
		max_len: 100,
	)]
	#[Form_Definition(
		type: Form_Field::TYPE_INPUT,
		label: 'Name:',
	)]
	protected string $name = '';

	#[DataModel_Definition(
		type: DataModel::TYPE_STRING,
		max_len: 100,
	)]
	#[Form_Definition(
		type: Form_Field::TYPE_INPUT,
		label: 'Alternative name:',
	)]
	protected string $second_name = '';

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
		label: 'H1:',
	)]
	protected string $seo_h1 = '';

	#[DataModel_Definition(
		type: DataModel::TYPE_STRING,
		max_len: 255,
	)]
	#[Form_Definition(
		type: Form_Field::TYPE_INPUT,
		label: 'Title:',
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
		max_len: 255,
	)]
	protected string $url_param = '';

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
		max_len: 255,
	)]
	protected string $image_logo = '';

	#[DataModel_Definition(
		type: DataModel::TYPE_STRING,
		max_len: 255,
	)]
	protected string $image_big_logo = '';

	#[DataModel_Definition(
		type: DataModel::TYPE_STRING,
		max_len: 255,
	)]
	protected string $image_title = '';


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

	public function setSeoDescription(  string $seo_description ) : void
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

	public function generateUrlParam() : void
	{
		$this->url_param = Shops::generateURLPathPart( $this->name, '', 0, $this->getShop() );
	}

	public function getImageEntity() : string
	{
		return 'brand';
	}

	public function getImageObjectId() : int|string
	{
		return $this->brand_id;
	}

	public static function getImageClasses() : array
	{
		return [
			Brand_ShopData::IMG_LOGO => Tr::_('Logo', [], Brand::getManageModuleName() ),
			Brand_ShopData::IMG_BIG_LOGO => Tr::_('Big logo', [], Brand::getManageModuleName() ),
			Brand_ShopData::IMG_TITLE => Tr::_('Title image', [], Brand::getManageModuleName() ),
		];
	}

	public function setImageLogo( string $image ) : void
	{
		$this->setImage( Brand_ShopData::IMG_LOGO, $image);
	}

	public function getImageLogo() : string
	{
		return $this->getImage( Brand_ShopData::IMG_LOGO );
	}

	public function getImageLogoUrl() : string
	{
		return $this->getImageUrl( Brand_ShopData::IMG_LOGO );
	}

	public function getImageLogoThumbnailUrl( int $max_w, int $max_h ) : string
	{
		return $this->getImageThumbnailUrl( Brand_ShopData::IMG_LOGO, $max_w, $max_h );
	}

	public function setImageBigLogo( string $image ) : void
	{
		$this->setImage( Brand_ShopData::IMG_BIG_LOGO, $image);
	}

	public function getImageBigLogo() : string
	{
		return $this->getImage( Brand_ShopData::IMG_BIG_LOGO );
	}

	public function getImageBigLogoUrl() : string
	{
		return $this->getImageUrl( Brand_ShopData::IMG_BIG_LOGO );
	}

	public function getImageBigLogoThumbnailUrl( int $max_w, int $max_h ) : string
	{
		return $this->getImageThumbnailUrl( Brand_ShopData::IMG_BIG_LOGO, $max_w, $max_h );
	}

	public function setImageTitle( string $image ) : void
	{
		$this->setImage( Brand_ShopData::IMG_TITLE, $image);
	}

	public function getImageTitle() : string
	{
		return $this->getImage( Brand_ShopData::IMG_TITLE );
	}

	public function getImageTitleUrl() : string
	{
		return $this->getImageUrl( Brand_ShopData::IMG_TITLE );
	}

	public function getImageTitleThumbnailUrl( int $max_w, int $max_h ) : string
	{
		return $this->getImageThumbnailUrl( Brand_ShopData::IMG_TITLE, $max_w, $max_h );
	}


}