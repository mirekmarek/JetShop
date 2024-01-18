<?php
namespace JetApplicationModule\Shop\Catalog;

use Jet\Navigation_Breadcrumb as Jet_Navigation_Breadcrumb;
use JetApplication\Category_ShopData;


abstract class Navigation_Breadcrumb extends Jet_Navigation_Breadcrumb {
	
	
	public static function setByCategory( Category_ShopData $category ) : void
	{
		$path = $category->getPath();
		
		foreach($path as $id) {
			$category = Category_ShopData::get($id);
			if($category) {
				static::addURL(
					$category->getName(),
					$category->getURL()
				);
			}
		}
	}
	
	
}