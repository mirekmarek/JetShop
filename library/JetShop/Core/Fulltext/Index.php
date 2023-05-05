<?php
namespace JetShop;
use Jet\Data_Text;
use Jet\DataModel;
use Jet\DataModel_Definition;
use Jet\DataModel_IDController_Passive;

use JetApplication\CommonEntity_ShopRelationTrait_ShopIsId;
use JetApplication\Fulltext_Index_Internal_Category_Word;
use JetApplication\Fulltext_Index_Word;
use JetApplication\Fulltext_Dictionary;
use JetApplication\Fulltext_Index;
use JetApplication\Shops_Shop;

#[DataModel_Definition(
	name: '',
	database_table_name: '',
	id_controller_class: DataModel_IDController_Passive::class
)]
abstract class Core_Fulltext_Index extends DataModel {

	use CommonEntity_ShopRelationTrait_ShopIsId;

	#[DataModel_Definition(
		type: DataModel::TYPE_INT,
		is_id: true
	)]
	protected int $object_id = 0;

	#[DataModel_Definition(
		type: DataModel::TYPE_STRING,
		max_len: 65536
	)]
	protected string $words = '';

	protected static int $search_ids_count_limit = 200;


	public function getObjectId() : int
	{
		return $this->object_id;
	}

	public function setObjectId( int $object_id ) : void
	{
		$this->object_id = $object_id;
	}

	public function getWords() : string
	{
		return $this->words;
	}

	public function setWords( string $words ) : void
	{
		$this->words = $words;
	}

	/**
	 * @param array $texts
	 * @param callable $index_word_setup
	 *
	 * @return Fulltext_Index_Internal_Category_Word[]
	 */
	abstract public function collectWords( array $texts, callable $index_word_setup ) : array;
	
	abstract public static function getWordClassName() : string;

	/**
	 * @param array $texts
	 * @param callable $index_word_setup
	 *
	 * @return Fulltext_Index_Word[]
	 */
	protected function _collectWords( array $texts, callable $index_word_setup) : array
	{
		$word_class_name = static::getWordClassName();
		$words = Fulltext_Dictionary::collectWords( $this->getShop(), $texts );
		$this->words = implode(' ', $words);

		$result = [];
		foreach( $words as $word ) {
			/**
			 * @var Fulltext_Index_Word $w
			 */
			$w = new $word_class_name();
			$w->setShop( $this->getShop() );
			$w->setObjectId( $this->object_id );
			$w->setWord( $word );

			$index_word_setup( $w );

			$result[] = $w;
		}

		return $result;
	}


	/**
	 * @param int $object_id
	 */
	public static function deleteRecord( int $object_id ) : void
	{
		/**
		 * @var DataModel $word_class
		 */
		$word_class = static::getWordClassName();
		
		$words = $word_class::fetchInstances(['object_id'=>$object_id]);
		foreach($words as $w) {
			$w->delete();
		}
		
		$index = static::fetchInstances(['object_id'=>$object_id]);
		foreach($index as $i) {
			$i->delete();
		}
		
	}


	/**
	 * @param Shops_Shop $shop
	 * @param string $search_string
	 * @param array $where
	 *
	 * @return array
	 */
	public static function searchObjectIds(
		Shops_Shop $shop,
		string $search_string,
		array $where
	) : array {

		$search_string = Fulltext_Index::tidySearchString( $search_string );

		$search_string = preg_replace( '/([0-9]+)x([0-9]+)/', '$1 $2', $search_string);
		$search_string = preg_replace( '/([0-9]+)x$/', '$1', $search_string);
		$query = explode(' ',  $search_string );


		if(!$query) {
			return [];
		}
		
		/**
		 * @var DataModel $word_class
		 */
		$word_class = static::getWordClassName();
		
		$sql_query = [];
		$matches = [];
		
		$base_where = [
			'shop_code' => $shop->getShopCode(),
			'AND',
			'locale' => $shop->getLocale(),
		];
		
		if($where) {
			$base_where[] = 'AND';
			$base_where[] = $where;
		}

		foreach( $query as $q_string ) {
			
			$_where = $base_where;
			$_where[] = 'AND';
			$_where['word*'] = $q_string.'%';

			$matches[$q_string] = $word_class::dataFetchCol(
				select:['object_id'],
				where: $_where
			);
		}
		

		$ids = [];

		if(count($matches)>1) {
			foreach( $matches as $result ) {
				if(!$result) {
					return [];
				}

				$ids[] = $result;

			}


			$ids = call_user_func_array('array_intersect', $ids);
		} else {
			foreach( $matches as $result ) {
				$ids = $result;
			}
		}

		$ids = array_slice($ids, 0, static::$search_ids_count_limit );

		return $ids;
	}

	/**
	 * @param string $query
	 * @return string
	 */
	public static function tidySearchString( string $query ) : string
	{
		$query = Data_Text::removeAccents( $query );
		$query = strtolower( $query );

		$query = str_replace('/', ' ', $query);
		$query = str_replace(',', ' ', $query);
		$query = str_replace('-', ' ', $query);
		$query = str_replace('&', ' ', $query);
		$query = preg_replace('/[ ]{2}/', ' ', $query);


		return $query;

	}

}