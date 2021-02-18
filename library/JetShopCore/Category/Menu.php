<?php
namespace JetShop;

use Jet\Data_Tree;
use Jet\Mvc_Site;

abstract class Core_Category_Menu
{
	/**
	 * @var Category_Menu_Item[][]
	 */
	protected static array $menus = [];

	public static function generateAll() : void
	{
		foreach(Shops::getList() as $shop) {
			static::generate( $shop->getCode() );
		}
	}

	public static function generate( string $shop_code ) : void
	{
		$tree = static::getTree( $shop_code );

		/**
		 * @var Category_Menu_Item[] $current_items
		 */
		$current_items = Category_Menu_Item::fetch(
			where_per_model: [
				'category_menu_item' => [
					'shop_code' => $shop_code
				]
			],
			item_key_generator: function( Category_Menu_Item $item ) {
				return $item->getCategoryId();
			});

		$exists = [];

		foreach($tree as $node) {
			$d = $node->getData();
			if(!$d) {
				continue;
			}

			$new_item = static::createMenuItem( $shop_code, $d );
			$category_id = $new_item->getCategoryId();
			$exists[] = $category_id;

			if(!isset($current_items[$category_id])) {
				$new_item->save();
			} else {
				$current_item = $current_items[$category_id];

				if(Category_Menu_Item::actualize( $current_item, $new_item )) {
					$current_item->save();
				}
			}
		}

		foreach($current_items as $current_item) {
			if(!in_array($current_item->getCategoryId(), $exists)) {
				$current_item->delete();
			}
		}
	}

	protected static function createMenuItem( string $shop_code, array $data ) : Category_Menu_Item
	{
		$shop = Shops::get($shop_code);
		$site = Mvc_Site::get($shop->getSiteId());

		$menu_item = new Category_Menu_Item();

		$menu_item->setShopCode( $shop_code );

		$menu_item->setCategoryId( $data['id'] );
		$menu_item->setParentCategoryId( $data['parent_id'] );

		$menu_item->setCategoryType( $data['type'] );
		$menu_item->setPriority( $data['priority'] );
		$menu_item->setLabel( $data['name'] );

		$menu_item->setUrl($site->getHomepage()->getURLPath([$data['URL_path_part']]) );
		$menu_item->setIconUrl( $data['image_pictogram'] );

		$menu_item->setVisibleProductsCount( $data['visible_products_count'] );
		$menu_item->setNestedVisibleProductsCount( $data['nested_visible_products_count'] );


		return $menu_item;
	}

	protected static function getTree( $shop_code ) : Data_Tree
	{

		$sort = 'priority';

		$where = [
			'categories_shop_data.shop_code'=>$shop_code,
			'AND',
			'categories_shop_data.is_active'=>true,
			'AND',
			'categories_shop_data.nested_visible_products_count >' => 0
		];


		$data = Category::fetchData(
			[
				'id' => 'id',
				'parent_id' => 'parent_id',
				'priority' => 'priority',
				'type' => 'type',

				'name' => 'categories_shop_data.name',
				'second_name' => 'categories_shop_data.second_name',
				'URL_path_part' => 'categories_shop_data.URL_path_part',
				'image_main' => 'categories_shop_data.image_main',
				'image_pictogram' => 'categories_shop_data.image_pictogram',
				'visible_products_count' => 'categories_shop_data.visible_products_count',
				'nested_visible_products_count' => 'categories_shop_data.nested_visible_products_count',
			],
			$where,
			$sort
		);


		$tree = new Data_Tree();
		$tree->getRootNode()->setId(0);
		$tree->getRootNode()->setLabel('Root');

		$tree->setAdoptOrphans(true);

		$tree->setData( $data );


		return $tree;
	}

	/**
	 * @param int $parent_id
	 * @param string $type
	 * @param string $shop_code
	 *
	 * @return Category_Menu_Item[]
	 */
	public static function getItems( int $parent_id, string $type='', string $shop_code='' ) : array
	{

		$all = static::getAllItems($shop_code);

		$res = [];

		foreach($all as $item) {

			if(
				$item->getParentCategoryId()!=$parent_id||
				(
					$type &&
					$type!=$item->getCategoryType()
				)
			) {
				continue;
			}

			$res[$item->getCategoryId()] = $item;
		}

		return $res;
	}

	/**
	 * @param string $shop_code
	 *
	 * @return Category_Menu_Item[]
	 */
	public static function getAllItems( string $shop_code='' ) : array
	{
		if(!$shop_code) {
			$shop_code = Shops::getCurrentCode();
		}

		if(array_key_exists($shop_code, static::$menus )) {
			return static::$menus[$shop_code];
		}

		static::$menus[$shop_code] = Category_Menu_Item::fetch(
			where_per_model: [
				'shop_code' => $shop_code
			],
			order_by: 'priority',
			item_key_generator: function( Category_Menu_Item $item ) {
				return $item->getCategoryId();
			}

		);

		foreach( static::$menus[$shop_code] as $item ) {
			$parent_id = $item->getParentCategoryId();
			if(
				$parent_id &&
				isset(static::$menus[$shop_code][$parent_id])
			) {
				static::$menus[$shop_code][$parent_id]->addChildren( $item );
			}
		}


		return static::$menus[$shop_code];
	}

}