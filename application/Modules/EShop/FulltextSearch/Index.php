<?php
namespace JetApplicationModule\EShop\FulltextSearch;

use Jet\DataModel;
use Jet\DataModel_Definition;
use Jet\DataModel_IDController_Passive;
use Jet\Locale;
use JetApplication\FulltextSearch_Dictionary;
use JetApplication\FulltextSearch_IndexDataProvider;
use JetApplication\EShops;
use JetApplication\EShop;


#[DataModel_Definition(
	name: 'index',
	database_table_name: 'fulltext_eshop',
	id_controller_class: DataModel_IDController_Passive::class,
)]
class Index extends DataModel
{
	protected ?EShop $_eshop = null;
	
	#[DataModel_Definition(
		type: DataModel::TYPE_STRING,
		max_len: 100,
		is_key: true,
		is_id: true,
	)]
	protected string $eshop_code = '';
	
	#[DataModel_Definition(
		type: DataModel::TYPE_LOCALE,
		is_key: true,
		is_id: true,
	)]
	protected ?Locale $locale = null;
	
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
		max_len: 65536
	)]
	protected string $words = '';

	protected static int $search_ids_count_limit = 200;
	
	public function __construct()
	{
	}
	
	public function setEshop( EShop $eshop ) : void
	{
		$this->eshop_code = $eshop->getCode();
		$this->locale = $eshop->getLocale();
		$this->_eshop = $eshop;
	}
	
	public function getEshopCode() : string
	{
		return $this->eshop_code;
	}
	
	public function getLocale(): ?Locale
	{
		return $this->locale;
	}
	
	public function getEshop() : EShop
	{
		if(!$this->_eshop) {
			$this->_eshop = EShops::get( $this->getShopKey() );
		}
		
		return $this->_eshop;
	}
	
	public function getShopKey() : string
	{
		return $this->eshop_code.'_'.$this->locale;
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
	 * @param EShop $eshop
	 * @param string $entity_type
	 * @param string $search_string
	 *
	 * @return array
	 */
	public static function search(
		EShop  $eshop,
		string $entity_type,
		string $search_string
	) : iterable {

		$search_string = FulltextSearch_Dictionary::prepareText( $search_string );
		$query = explode(' ',  $search_string );


		if(!$query) {
			return [];
		}

		
		$sql_query = [];
		$matches = [];
		
		$base_where = $eshop->getWhere();
		$base_where[] = 'AND';
		$base_where['entity_type'] = $entity_type;
		
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
		
		return $ids;
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
		
		foreach( EShops::getList() as $eshop ) {
			static::_updateIndex( $eshop, $object );
		}
		
	}
	
	
	protected static function _updateIndex( EShop $eshop, FulltextSearch_IndexDataProvider $object ) : void
	{
		$where = $eshop->getWhere();
		$where[] = 'AND';
		$where[] = [
			'entity_type' => $object::getEntityType(),
			'AND',
			'object_id' => $object->getId()
		];
		
		$index = static::load( $where );
		
		if(!$index) {
			$index = new static();
			$index->setEntityType( $object::getEntityType() );
			$index->setEshop( $eshop );
			$index->setObjectId( $object->getId() );
			
			$new_words = $object->getShopFulltextTexts( $eshop );
			
			if($new_words) {
				$new_words = FulltextSearch_Dictionary::collectWords( $new_words );
				
				$index->setWords( $new_words );
				
				$index->save();
				
				foreach( $new_words as $word ) {
					$w = new Index_Word();
					$w->setEshop( $eshop );
					$w->setObjectId( $index->object_id );
					$w->setEntityType( $index->entity_type );
					$w->setWord( $word );
					
					$w->save();
				}
				
			}
			
			return;
		}
		
		
		$old_words = $index->getWords();
		
		$new_words = $object->getInternalFulltextTexts();
		
		if(!$new_words) {
			Index_Word::dataDelete($where);
			$index->delete();
			
			return;
		}
		
		$update_words = false;
		
		$new_words = FulltextSearch_Dictionary::collectWords( $new_words );
		
		if($old_words!=$new_words) {
			$index->setWords( $new_words );
			$index->save();
			
			Index_Word::dataDelete($where);
			
			foreach( $new_words as $word ) {
				$w = new Index_Word();
				$w->setEshop( $eshop );
				$w->setObjectId( $index->object_id );
				$w->setEntityType( $index->entity_type );
				$w->setWord( $word );
				
				$w->save();
			}
		}
		
		
		
	}
	
}