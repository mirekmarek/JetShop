<?php
namespace JetShop;

use Jet\DataModel;
use Jet\DataModel_Definition;
use Jet\DataModel_IDController_Passive;
use Jet\Form_Definition;
use Jet\Form_Field;
use JetApplication\Brand;
use JetApplication\Entity_WithShopData_ShopData;
use JetApplication\Shops_Shop;

/**
 *
 */
#[DataModel_Definition(
	name: 'brands_shop_data',
	database_table_name: 'brands_shop_data',
	id_controller_class: DataModel_IDController_Passive::class,
	parent_model_class: Brand::class
)]
abstract class Core_Brand_ShopData extends Entity_WithShopData_ShopData {
	

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
	#[Form_Definition(
		type: Form_Field::TYPE_INPUT,
		label: 'URL parameter:',
	)]
	protected string $url_param = '';


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
	
	
	public static function getNameMap( Shops_Shop $shop ) : array
	{
		$where = $shop->getWhere();
		
		return static::dataFetchPairs(
			select: [
				'entity_id',
				'name'
			],
			where: $where,
			raw_mode: true
		);
	}
	
	public function getName() : string
	{
		return $this->name;
	}

	public function setName( string $name ) : void
	{
		if($this->name==$name) {
			return;
		}
		$this->name = $name;
		$this->url_param = $this->_generateURLParam( $this->name );
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


	public function getUrlParam() : string
	{
		return $this->url_param;
	}
	
	public function setImageLogo( string $image ) : void
	{
		$this->image_logo = $image;
	}
	
	public function getImageLogo() : string
	{
		return $this->image_logo;
	}
	
	public function setImageBigLogo( string $image ) : void
	{
		$this->image_big_logo = $image;
	}
	
	public function getImageBigLogo() : string
	{
		return $this->image_big_logo;
	}
	
	public function setImageTitle( string $image ) : void
	{
		$this->image_title = $image;
	}
	
	public function getImageTitle() : string
	{
		return $this->image_title;
	}
}