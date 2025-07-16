<?php
/**
 * @copyright Copyright (c) Miroslav Marek <mirek.marek@web-jet.cz>
 * @license EUPL 1.2  https://eupl.eu/1.2/en/
 * @author Miroslav Marek <mirek.marek@web-jet.cz>
 */
namespace JetApplication;


use Jet\DataModel;
use Jet\DataModel_Definition;


#[DataModel_Definition(
	name: 'content_article_categories_assoc',
	database_table_name: 'content_article_categories_assoc'
)]
class Content_Article_EShopData_CategoryAssoc extends EShopEntity_WithEShopRelation
{
	
	#[DataModel_Definition(
		type: DataModel::TYPE_INT,
		is_id: true,
	)]
	protected int $article_id = 0;
	
	#[DataModel_Definition(
		type: DataModel::TYPE_INT,
		is_id: true,
	)]
	protected int $category_id = 0;
	
	public function getArticleId(): int
	{
		return $this->article_id;
	}
	
	public function setArticleId( int $article_id ): void
	{
		$this->article_id = $article_id;
	}
	
	public function getCategoryId(): int
	{
		return $this->category_id;
	}
	
	public function setCategoryId( int $category_id ): void
	{
		$this->category_id = $category_id;
	}
	
	public static function setAssoc( EShop $eshop, int $article_id, array $category_ids ) : void
	{
		foreach( $category_ids as $category_id ) {
			static::assoc( $eshop, $article_id, $category_id );
		}
		
		$current = static::getCategoryIds($eshop, $article_id);
		foreach( $current as $category_id ) {
			if(!in_array($category_id, $category_ids)) {
				static::load([
					$eshop->getWhere(),
					'AND',
					'article_id' => $article_id,
					'AND',
					'category_id' => $category_id,
				])?->delete();
				
			}
		}
	}
	
	public static function assoc( EShop $eshop, int $article_id, int $category_id) : void
	{
		$exists = static::load([
			$eshop->getWhere(),
			'AND',
			'article_id' => $article_id,
			'AND',
			'category_id' => $category_id,
		]);
		if($exists) {
			return;
		}
		
		$item = new static();
		$item->setEshop( $eshop );
		$item->setArticleId( $article_id );
		$item->setCategoryId( $category_id );
		$item->save();
	}
	
	public static function getCategoryIds( EShop $eshop, int $article_id ): array
	{
		return static::dataFetchCol(
			select: ['category_id'],
			where: [
				$eshop->getWhere(),
				'AND',
				'article_id' => $article_id,
			],
			raw_mode: true
		);
	}
	
	
	public static function getArticleIds( EShop $eshop, int|array $category_id ): array
	{
		if(!$category_id) {
			return [];
		}
		
		return static::dataFetchCol(
			select: ['article_id'],
			where: [
				$eshop->getWhere(),
				'AND',
				'category_id' => $category_id,
			],
			raw_mode: true
		);
	}
	
}