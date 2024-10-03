<?php
/**
 *
 */

namespace JetShop;

use Jet\DataModel;
use Jet\DataModel_Definition;
use Jet\Form_Definition;
use Jet\Form_Field;
use JetApplication\Entity_WithShopData_ShopData;
use JetApplication\Content_Article_Author;


#[DataModel_Definition(
	name: 'content_article_author_shop_data',
	database_table_name: 'content_articles_authors_shop_data',
	parent_model_class: Content_Article_Author::class
)]
abstract class Core_Content_Article_Author_ShopData extends Entity_WithShopData_ShopData
{
	#[DataModel_Definition(
		type: DataModel::TYPE_STRING,
		max_len: 255,
	)]
	#[Form_Definition(
		type: Form_Field::TYPE_INPUT,
		label: 'Name:'
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