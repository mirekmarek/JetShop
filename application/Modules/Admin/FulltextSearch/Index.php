<?php
/**
 * @copyright Copyright (c) Miroslav Marek <mirek.marek@web-jet.cz>
 * @license EUPL 1.2  https://eupl.eu/1.2/en/
 * @author Miroslav Marek <mirek.marek@web-jet.cz>
 */
namespace JetApplicaTionModule\Admin\FulltextSearch;


use Jet\DataModel;
use Jet\DataModel_Definition;
use Jet\DataModel_IDController_Passive;
use JetApplication\FulltextSearch_Dictionary;
use JetApplication\FulltextSearch_IndexDataProvider;


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
	protected string $entity_type = '';

	
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
	
	public function __construct()
	{
	}
	
	
	public function getObjectId() : int
	{
		return $this->object_id;
	}

	public function setObjectId( int $object_id ) : void
	{
		$this->object_id = $object_id;
	}
	
	public function getEntityType(): string
	{
		return $this->entity_type;
	}
	
	public function setEntityType( string $entity_type ): void
	{
		$this->entity_type = $entity_type;
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
	
	
	public static function deleteRecord( string $entity_type, string $object_id ) : void
	{
		static::dataDelete([
			'entity_type'=>$entity_type,
			'AND',
			'object_id'=>$object_id
		]);
		
		Index_Word::dataDelete([
			'entity_type'=>$entity_type,
			'AND',
			'object_id'=>$object_id
		]);
	}
	
	
	/**
	 * @param string $entity_type
	 * @param string $search_string
	 *
	 * @param array|null $object_type_filter
	 * @param bool|null $object_is_active_filter
	 *
	 * @return static[]
	 */
	public static function search(
		string $entity_type,
		string $search_string,
		?array $object_type_filter=null,
		?bool $object_is_active_filter=null
	) : iterable {

		$search_string = FulltextSearch_Dictionary::prepareText( $search_string );
		$query = explode(' ',  $search_string );


		if(!$query) {
			return [];
		}

		
		$sql_query = [];
		$matches = [];
		
		$base_where = [
			'entity_type' => $entity_type,
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
			'entity_type' => $entity_type,
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
		
		
		$data = static::dataFetchAll(
			select: [
				'entity_type',
				'object_id',
				'object_type',
				'object_is_active',
				'object_title',
			],
			where: $where,
			limit: static::$search_ids_count_limit,
			raw_mode: true
		);
		
		$res = [];
		foreach($data as $d) {
			$i = new static();
			$i->entity_type = $d['entity_type'];
			$i->object_id = $d['object_id'];
			$i->object_type = $d['object_type'];
			$i->object_is_active = $d['object_is_active'];
			$i->object_title = $d['object_title'];
			
			$res[] = $i;
		}
		
		return $res;
	}
	
	public static function deleteIndex( FulltextSearch_IndexDataProvider $object ) : void
	{
		static::deleteRecord(
			entity_type: $object::getEntityType(),
			object_id: $object->getId()
		);
	}
	
	public static function updateIndex( FulltextSearch_IndexDataProvider $object ) : void
	{
		$index = static::load([
			'entity_type' => $object::getEntityType(),
			'AND',
			'object_id' => $object->getId()
		]);
		
		if(!$index) {
			$index = new static();
			$index->setEntityType( $object::getEntityType() );
			$index->setObjectId( $object->getId() );
			$index->setObjectType( $object->getFulltextObjectType() );
			$index->setObjectTitle( $object->getInternalFulltextObjectTitle() );
			$index->setObjectIsActive( $object->getFulltextObjectIsActive() );
			
			$words = FulltextSearch_Dictionary::collectWords( $object->getInternalFulltextTexts() );
			
			$index->setWords( $words );
			
			$index->save();
			
			foreach( $words as $word ) {
				$w = new Index_Word();
				$w->setObjectId( $index->object_id );
				$w->setEntitytype( $index->entity_type );
				$w->setWord( $word );
				
				$w->save();
			}
			
			return;
		}
		
		$update = false;
		$update_words = false;
		
		if($index->getObjectType()!=$object->getFulltextObjectType()) {
			$index->setObjectType( $object->getFulltextObjectType() );
			$update = true;
		}
		if($index->getObjectTitle()!=$object->getInternalFulltextObjectTitle()) {
			$index->setObjectTitle( $object->getInternalFulltextObjectTitle() );
			$update = true;
		}
		if($index->getObjectIsActive()!=$object->getFulltextObjectIsActive()) {
			$index->setObjectIsActive( $object->getFulltextObjectIsActive() );
			$update = true;
		}
		
		$old_words = $index->getWords();
		$words = FulltextSearch_Dictionary::collectWords( $object->getInternalFulltextTexts() );
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
				'entity_type'=>$index->getEntityType(),
				'AND',
				'object_id'=>$index->getObjectId()
			]);
			
			foreach( $words as $word ) {
				$w = new Index_Word();
				$w->setObjectId( $index->object_id );
				$w->setEntitytype( $index->entity_type );
				$w->setWord( $word );
				
				$w->save();
			}
		}
		
		
	}
	
	
}