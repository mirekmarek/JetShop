<?php
namespace JetShop;

use Jet\DataModel;
use Jet\DataModel_Definition;

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

	public function setProductIsActive( string $product_is_active ) : void
	{
		$this->product_is_active = $product_is_active;
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
	 * @param string $search_string
	 * @param bool $only_active
	 * @param array $filter
	 * @param bool $only_ids
	 * @param ?Shops_Shop $shop
	 *
	 * @return Product[]
	 */
	public static function search( string $search_string,
	                               bool $only_active=false,
	                               array $filter=[],
	                               bool $only_ids=false,
	                               ?Shops_Shop $shop = null ) : array
	{

		if(!$search_string) {
			return [];
		}

		if(!$shop) {
			$shop = Shops::getCurrent();
		}

		$sql_query_where = [];
		if($only_active) {
			$sql_query_where[] = "product_is_active=1";
		}

		/** @noinspection PhpStatementHasEmptyBodyInspection */
		if($filter) {
			//TODO:
		}

		$ids = [];

		/** @noinspection PhpIfWithCommonPartsInspection */
		if(((int)$search_string)>0) {
			$_ids_by_code = Product::fetchIDs([
				'id' => (int)$search_string,
			]);
			foreach($_ids_by_code as $id) {
				$ids[] = (int)(string)$id;
			}

		} else {
			$_ids_by_code = Product::fetchIDs([
				'ean' => $search_string,
				'OR',
				'internal_code *' => $search_string.'%',
			]);

			foreach($_ids_by_code as $id) {
				$ids[] = (int)(string)$id;
			}
		}

		if(!$ids) {
			$ids = static::searchObjectIds( $shop, $search_string, implode(' AND ', $sql_query_where) );
		}


		if(!$ids) {
			return [];
		}

		if($only_ids) {
			return $ids;
		}

		$result = Product::fetch(
			where_per_model: [
				'products' => ['id' => $ids],
				'products_shop_data' => Shops::getCurrent()->getWhere()
			],
			item_key_generator: function(Product $item) {
				return $item->getId();
			},
			load_filter: [
				'products.*',
				'products_shop_data.name'
			]
		);


		return $result;
	}

}
