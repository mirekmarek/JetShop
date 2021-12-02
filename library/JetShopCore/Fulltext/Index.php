<?php
namespace JetShop;
use Jet\Data_Text;
use Jet\DataModel;
use Jet\DataModel_Definition;
use Jet\DataModel_IDController_Passive;
use Jet\Db;

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

	public static function getIndexDatabaseTableName() : string
	{
		return DataModel::getDataModelDefinition( get_called_class() )->getDatabaseTableName();
	}

	public static function getIndexWordsDatabaseTableName() : string
	{
		return DataModel::getDataModelDefinition( get_called_class().'_Word' )->getDatabaseTableName();
	}

	/**
	 * @param array $texts
	 * @param callable $index_word_setup
	 *
	 * @return Fulltext_Index_Internal_Category_Word[]
	 */
	abstract public function collectWords( array $texts, callable $index_word_setup ) : array;

	/**
	 * @param array $texts
	 * @param string $word_class_name
	 * @param callable $index_word_setup
	 *
	 * @return Fulltext_Index_Word[]
	 */
	protected function _collectWords( array $texts, string $word_class_name, callable $index_word_setup) : array
	{
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
		$class = get_called_class();

		/**
		 * @var Fulltext_Index $class
		 */
		$table_index = $class::getIndexDatabaseTableName();
		$table_index_words = $class::getIndexWordsDatabaseTableName();

		$db = Db::get();
		$db->execute( "DELETE FROM $table_index WHERE object_id=".$object_id );
		$db->execute( "DELETE FROM $table_index_words WHERE object_id=".$object_id );
	}


	/**
	 * @param Shops_Shop $shop
	 * @param string $search_string
	 * @param string $sql_query_where
	 *
	 * @return array
	 */
	public static function searchObjectIds(
		Shops_Shop $shop,
		string $search_string,
		string $sql_query_where
	) : array {

		$search_string = Fulltext_Index::tidySearchString( $search_string );

		$search_string = preg_replace( '/([0-9]+)x([0-9]+)/', '$1 $2', $search_string);
		$search_string = preg_replace( '/([0-9]+)x$/', '$1', $search_string);
		$query = explode(' ',  $search_string );


		if(!$query) {
			return [];
		}

		$table = static::getIndexWordsDatabaseTableName();

		if($sql_query_where) {
			$sql_query_where = " AND ($sql_query_where)";
		}

		//TODO: SQL!
			$sql_query_where .= " AND shop_code='".addslashes($shop->getShopCode())."' AND locale='".addslashes($shop->getLocale())."'";

		$sql_query = [];
		$matches = [];

		foreach( $query as $q_string ) {
			$matches[$q_string] = [];


			$sql_q_string = addslashes( $q_string );

			$q = "SELECT '$sql_q_string' as word, object_id as id FROM $table WHERE
						word LIKE '$sql_q_string%' 
						$sql_query_where
					";


			$sql_query[] = $q;
		}

		$sql_query = implode( "\nUNION\n", $sql_query );

		$q_r = Db::get()->fetchAll( $sql_query );

		foreach( $q_r as $r ) {
			$word = $r['word'];
			$id = (int)$r['id'];

			//if(!in_array($id, $matches[$word]))
			{
				$matches[$word][$id] = $id;
			}
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