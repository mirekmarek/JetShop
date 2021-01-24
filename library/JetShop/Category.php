<?php
namespace JetShop;


use Jet\DataModel_Definition;

/**
 *
 *
 */
#[DataModel_Definition]
class Category extends Core_Category {

	public static function getRootCategories( string $shop_id=null, bool $only_active=true ) : iterable
	{
		if(!$shop_id) {
			$shop_id = Shops::getCurrentId();
		}


		return static::fetch([
			'categories' => [
				'parent_id' => 0,
				//'and',
				//'is_active' => true
			],
			'categories_shop_data' => [
				'shop_id'=>$shop_id,
				'AND',
				'is_active' => true

			]
		],
			['priority']);
	}
}
