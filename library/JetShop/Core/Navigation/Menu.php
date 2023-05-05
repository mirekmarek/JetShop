<?php
namespace JetShop;


use Jet\BaseObject;

use JetApplication\Shops;
use JetApplication\Navigation_Menu;
use JetApplication\Navigation_Menu_Item;
use JetApplication\Cache;

abstract class Core_Navigation_Menu extends BaseObject
{
	protected string $id = '';

	/**
	 * @var Navigation_Menu_Item[]
	 */
	protected array $items = [];

	/**
	 * @var Navigation_Menu[]
	 */
	protected static array $menus = [];

	/**
	 * @param string $menu_id
	 *
	 * @return Navigation_Menu
	 */
	public static function get( string $menu_id ) :  Navigation_Menu
	{
		if(isset(static::$menus[$menu_id])) {
			return static::$menus[$menu_id];
		}

		$cache_key = 'menu:'.Shops::getCurrent()->getKey().':'.$menu_id;
		$menu = Cache::load( $cache_key );
		if($menu) {
			/**
			 * @var Navigation_Menu $menu
			 */
			foreach($menu->items as $item) {
				$item->setMenu( $menu );
			}

			static::$menus[$menu_id] = $menu;

			return $menu;
		}

		$menu = new Navigation_Menu( $menu_id );
		$menu->generate();

		Cache::save( $cache_key, $menu );

		static::$menus[$menu_id] = $menu;

		return $menu;
	}

	public function __construct( string $id )
	{
		$this->id = $id;
	}

	/**
	 * @return Navigation_Menu_Item[]
	 */
	public function getItems() : array
	{
		return $this->items;
	}

	/**
	 * @return Navigation_Menu_Item[]
	 */
	public function getRootItems() : array
	{
		$items = [];

		foreach($this->items as $item) {
			if(!$item->getParentId()) {
				$items[$item->getId()] = $item;
			}
		}

		return $items;
	}


	abstract public function generate() : void;
}