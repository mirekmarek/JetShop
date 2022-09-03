<?php
namespace JetShop;

use Jet\Data_Tree;
use Jet\MVC;

abstract class Core_Category_Menu
{
	/**
	 * @var Category_Menu_Item[][]
	 */
	protected static array $menus = [];

	public static function generateAll() : void
	{
		foreach(Shops::getList() as $shop) {
			static::generate( $shop );
		}
	}

	public static function generate( Shops_Shop $shop ) : void
	{
		$tree = static::getTree( $shop );

		$current_items = Category_Menu_Item::fetch(
			where_per_model: [
				'category_menu_item' => $shop->getWhere()
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

			$new_item = static::createMenuItem( $shop, $d );
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

	protected static function createMenuItem( Shops_Shop $shop, array $data ) : Category_Menu_Item
	{
		$site = MVC::getBase($shop->getBaseId());

		$menu_item = new Category_Menu_Item();

		$menu_item->setShop( $shop );

		$menu_item->setCategoryId( $data['id'] );
		$menu_item->setParentCategoryId( $data['parent_id'] );
		
		$menu_item->setPriority( $data['priority'] );
		$menu_item->setLabel( $data['name'] );

		$menu_item->setUrl($site->getHomepage()->getURLPath([$data['URL_path_part']]) );
		$menu_item->setIconUrl( $data['image_pictogram'] );

		$menu_item->setVisibleProductsCount( $data['visible_products_count'] );
		$menu_item->setNestedVisibleProductsCount( $data['nested_visible_products_count'] );


		return $menu_item;
	}

	protected static function getTree( Shops_Shop $shop ) : Data_Tree
	{

		$sort = 'priority';

		$where = [
			$shop->getWhere('categories_shop_data.'),
			'AND',
			'categories_shop_data.is_active'=>true,
			'AND',
			'categories_shop_data.nested_visible_products_count >' => 0
		];


		$data = Category::dataFetchAll(
			select: [
				'id' => 'id',
				'parent_id' => 'parent_id',
				'priority' => 'priority',

				'name' => 'categories_shop_data.name',
				'second_name' => 'categories_shop_data.second_name',
				'URL_path_part' => 'categories_shop_data.URL_path_part',
				'image_main' => 'categories_shop_data.image_main',
				'image_pictogram' => 'categories_shop_data.image_pictogram',
				'visible_products_count' => 'categories_shop_data.visible_products_count',
				'nested_visible_products_count' => 'categories_shop_data.nested_visible_products_count',
			],
			where: $where,
			order_by: $sort
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
	 * @param ?Shops_Shop $shop
	 *
	 * @return Category_Menu_Item[]
	 */
	public static function getItems( int $parent_id,?Shops_Shop $shop = null ) : array
	{

		$all = static::getAllItems($shop);

		$res = [];

		foreach($all as $item) {

			$res[$item->getCategoryId()] = $item;
		}

		return $res;
	}

	/**
	 * @param Shops_Shop|null $shop
	 *
	 * @return Category_Menu_Item[]
	 */
	public static function getAllItems( ?Shops_Shop $shop = null ) : array
	{
		if(!$shop) {
			$shop = Shops::getCurrent();
		}

		$shop_key = $shop->getKey();

		if(array_key_exists($shop_key, static::$menus )) {
			return static::$menus[$shop_key];
		}

		static::$menus[$shop_key] = Category_Menu_Item::fetch(
			where_per_model: $shop->getWhere(),
			order_by: 'priority',
			item_key_generator: function( Category_Menu_Item $item ) {
				return $item->getCategoryId();
			}

		);

		foreach( static::$menus[$shop_key] as $item ) {
			$parent_id = $item->getParentCategoryId();
			if(
				$parent_id &&
				isset(static::$menus[$shop_key][$parent_id])
			) {
				static::$menus[$shop_key][$parent_id]->addChildren( $item );
			}
		}


		return static::$menus[$shop_key];
	}

}