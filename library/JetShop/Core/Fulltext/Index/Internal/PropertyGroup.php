<?php
namespace JetShop;

use Jet\DataModel;
use Jet\DataModel_Definition;

use JetApplication\Fulltext_Index;
use JetApplication\Fulltext_Index_Internal_PropertyGroup_Word;
use JetApplication\Shops;
use JetApplication\Shops_Shop;
use JetApplication\Product;
use JetApplication\PropertyGroup;
use JetApplication\Fulltext_Index_Internal_PropertyGroup;


#[DataModel_Definition(
	name: 'index_internal_property_group',
	database_table_name: 'fulltext_internal_property_groups'
)]
abstract class Core_Fulltext_Index_Internal_PropertyGroup extends Fulltext_Index {

	#[DataModel_Definition(
		type: DataModel::TYPE_BOOL,
		is_key: true
	)]
	protected string $group_is_active = '';


	public function getGroupIsActive() : string
	{
		return $this->group_is_active;
	}

	public function setGroupIsActive( string $group_is_active ) : void
	{
		$this->group_is_active = $group_is_active;
	}
	
	public static function getWordClassName() : string
	{
		return Fulltext_Index_Internal_PropertyGroup_Word::class;
	}
	
	/**
	 * @param array $texts
	 * @param callable $index_word_setup
	 *
	 * @return Fulltext_Index_Internal_PropertyGroup_Word[]
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
			$where['group_is_active'] = true;
		}
		

		$ids = [];

		if(((int)$search_string)>0) {
			$_ids_by_code = PropertyGroup::fetchIDs([
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

		$result = PropertyGroup::fetch(
			where_per_model: [
				'property_group' => ['id' => $ids],
			],
			item_key_generator: function(PropertyGroup $item) {
				return $item->getId();
			},
			load_filter: [
				'property_group.*'
			]
		);


		return $result;
	}
	
	public static function addIndex( PropertyGroup $group ) : void
	{
		foreach( Shops::getList() as $shop ) {
			$shop_data = $group->getShopData( $shop );
			
			$internal_index = new Fulltext_Index_Internal_PropertyGroup();
			$internal_index->setShop( $shop );
			$internal_index->setObjectId( $group->getId() );
			$internal_index->setGroupIsActive( $group->isActive() && $shop_data->isActive() );
			
			$words = $internal_index->collectWords(
				[
					$group->getInternalName(),
					$group->getInternalNotes(),
				],
				function( Fulltext_Index_Internal_PropertyGroup_Word $word ) use ($group, $shop_data) {
					$word->setGroupIsActive( $shop_data->isActive() );
				}
			);
			
			$internal_index->save();
			foreach( $words as $word ) {
				$word->save();
			}
		}
	}
	
	public static function deleteIndex( PropertyGroup $group ) : void
	{
		Fulltext_Index_Internal_PropertyGroup::deleteRecord( $group->getId() );
	}
	
	public static function updateIndex( PropertyGroup $group ) : void
	{
		Fulltext_Index_Internal_PropertyGroup::deleteIndex( $group );
		Fulltext_Index_Internal_PropertyGroup::addIndex( $group );
	}
	
}
