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

/**
 *
 */
trait Controller_Main_Category
{

	protected static ?Category_ShopData $category = null;

	public function getControllerRouter_category( int $object_id, array $path ) : void
	{

		$category_URL_path = array_shift( $path );

		MVC::getRouter()->setUsedUrlPath($category_URL_path);

		static::$category = Category_ShopData::get($object_id);


		if(static::$category) {

			if(static::$category->getURLPathPart()!=$category_URL_path ) {
				MVC::getRouter()->setIsRedirect( static::$category->getURL(), Http_Headers::CODE_301_MOVED_PERMANENTLY );
				return;
			}


			if(!static::$category->isActive()) {
				$this->router->setDefaultAction('category_not_active');
			} else {
				if(static::$category->getProductsCount()) {
					$this->router->setDefaultAction('category_listing');
				} else {
					$this->router->setDefaultAction('category_signpost');
				}
			}
		} else {
			$this->router->addAction('category_unknown')->setResolver(function() {
				return true;
			});
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
		
		/**
		 * @var Main $moduel
		 */
		$module = $this->getModule();
		
		$listing = $module->getProductListing( $category->getProductIds(), $category->getId() );
		
		$this->view->setVar('category', static::$category);
		$this->view->setVar('listing', $listing);

		$this->output('category/listing');
	}
	
	public function category_signpost_Action(): void
	{
		Navigation_Breadcrumb::setByCategory( static::$category );

		$this->view->setVar('category', static::$category);

		$this->output('category/signpost');

	}

}
