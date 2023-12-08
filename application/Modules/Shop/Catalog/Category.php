<?php
/**
 *
 * @copyright
 * @license
 * @author
 */
namespace JetApplicationModule\Shop\Catalog;

use JetApplication\Category_ShopData as Application_Category_ShopData;
use JetApplication\Shops;

class Category extends Application_Category_ShopData {
	
	protected static array $loaded_items = [];

	public static function get( int $id ) : ?static
	{
		if(array_key_exists($id, static::$loaded_items)) {
			return static::$loaded_items[$id];
		}
		
		$where = [];
		$where[] = static::getActiveQueryWhere( Shops::getCurrent() );
		$where[] = 'AND';
		$where['entity_id'] = $id;
		
		static::$loaded_items[$id] = static::load( $where );
		
		return static::$loaded_items[$id];
	}
}