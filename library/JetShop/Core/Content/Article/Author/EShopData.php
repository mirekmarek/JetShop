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
use JetApplication\EShopEntity_Definition;
use JetApplication\EShopEntity_HasImages_Interface;
use JetApplication\EShopEntity_HasImages_Trait;
use JetApplication\EShopEntity_WithEShopData_EShopData;
use JetApplication\Content_Article_Author;


#[DataModel_Definition(
	name: 'content_article_author_eshop_data',
	database_table_name: 'content_articles_authors_eshop_data',
	parent_model_class: Content_Article_Author::class
)]
abstract class Core_Content_Article_Author_EShopData extends EShopEntity_WithEShopData_EShopData implements EShopEntity_HasImages_Interface
{
	use EShopEntity_HasImages_Trait;
	
	#[DataModel_Definition(
		type: DataModel::TYPE_STRING,
		max_len: 255,
	)]
	#[Form_Definition(
		type: Form_Field::TYPE_INPUT,
		label: 'Name:'
	)]
	#[EShopEntity_Definition(
		is_description: true,
		setter: 'setName'
	)]
	protected string $name = '';
	
	#[DataModel_Definition(
		type: DataModel::TYPE_STRING,
		max_len: 9999999,
	)]
	#[Form_Definition(
		type: Form_Field::TYPE_WYSIWYG,
		label: 'About author:'
	)]
	#[EShopEntity_Definition(
		is_description: true,
		setter: 'setAboutAuthor'
	)]
	protected string $about_author = '';
	
	#[DataModel_Definition(
		type: DataModel::TYPE_STRING,
		max_len: 255,
	)]
	protected string $image_avatar_1 = '';
	
	#[DataModel_Definition(
		type: DataModel::TYPE_STRING,
		max_len: 255,
	)]
	protected string $image_avatar_2 = '';
	
	public function getName(): string
	{
		return $this->name;
	}
	
	public function setName( string $name ): void
	{
		$this->name = $name;
	}
	
	public function setAboutAuthor( string $value ) : void
	{
		$this->about_author = $value;
	}
	
	public function getAboutAuthor() : string
	{
		return $this->about_author;
	}
	
	
	public function setImageAvatar1( string $image ) : void
	{
		$this->image_avatar_1 = $image;
	}
	
	public function getImageAvatar1() : string
	{
		return $this->image_avatar_1;
	}
	
	public function getImageAvatar1ThumbnailUrl( int $max_w, int $max_h ): string
	{
		return $this->getImageThumbnailUrl('avatar_1', $max_w, $max_h);
	}
	
	public function getImageAvatar1Url(): string
	{
		return $this->getImageUrl('avatar_1');
	}
	
	
	
	public function setImageAvatar2( string $image ) : void
	{
		$this->image_avatar_2 = $image;
	}
	
	public function getImageAvatar2() : string
	{
		return $this->image_avatar_2;
	}
	
	public function getImageAvatar2ThumbnailUrl( int $max_w, int $max_h ): string
	{
		return $this->getImageThumbnailUrl('avatar_2', $max_w, $max_h);
	}
	
	public function getImageAvatar2Url(): string
	{
		return $this->getImageUrl('avatar_2');
	}
	
	
}