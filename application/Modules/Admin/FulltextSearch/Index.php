<?php
namespace JetApplicationModule\Admin\FulltextSearch;

use Jet\DataModel;
use Jet\DataModel_Definition;
use Jet\DataModel_IDController_Passive;
use JetApplication\Admin_FulltextSearch_IndexDataProvider;


#[DataModel_Definition(
	name: 'index',
	database_table_name: 'fulltext_internal',
	id_controller_class: DataModel_IDController_Passive::class
)]
class Index extends DataModel {
	
	#[DataModel_Definition(
		type: DataModel::TYPE_STRING,
		max_len: 50,
		is_id: true
	)]
	protected string $object_class = '';

	
	#[DataModel_Definition(
		type: DataModel::TYPE_STRING,
		max_len: 50,
		is_id: true
	)]
	protected string $object_id = '';
	
	#[DataModel_Definition(
		type: DataModel::TYPE_STRING,
		max_len: 50,
		is_key: true
	)]
	protected string $object_type = '';
	
	
	#[DataModel_Definition(
		type: DataModel::TYPE_BOOL,
		is_key: true
	)]
	protected bool $object_is_active = false;
	
	#[DataModel_Definition(
		type: DataModel::TYPE_STRING,
		max_len: 100
	)]
	protected string $object_title = '';
	

	#[DataModel_Definition(
		type: DataModel::TYPE_STRING,
		max_len: 65536
	)]
	protected string $words = '';

	protected static int $search_ids_count_limit = 200;
	
	public function __construct( string $object_class='' )
	{
		$this->object_class = $object_class;
	}
	
	
	public function getObjectId() : int
	{
		return $this->object_id;
	}

	public function setObjectId( int $object_id ) : void
	{
		$this->object_id = $object_id;
	}
	
	/**
	 * @return string
	 */
	public function getObjectClass(): string
	{
		return $this->object_class;
	}
	
	/**
	 * @param string $object_class
	 */
	public function setObjectClass( string $object_class ): void
	{
		$this->object_class = $object_class;
	}
	
	/**
	 * @return string
	 */
	public function getObjectType(): string
	{
		return $this->object_type;
	}
	
	public function setObjectType( string $object_type ): void
	{
		$this->object_type = $object_type;
	}
	
	public function getObjectIsActive(): bool
	{
		return $this->object_is_active;
	}
	
	public function setObjectIsActive( bool $object_is_active ): void
	{
		$this->object_is_active = $object_is_active;
	}
	
	public function getObjectTitle(): string
	{
		return $this->object_title;
	}
	
	public function setObjectTitle( string $object_title ): void
	{
		$this->object_title = $object_title;
	}
	
	

	public function getWords() : string
	{
		return $this->words;
	}

	public function setWords( array $words ) : void
	{
		$this->words = implode(' ', $words);
	}
	
	
	public static function deleteRecord( string $object_class, string $object_id ) : void
	{
		static::dataDelete([
			'object_class'=>$object_class,
			'AND',
			'object_id'=>$object_id
		]);
		
		Index_Word::dataDelete([
			'object_class'=>$object_class,
			'AND',
			'object_id'=>$object_id
		]);
	}
	
	
	/**
	 * @param string $object_class
	 * @param string $search_string
	 *
	 * @param array|null $object_type_filter
	 * @param bool|null $object_is_active_filter
	 *
	 * @return static[]
	 */
	public static function search(
		string $object_class,
		string $search_string,
		?array $object_type_filter=null,
		?bool $object_is_active_filter=null
	) : iterable {

		$search_string = Dictionary::prepareText( $search_string );
		$query = explode(' ',  $search_string );


		if(!$query) {
			return [];
		}

		
		$sql_query = [];
		$matches = [];
		
		$base_where = [
			'object_class' => $object_class,
		];
		
		foreach( $query as $q_string ) {
			
			$_where = $base_where;
			$_where[] = 'AND';
			$_where['word*'] = $q_string.'%';

			$matches[$q_string] = Index_Word::dataFetchCol(
				select:['object_id'],
				where: $_where,
				raw_mode: true
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

		if(!$ids) {
			return [];
		}
		
		$where = [
			'object_class' => $object_class,
			'AND',
			'object_id' => $ids
		];
		
		if($object_is_active_filter!==null) {
			$where[] = 'AND';
			$where['object_is_active'] = $object_is_active_filter;
		}
		
		if($object_type_filter!==null) {
			$where[] = 'AND';
			$where['object_type'] = $object_type_filter;
		}
		
		$res = static::fetchInstances($where);
		
		$res->getQuery()->setOrderBy('+object_title');
		$res->getQuery()->setLimit(0, static::$search_ids_count_limit);
		
		return $res;
	}
	
	public static function addIndex( Admin_FulltextSearch_IndexDataProvider $object ) : void
	{
		static::updateIndex($object);
	}
	
	public static function deleteIndex( Admin_FulltextSearch_IndexDataProvider $object ) : void
	{
		static::deleteRecord(
			object_class: $object->getAdminFulltextObjectClass(),
			object_id: $object->getAdminFulltextObjectId()
		);
	}
	
	public static function updateIndex( Admin_FulltextSearch_IndexDataProvider $object ) : void
	{
		
		$index = static::load([
			'object_class' => $object->getAdminFulltextObjectClass(),
			'AND',
			'object_id' => $object->getAdminFulltextObjectId()
		]);
		
		if(!$index) {
			$index = new static( $object->getAdminFulltextObjectClass() );
			$index->setObjectId( $object->getAdminFulltextObjectId() );
			$index->setObjectType( $object->getAdminFulltextObjectType() );
			$index->setObjectTitle( $object->getAdminFulltextObjectTitle() );
			$index->setObjectIsActive( $object->getAdminFulltextObjectIsActive() );
			
			$words = Dictionary::collectWords( $object->getAdminFulltextTexts() );
			
			$index->setWords( $words );
			
			$index->save();
			
			foreach( $words as $word ) {
				$w = new Index_Word();
				$w->setObjectId( $index->object_id );
				$w->setObjectClass( $index->object_class );
				$w->setWord( $word );
				
				$w->save();
			}
			
			return;
		}
		
		$update = false;
		$update_words = false;
		
		if($index->getObjectType()!=$object->getAdminFulltextObjectType()) {
			$index->setObjectType( $object->getAdminFulltextObjectType() );
			$update = true;
		}
		if($index->getObjectTitle()!=$object->getAdminFulltextObjectTitle()) {
			$index->setObjectTitle( $object->getAdminFulltextObjectTitle() );
			$update = true;
		}
		if($index->getObjectIsActive()!=$object->getAdminFulltextObjectIsActive()) {
			$index->setObjectIsActive( $object->getAdminFulltextObjectIsActive() );
			$update = true;
		}
		
		$old_words = $index->getWords();
		$words = Dictionary::collectWords( $object->getAdminFulltextTexts() );
		$index->setWords( $words );
		if($old_words!=$index->getWords()) {
			$update = true;
			$update_words = true;
		}
		
		if($update) {
			$index->save();
		}
		
		if($update_words) {
			Index_Word::dataDelete([
				'object_class'=>$index->getObjectClass(),
				'AND',
				'object_id'=>$index->getObjectId()
			]);
			
			foreach( $words as $word ) {
				$w = new Index_Word();
				$w->setObjectId( $index->object_id );
				$w->setObjectClass( $index->object_class );
				$w->setWord( $word );
				
				$w->save();
			}
		}
		
		
	}
	
	
}