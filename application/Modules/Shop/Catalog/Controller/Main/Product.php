<?php
/**
 *
 * @copyright
 * @license
 * @author
 */
namespace JetApplicationModule\Shop\Catalog;

use Jet\ErrorPages;
use Jet\Http_Headers;
use Jet\Http_Request;
use Jet\MVC;
use JetApplication\Product_ShopData;

/**
 *
 */
trait Controller_Main_Product
{

	protected static ?Product_ShopData $product = null;

	public function getControllerRouter_product( int $object_id, array $path ) : void
	{
		MVC::getRouter()->setUsedUrlPath( $path[0]);

		static::$product = Product_ShopData::get($object_id);

		if(static::$product) {
			if(static::$product->getURLPathPart()!=$path[0] ) {
				MVC::getRouter()->setIsRedirect( static::$product->getURL(), Http_Headers::CODE_301_MOVED_PERMANENTLY );

				return;
			}

			$GET = Http_Request::GET();
			/*
			//TODO:
			if(
				($category_id=$GET->getInt('c')) &&
				static::$product->hasCategory( $category_id )
			) {
				static::$category = Category::get( $category_id );
			}
			*/
			
			$this->router->addAction('detail')->setResolver(function() {
				return true;
			});
			
		} else {
			$this->router->addAction('unknown')->setResolver(function() {
				return true;
			});
		}
	}

	public static function getProduct() : Product_ShopData
	{
		return static::$product;
	}

	public function unknown_Action() : void
	{
		ErrorPages::handleNotFound();
	}

	protected function _initBreadcrumbNavigation() : void
	{
		if(static::$category) {
			Navigation_Breadcrumb::setByCategory( static::$category );
		}

		Navigation_Breadcrumb::addURL(
			static::$product->getFullName()
		);
	}



	public function detail_Action(): void
	{
		$this->view->setVar( 'product', static::$product );
		$this->_initBreadcrumbNavigation();

		$this->output('product/detail');
	}
	
}

