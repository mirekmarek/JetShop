<?php
namespace JetShop;

use Jet\DataModel;
use Jet\DataModel_Definition;

#[DataModel_Definition(
	name: 'index_internal_category',
	database_table_name: 'fulltext_internal_categories'
)]
abstract class Core_Fulltext_Index_Internal_Category extends Fulltext_Index {

	#[DataModel_Definition(
		type: DataModel::TYPE_BOOL,
		is_key: true
	)]
	protected bool $category_is_active = false;

	public function getCategoryIsActive() : bool
	{
		return $this->category_is_active;
	}

	public function setCategoryIsActive( string $category_is_active ) : void
	{
		$this->category_is_active = $category_is_active;
	}
	
	public static function getWordClassName() : string
	{
		return Fulltext_Index_Internal_Category_Word::class;
	}

	/**
	 * @param array $texts
	 * @param callable $index_word_setup
	 * @return Fulltext_Index_Internal_Category_Word[]
	 */
	public function collectWords( array $texts, callable $index_word_setup ) : array
	{
		return $this->_collectWords( $texts, $index_word_setup );
	}

	/**
	 * @param string $search_string
	 * @param array $only_types
	 * @param bool $only_active
	 * @param int $exclude_branch_id
	 * @param ?Shops_Shop $shop
	 *
	 * @return Category[]
	 */
	public static function search( string $search_string, array $only_types=[], bool $only_active=false, int $exclude_branch_id=0, ?Shops_Shop $shop = null ) : array
	{
		if($shop===null) {
			$shop = Shops::getCurrent();
		}

		$where = [];

		if($only_types) {
			$where['category_type'] = $only_types;
		}

		if($only_active) {
			if($where) {
				$where[] = 'AND';
			}
			$where['category_is_active'] = true;
		}

		$ids = static::searchObjectIds( $shop, $search_string, $where );

		if(!$ids) {
			return [];
		}

		$result = Category::fetch(
			where_per_model: [
				'category' => ['id' => $ids],
				'category_shop_data' => Shops::getCurrent()->getWhere()
			],
			load_filter: [
				'category.*',
				'category_shop_data.*'
			]
		);

		if($exclude_branch_id) {
			foreach($result as $i=>$category) {

				$path = $category->getPath();
				if(in_array($exclude_branch_id, $path)) {
					unset($result[$i]);
				}
			}
		}

		return $result;
	}
	
	
	public static function addIndex( Category $category ) : void
	{
		foreach( Shops::getList() as $shop ) {
			
			$shop_data = $category->getShopData( $shop );
			
			$internal_index = new Fulltext_Index_Internal_Category();
			$internal_index->setShop( $shop );
			$internal_index->setObjectId( $category->getId() );
			$internal_index->setCategoryIsActive( $shop_data->isActive() );
			
			$words = $internal_index->collectWords(
				[
					$shop_data->getName(),
					$shop_data->getSecondName(),
					$shop_data->getSeoH1(),
					$shop_data->getSeoTitle(),
					$shop_data->getSeoKeywords(),
					$shop_data->getInternalFulltextKeywords()
				],
				function( Fulltext_Index_Internal_Category_Word $word ) use ($category, $shop_data) {
					$word->setCategoryIsActive( $shop_data->isActive() );
				}
			);
			
			$internal_index->save();
			foreach( $words as $word ) {
				$word->save();
			}
			
			//TODO: index for e-shop
		}
	}
	
	public static function deleteIndex( Category $category ) : void
	{
		Fulltext_Index_Internal_Category::deleteRecord( $category->getId() );
	}
	
	public static function updateIndex( Category $category ) : void
	{
		static::deleteIndex( $category );
		static::addIndex( $category );
	}
	
	
}
