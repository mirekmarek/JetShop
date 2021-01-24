<?php
namespace JetShop;

use Jet\Navigation_Breadcrumb as Jet_Navigation_Breadcrumb;

abstract class Core_Navigation_Breadcrumb extends Jet_Navigation_Breadcrumb {


	public static function setByCategory( Category $category ) : void
	{
		$path = $category->getShopData()->getPath();

		foreach($path as $id) {
			$category = Category::get($id);
			if($category) {
				static::addURL(
					$category->getName(),
					$category->getURL()
				);
			}
		}
	}


}