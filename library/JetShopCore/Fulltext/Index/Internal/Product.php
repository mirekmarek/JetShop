<?php
namespace JetShop;

use Jet\DataModel;
use Jet\DataModel_Definition;
use JetShopAdmin\Shop_Admin;

#[DataModel_Definition(
	name: 'index_internal_product',
	database_table_name: 'fulltext_internal_products'
)]
abstract class Core_Fulltext_Index_Internal_Product extends Fulltext_Index {

	#[DataModel_Definition(
		type: DataModel::TYPE_STRING,
		max_len: 100,
		is_key: true
	)]
	protected string $product_type = '';

	#[DataModel_Definition(
		type: DataModel::TYPE_BOOL,
		is_key: true
	)]
	protected string $product_is_active = '';

	public function getProductType(): string
	{
		return $this->product_type;
	}

	public function setProductType( string $category_type ) : void
	{
		$this->product_type = $category_type;
	}

	public function getProductIsActive() : string
	{
		return $this->product_is_active;
	}

	public function setProductIsActive( string $category_is_active ) : void
	{
		$this->product_is_active = $category_is_active;
	}

	/**
	 * @param array $texts
	 * @param callable $index_word_setup
	 *
	 * @return Fulltext_Index_Internal_Product_Word[]
	 */
	public function collectWords( array $texts, callable $index_word_setup ) : array
	{
		return $this->_collectWords( $texts, __NAMESPACE__.'\\Fulltext_Index_Internal_Product_Word', $index_word_setup );
	}

	/**
	 * @param $search_string
	 * @param array $only_types
	 * @param bool $only_active
	 * @param int $exclude_id
	 * @param null|string $shop_id
	 *
	 * @return Product[]
	 */
	public static function search( string $search_string,
	                               array $only_types=[],
	                               bool $only_active=false,
	                               int $exclude_id=0,
	                               string|null $shop_id=null ) : array
	{
		if($shop_id===null) {
			$shop_id = Shops::getCurrentId();
		}

		$sql_query_where = [];

		if($only_types) {
			foreach( $only_types as $i=>$type ) {
				$only_types[$i] = addslashes($type);
			}

			$sql_query_where[] = "product_type IN ('".implode("', '", $only_types)."')";
		}

		if($only_active) {
			$sql_query_where[] = "product_is_active=1";
		}

		$ids = static::searchObjectIds( $shop_id, $search_string, implode(' AND ', $sql_query_where) );

		if(!$ids) {
			return [];
		}

		if($exclude_id) {
			if(($s = array_search( $exclude_id, $ids ))!==false) {
				unset($ids[$s]);
			}

		}

		/**
		 * @var Product[] $result
		 */
		$result = Product::fetch([
			'products' => ['id' => $ids]
		]);


		return $result;
	}

}
