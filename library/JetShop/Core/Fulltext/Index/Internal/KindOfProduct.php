<?php
namespace JetShop;

use Jet\DataModel;
use Jet\DataModel_Definition;

use JetApplication\KindOfProduct;
use JetApplication\Fulltext_Index;
use JetApplication\Fulltext_Index_Internal_KindOfProduct_Word;
use JetApplication\Shops;
use JetApplication\Shops_Shop;
use JetApplication\Product;
use JetApplication\Fulltext_Index_Internal_KindOfProduct;


#[DataModel_Definition(
	name: 'index_internal_kind_of_product',
	database_table_name: 'fulltext_internal_kinds_of_products'
)]
abstract class Core_Fulltext_Index_Internal_KindOfProduct extends Fulltext_Index {
	
	#[DataModel_Definition(
		type: DataModel::TYPE_BOOL,
		is_key: true
	)]
	protected string $kind_is_active = '';


	public function getKindIsActive() : string
	{
		return $this->kind_is_active;
	}

	public function setKindIsActive( string $kind_is_active ) : void
	{
		$this->kind_is_active = $kind_is_active;
	}
	
	public static function getWordClassName() : string
	{
		return Fulltext_Index_Internal_KindOfProduct_Word::class;
	}

	/**
	 * @param array $texts
	 * @param callable $index_word_setup
	 *
	 * @return Fulltext_Index_Internal_KindOfProduct_Word[]
	 */
	public function collectWords( array $texts, callable $index_word_setup ) : array
	{
		return $this->_collectWords( $texts, $index_word_setup );
	}

	/**
	 * @param string $search_string
	 * @param bool $only_active
	 * @param bool $only_ids
	 * @param ?Shops_Shop $shop
	 *
	 * @return Product[]
	 */
	public static function search( string $search_string,
	                               bool $only_active=false,
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
			$where['kind_is_active'] = true;
		}
		
		$ids = [];

		if(((int)$search_string)>0) {
			$_ids_by_code = KindOfProduct::fetchIDs([
				'id' => (int)$search_string,
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

		$result = KindOfProduct::fetch(
			where_per_model: [
				'kind_of_product' => ['id' => $ids],
			],
			item_key_generator: function(KindOfProduct $item) {
				return $item->getId();
			},
			load_filter: [
				'kind_of_product.*'
			]
		);


		return $result;
	}
	
	public static function addIndex( KindOfProduct $kind ) : void
	{
		foreach( Shops::getList() as $shop ) {
			$shop_data = $kind->getShopData( $shop );
			
			$internal_index = new Fulltext_Index_Internal_KindOfProduct();
			$internal_index->setShop( $shop );
			$internal_index->setObjectId( $kind->getId() );
			$internal_index->setKindIsActive( $kind->isActive() && $shop_data->isActive() );
			
			$words = $internal_index->collectWords(
				[
					$kind->getInternalName(),
					$kind->getInternalNotes(),
				],
				function( Fulltext_Index_Internal_KindOfProduct_Word $word ) use ($kind, $shop_data) {
					$word->setKindIsActive( $shop_data->isActive() );
				}
			);
			
			$internal_index->save();
			foreach( $words as $word ) {
				$word->save();
			}
		}
	}
	
	public static function deleteIndex( KindOfProduct $kind ) : void
	{
		Fulltext_Index_Internal_KindOfProduct::deleteRecord( $kind->getId() );
	}
	
	public static function updateIndex( KindOfProduct $kind ) : void
	{
		Fulltext_Index_Internal_KindOfProduct::deleteIndex( $kind );
		Fulltext_Index_Internal_KindOfProduct::addIndex( $kind );
	}
	
}
