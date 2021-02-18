<?php
namespace JetShop;

use Jet\DataModel;
use Jet\DataModel_Definition;
use JetShopAdmin\Shop_Admin;

#[DataModel_Definition(
	name: 'index_internal_category',
	database_table_name: 'fulltext_internal_categories'
)]
abstract class Core_Fulltext_Index_Internal_Category extends Fulltext_Index {

	#[DataModel_Definition(
		type: DataModel::TYPE_STRING,
		max_len: 100,
		is_key: true
	)]
	protected string $category_type = '';

	#[DataModel_Definition(
		type: DataModel::TYPE_BOOL,
		is_key: true
	)]
	protected bool $category_is_active = false;

	public function getCategoryType() : string
	{
		return $this->category_type;
	}

	public function setCategoryType( string $category_type ) : void
	{
		$this->category_type = $category_type;
	}

	public function getCategoryIsActive() : bool
	{
		return $this->category_is_active;
	}

	public function setCategoryIsActive( string $category_is_active ) : void
	{
		$this->category_is_active = $category_is_active;
	}

	/**
	 * @param array $texts
	 * @param callable $index_word_setup
	 * @return Fulltext_Index_Internal_Category_Word[]
	 */
	public function collectWords( array $texts, callable $index_word_setup ) : array
	{
		return $this->_collectWords( $texts, __NAMESPACE__.'\\Fulltext_Index_Internal_Category_Word', $index_word_setup );
	}

	/**
	 * @param $search_string
	 * @param array $only_types
	 * @param bool $only_active
	 * @param int $exclude_branch_id
	 * @param null|string $shop_code
	 *
	 * @return Category[]
	 */
	public static function search( string $search_string, array $only_types=[], bool $only_active=false, int $exclude_branch_id=0, string|null $shop_code=null ) : array
	{
		if($shop_code===null) {
			$shop_code = Shops::getCurrentCode();
		}

		$sql_query_where = [];

		if($only_types) {
			foreach( $only_types as $i=>$type ) {
				$only_types[$i] = addslashes($type);
			}

			$sql_query_where[] = "category_type IN ('".implode("', '", $only_types)."')";
		}

		if($only_active) {
			$sql_query_where[] = "category_is_active=1";
		}

		$ids = static::searchObjectIds( $shop_code, $search_string, implode(' AND ', $sql_query_where) );

		if(!$ids) {
			return [];
		}

		/**
		 * @var Category[] $result
		 */
		$result = Category::fetch([
			'categories' => ['id' => $ids]
		]);

		if($exclude_branch_id) {
			foreach($result as $i=>$category) {

				$path = $category->getPath();
				if(in_array($exclude_branch_id, $path)) {
					unset($result[$i]);
					continue;
				}
			}
		}

		return $result;
	}

}
