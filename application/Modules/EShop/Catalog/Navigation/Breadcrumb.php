<?php
namespace JetApplicationModule\EShop\Catalog;

use Jet\Navigation_Breadcrumb as Jet_Navigation_Breadcrumb;
use JetApplication\Category_EShopData;


abstract class Navigation_Breadcrumb extends Jet_Navigation_Breadcrumb {
	
	
	public static function setByCategory( Category_EShopData $category ) : void
	{
		$path = $category->getPath();
		
		foreach($path as $id) {
			$category = Category_EShopData::get($id);
			if($category) {
				static::addURL(
					$category->getName(),
					$category->getURL()
				);
			}
		}
	}
	
	
}