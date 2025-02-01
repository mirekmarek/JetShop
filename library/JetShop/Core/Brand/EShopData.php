<?php
/**
 * @copyright Copyright (c) Miroslav Marek <mirek.marek@web-jet.cz>
 * @license EUPL 1.2  https://eupl.eu/1.2/en/
 * @author Miroslav Marek <mirek.marek@web-jet.cz>
 */
namespace JetShop;


use Jet\DataModel;
use Jet\DataModel_Definition;
use Jet\DataModel_IDController_Passive;
use Jet\Form_Definition;
use Jet\Form_Field;
use JetApplication\EShopEntity_HasImages_Interface;
use JetApplication\EShopEntity_HasImages_Trait;
use JetApplication\EShopEntity_HasURL_Interface;
use JetApplication\EShopEntity_HasURL_Trait;
use JetApplication\EShopEntity_Definition;
use JetApplication\Brand;
use JetApplication\EShopEntity_WithEShopData_EShopData;
use JetApplication\EShop;

/**
 *
 */
#[DataModel_Definition(
	name: 'brands_eshop_data',
	database_table_name: 'brands_eshop_data',
	id_controller_class: DataModel_IDController_Passive::class,
	parent_model_class: Brand::class
)]
#[EShopEntity_Definition(
	URL_template: '%NAME%-m-%ID%'
)]
abstract class Core_Brand_EShopData extends EShopEntity_WithEShopData_EShopData implements
	EShopEntity_HasURL_Interface,
	EShopEntity_HasImages_Interface
{
	
	use EShopEntity_HasURL_Trait;
	use EShopEntity_HasImages_Trait;

	#[DataModel_Definition(
		type: DataModel::TYPE_STRING,
		max_len: 100,
	)]
	#[Form_Definition(
		type: Form_Field::TYPE_INPUT,
		label: 'Name:',
	)]
	#[EShopEntity_Definition(
		is_description: true,
		setter: 'setName'
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
	#[EShopEntity_Definition(
		is_description: true,
		setter: 'setSecondName'
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
	#[EShopEntity_Definition(
		is_description: true,
		setter: 'setDescription'
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
	#[EShopEntity_Definition(
		is_description: true,
		setter: 'setSeoTitle'
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
		is_description: true,
		setter: 'setSeoDescription'
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
		is_description: true,
		setter: 'setSeoKeywords'
	)]
	protected string $seo_keywords = '';

	#[DataModel_Definition(
		type: DataModel::TYPE_STRING,
		max_len: 255,
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
	
	public function getURLNameDataSource(): string
	{
		return $this->url_param ? : $this->name;
	}
	
	public static function getNameMap( EShop $eshop ) : array
	{
		$where = $eshop->getWhere();
		
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
		$this->url_param = $this->generateURLParam( $this->name );
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
	
	public function setUrlParam( string $url_param ): void
	{
		$this->url_param = $url_param;
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