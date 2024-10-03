<?php
/**
 *
 * @copyright
 * @license
 * @author
 */
namespace JetApplicationModule\Shop\Catalog;

use JetApplication\Category_ShopData;

use Jet\ErrorPages;
use Jet\Http_Headers;
use Jet\MVC;
use JetApplication\Shop_Managers;

/**
 *
 */
trait Controller_Main_Category
{

	protected static ?Category_ShopData $category = null;

	public function resolve_category( int $object_id, array $path ) : bool|string
	{

		$category_URL_path = array_shift( $path );

		MVC::getRouter()->setUsedUrlPath($category_URL_path);

		static::$category = Category_ShopData::get($object_id);


		if(static::$category) {
			if(static::$category->getURLPathPart()!=$category_URL_path ) {
				MVC::getRouter()->setIsRedirect( static::$category->getURL(), Http_Headers::CODE_301_MOVED_PERMANENTLY );
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

	public static function getCategory() : Category_ShopData
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

		$category = static::$category;
		
		$listing = Shop_Managers::ProductListing();
		
		$listing->init(
			$category->getProductIds(),
			category_id: $category->getId(),
			category_name: $category->getPathName()
		);
		
		$this->view->setVar('category', static::$category);
		$this->view->setVar('listing', $listing);

		$this->output('category/listing');
	}
	
	public function category_no_products_Action(): void
	{
		Navigation_Breadcrumb::setByCategory( static::$category );

		$this->view->setVar('category', static::$category);

		$this->output('category/no-products');

	}

}
