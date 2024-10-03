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

	public function resolve_product( int $object_id, array $path ) : bool|string
	{
		MVC::getRouter()->setUsedUrlPath( $path[0]);

		static::$product = Product_ShopData::get($object_id);

		if(static::$product) {
			if(static::$product->getURLPathPart()!=$path[0] ) {
				MVC::getRouter()->setIsRedirect( static::$product->getURL(), Http_Headers::CODE_301_MOVED_PERMANENTLY );
				return false;
			}

			$GET = Http_Request::GET();
			
			if(
				($category_id=$GET->getInt('c')) &&
				($category = static::$product->getCategories()[$category_id]??null)
			) {
				static::$category = $category;
			}
			
			
			return 'product_detail';
		} else {
			return 'unknown_product';
		}
	}

	public static function getProduct() : Product_ShopData
	{
		return static::$product;
	}

	public function unknown_product_Action() : void
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



	public function product_detail_Action(): void
	{
		$this->view->setVar( 'product', static::$product );
		$this->_initBreadcrumbNavigation();

		$this->output('product/detail');
	}
	
}

