<?php
/**
 * @copyright Copyright (c) Miroslav Marek <mirek.marek@web-jet.cz>
 * @license EUPL 1.2  https://eupl.eu/1.2/en/
 * @author Miroslav Marek <mirek.marek@web-jet.cz>
 */
namespace JetApplicationModule\EShop\Catalog;


use Jet\MVC_Layout;
use JetApplication\Category_EShopData;

use Jet\ErrorPages;
use Jet\MVC;
use JetApplication\EShop_Managers;
use JetApplication\EShop_Managers_ProductListing;


trait Controller_Main_Category
{

	protected static ?Category_EShopData $category = null;

	public function resolve_category( int $object_id, array $path ) : bool|string
	{

		$category_URL_path = array_shift( $path );

		MVC::getRouter()->setUsedUrlPath($category_URL_path);

		static::$category = Category_EShopData::get($object_id);


		if(static::$category) {
			if(!static::$category->checkURL( $category_URL_path )) {
				return false;
			}
			
			if(!static::$category->isActive()) {
				return 'category_not_active';
			} else {
				if(static::$category->getProductsCount()) {
					return 'category_listing';
				} else {
					return 'category_no_products';
				}
			}
		} else {
			return 'category_unknown';
		}
		
	}

	public static function getCategory() : Category_EShopData
	{
		return static::$category;
	}

	public function category_unknown_Action() : void
	{
		ErrorPages::handleNotFound();
	}

	public function category_not_active_Action() : void
	{
		$this->view->setVar('category', static::$category);
		$this->output('category/not_active');
	}

	public function category_listing_Action() : void
	{
		Navigation_Breadcrumb::setByCategory( static::$category );
		MVC_Layout::getCurrentLayout()->setVar('title', static::$category->getName() );

		$category = static::$category;
		
		$listing = EShop_Managers::ProductListing();
		
		/**
		 * @var EShop_Managers_ProductListing $listing
		 */
		
		$listing->init( $category->getProductIds() );
		$listing->setCategoryId( $category->getId() );
		$listing->setAjaxEventHandler( function( EShop_Managers_ProductListing $listing ) use ($category) {
			EShop_Managers::Analytics()?->viewCategory( $category, $listing->getListing() );
		} );
		
		$listing->handle();
		
		$this->view->setVar('category', static::$category);
		$this->view->setVar('listing', $listing);

		$this->output('category/listing');
	}
	
	public function category_no_products_Action(): void
	{
		Navigation_Breadcrumb::setByCategory( static::$category );
		MVC_Layout::getCurrentLayout()->setVar('title', static::$category->getName() );

		$this->view->setVar('category', static::$category);

		$this->output('category/no-products');

	}

}
