<?php
namespace JetShop;

use Jet\DataModel;
use Jet\DataModel_Definition;
use Jet\DataModel_IDController_Passive;

#[DataModel_Definition(
	name: 'content_article_category',
	database_table_name: 'content_articles_categories',
	id_controller_class: DataModel_IDController_Passive::class
)]
abstract class Core_Content_Article_Category extends DataModel
{
	
	#[DataModel_Definition(
		type: DataModel::TYPE_INT,
		is_id: true,
		is_key: true,
	)]
	protected int $article_id = 0;
	
	#[DataModel_Definition(
		type: DataModel::TYPE_INT,
		is_id: true,
		is_key: true
	)]
	protected int $category_id = 0;
	
	#[DataModel_Definition(
		type: DataModel::TYPE_INT,
		is_key: true,
	)]
	protected int $priority = 0;

	
	public function getArrayKeyValue() : string
	{
		return $this->category_id;
	}
	
	public function getArticleId() : int
	{
		return $this->article_id;
	}
	
	public function setArticleId( int $article_id ) : void
	{
		$this->article_id = $article_id;
	}
	
	public function getCategoryId() : int
	{
		return $this->category_id;
	}
	
	public function setCategoryId( int $category_id ) : void
	{
		$this->category_id = $category_id;
	}
	
	
	public function getPriority(): int
	{
		return $this->priority;
	}
	
	public function setPriority( int $priority ): void
	{
		$this->priority = $priority;
	}
	
}