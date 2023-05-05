<?php
namespace JetShop;

use Jet\DataModel;
use Jet\DataModel_Definition;

use JetApplication\Fulltext_Index;
use JetApplication\Shops;
use JetApplication\Shops_Shop;
use JetApplication\Product;
use JetApplication\Fulltext_Index_Internal_Product;
use JetApplication\Fulltext_Index_Internal_Product_Word;

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
	
	public static function getWordClassName() : string
	{
		return Fulltext_Index_Internal_Product_Word::class;
	}

	/**
	 * @param array $texts
	 * @param callable $index_word_setup
	 *
	 * @return Fulltext_Index_Internal_Product_Word[]
	 */
	public function collectWords( array $texts, callable $index_word_setup ) : array
	{
		return $this->_collectWords( $texts, $index_word_setup );
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

		$where = [];
		if($only_active) {
			$where['product_is_active'] = true;
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
			$ids = static::searchObjectIds( $shop, $search_string, $where );
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
	
	public static function addIndex( Product $product ) : void
	{
		foreach( Shops::getList() as $shop ) {
			$shop_data = $product->getShopData( $shop );
			
			$internal_index = new Fulltext_Index_Internal_Product();
			$internal_index->setShop( $shop );
			$internal_index->setObjectId( $product->getId() );
			$internal_index->setProductType( $product->getType() );
			$internal_index->setProductIsActive( $product->isActive() && $shop_data->isActive() );
			
			$words = $internal_index->collectWords(
				[
					$shop_data->getName(),
					$shop_data->getVariantName(),
					$shop_data->getSeoH1(),
					$shop_data->getSeoTitle(),
					$shop_data->getSeoKeywords(),
					$shop_data->getInternalFulltextKeywords()
				],
				function( Fulltext_Index_Internal_Product_Word $word ) use ($product, $shop_data) {
					$word->setProductType( $product->getType() );
					$word->setProductIsActive( $shop_data->isActive() );
				}
			);
			
			$internal_index->save();
			foreach( $words as $word ) {
				$word->save();
			}
		}
	}
	
	public static function deleteIndex( Product $product ) : void
	{
		Fulltext_Index_Internal_Product::deleteRecord( $product->getId() );
	}
	
	public static function updateIndex( Product $product ) : void
	{
		Fulltext_Index_Internal_Product::deleteIndex( $product );
		Fulltext_Index_Internal_Product::addIndex( $product );
	}
	
}
