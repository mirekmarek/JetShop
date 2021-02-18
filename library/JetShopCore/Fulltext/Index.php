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

	#[DataModel_Definition(
		type: DataModel::TYPE_STRING,
		max_len: 100,
		is_id: true
	)]
	protected string $shop_code = '';

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

	public function getShopCode() : string
	{
		return $this->shop_code;
	}

	public function setShopCode( string $shop_code ) : void
	{
		$this->shop_code = $shop_code;
	}

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
	protected function _collectWords( array $texts, $word_class_name, callable $index_word_setup) : array
	{
		$words = Fulltext_Dictionary::collectWords( $this->shop_code, $texts );
		$this->words = implode(' ', $words);

		$result = [];
		foreach( $words as $word ) {
			/**
			 * @var Fulltext_Index_Word $w
			 */
			$w = new $word_class_name();
			$w->setShopCode( $this->shop_code );
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
		$object_id = (int)$object_id;
		$class = get_called_class();

		/**
		 * @var Fulltext_Index $class
		 */
		$table_index = $class::getIndexDatabaseTableName();
		$table_index_words = $class::getIndexWordsDatabaseTableName();

		$db = Db::get();
		$db->execCommand( "DELETE FROM $table_index WHERE object_id=".$object_id );
		$db->execCommand( "DELETE FROM $table_index_words WHERE object_id=".$object_id );
	}


	/**
	 * @param bool|string $shop_code
	 * @param string $search_string
	 * @param string $sql_query_where
	 *
	 * @return mixed
	 */
	public static function searchObjectIds(
		bool|string $shop_code,
		string $search_string,
		string $sql_query_where
	) : mixed {
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

		if($shop_code) {
			$sql_query_where .= " AND shop_code='".addslashes($shop_code)."'";
		}

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

			if(!in_array($id, $matches[$word])) {
				$matches[$word][] = $id;
			}
		}

		$ids = [];

		if(count($matches)>1) {
			foreach( $matches as $q_string=>$result ) {
				if(!$result) {
					return [];
				}

				$ids[] = $result;

			}


			$ids = call_user_func_array('array_intersect', $ids);
		} else {
			foreach( $matches as $q_string=>$result ) {
				$ids = $result;
			}
		}

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