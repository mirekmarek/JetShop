<?php
namespace JetShop;

use Jet\DataModel;
use Jet\DataModel_Definition;

#[DataModel_Definition(
	name: 'index_internal_property',
	database_table_name: 'fulltext_internal_properties'
)]
abstract class Core_Fulltext_Index_Internal_Property extends Fulltext_Index {

	#[DataModel_Definition(
		type: DataModel::TYPE_STRING,
		max_len: 100,
		is_key: true
	)]
	protected string $property_type = '';

	#[DataModel_Definition(
		type: DataModel::TYPE_BOOL,
		is_key: true
	)]
	protected string $property_is_active = '';

	public function getPropertyType(): string
	{
		return $this->property_type;
	}

	public function setPropertyType( string $category_type ) : void
	{
		$this->property_type = $category_type;
	}

	public function getPropertyIsActive() : string
	{
		return $this->property_is_active;
	}

	public function setPropertyIsActive( string $property_is_active ) : void
	{
		$this->property_is_active = $property_is_active;
	}
	
	public static function getWordClassName() : string
	{
		return Fulltext_Index_Internal_Property_Word::class;
	}

	/**
	 * @param array $texts
	 * @param callable $index_word_setup
	 *
	 * @return Fulltext_Index_Internal_Property_Word[]
	 */
	public function collectWords( array $texts, callable $index_word_setup ) : array
	{
		return $this->_collectWords( $texts, $index_word_setup );
	}

	/**
	 * @param string $search_string
	 * @param bool $only_active
	 * @param string $only_type
	 * @param bool $only_ids
	 * @param ?Shops_Shop $shop
	 *
	 * @return Product[]
	 */
	public static function search( string $search_string,
	                               bool $only_active=false,
	                               string $only_type='',
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
			$where['property_is_active'] = true;
		}
		
		if($only_type) {
			if($where) {
				$where[] = 'AND';
			}
			$where['property_type'] = $only_type;
		}

		$ids = [];

		/** @noinspection PhpIfWithCommonPartsInspection */
		if(((int)$search_string)>0) {
			$_ids_by_code = Property::fetchIDs([
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

		$result = Property::fetch(
			where_per_model: [
				'property' => ['id' => $ids],
			],
			item_key_generator: function(Property $item) {
				return $item->getId();
			},
			load_filter: [
				'property.*'
			]
		);


		return $result;
	}
	
	public static function addIndex( Property $property ) : void
	{
		foreach( Shops::getList() as $shop ) {
			$shop_data = $property->getShopData( $shop );
			
			$internal_index = new Fulltext_Index_Internal_Property();
			$internal_index->setShop( $shop );
			$internal_index->setObjectId( $property->getId() );
			$internal_index->setPropertyType( $property->getType() );
			$internal_index->setPropertyIsActive( $property->isActive() && $shop_data->isActive() );
			
			$words = $internal_index->collectWords(
				[
					$property->getInternalName(),
					$property->getInternalNotes(),
				],
				function( Fulltext_Index_Internal_Property_Word $word ) use ($property, $shop_data) {
					$word->setPropertyType( $property->getType() );
					$word->setPropertyIsActive( $shop_data->isActive() );
				}
			);
			
			$internal_index->save();
			foreach( $words as $word ) {
				$word->save();
			}
		}
	}
	
	public static function deleteIndex( Property $property ) : void
	{
		Fulltext_Index_Internal_Property::deleteRecord( $property->getId() );
	}
	
	public static function updateIndex( Property $property ) : void
	{
		Fulltext_Index_Internal_Property::deleteIndex( $property );
		Fulltext_Index_Internal_Property::addIndex( $property );
	}
	
}
