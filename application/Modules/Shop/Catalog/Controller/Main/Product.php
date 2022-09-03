<?php
/**
 *
 * @copyright
 * @license
 * @author
 */
namespace JetShopModule\Shop\Catalog;

use Jet\ErrorPages;
use Jet\Http_Headers;
use Jet\Http_Request;
use Jet\MVC;
use Jet\Tr;
use JetShop\Category;
use JetShop\Navigation_Breadcrumb;
use JetShop\Product;

/**
 *
 */
trait Controller_Main_Product
{

	protected static ?Product $product = null;

	public function getControllerRouter_product( int $object_id, array $path ) : void
	{
		MVC::getRouter()->setUsedUrlPath( $path[0]);

		static::$product = Product::get($object_id);

		if(static::$product) {
			if(static::$product->getURLPathPart()!=$path[0] ) {
				MVC::getRouter()->setIsRedirect( static::$product->getURL(), Http_Headers::CODE_301_MOVED_PERMANENTLY );

				return;
			}

			$GET = Http_Request::GET();
			if(
				($category_id=$GET->getInt('c')) &&
				static::$product->hasCategory( $category_id )
			) {
				static::$category = Category::get( $category_id );
			}

			if(
				!static::$product->isActive() ||
				!static::$product->getShopData()->isActive()
			) {
				$this->router->addAction('product_not_active')->setResolver(function() {
					return true;
				});
			} else {

				$action = match(static::$product->getType()) {
					Product::PRODUCT_TYPE_SET => 'product_set',
					Product::PRODUCT_TYPE_REGULAR => 'product_regular',
					Product::PRODUCT_TYPE_VARIANT => 'product_variant',
					Product::PRODUCT_TYPE_VARIANT_MASTER => 'product_variant_master'
				};

				$this->router->addAction($action)->setResolver(function() {
					return true;
				});
			}
		} else {
			$this->router->addAction('product_unknown')->setResolver(function() {
				return true;
			});
		}
	}

	public static function getProduct() : Product
	{
		return static::$product;
	}

	public function product_unknown_Action() : void
	{
		ErrorPages::handleNotFound();
	}

	protected function _initBreadcrumbNavigation()
	{
		if(static::$category) {
			Navigation_Breadcrumb::setByCategory( static::$category );
		}

		Navigation_Breadcrumb::addURL(
			static::$product->getName()
		);
	}

	public function product_not_active_Action(): void
	{
		Tr::setCurrentDictionary('product.not_active');
		$this->view->setVar( 'product', static::$product );
		$this->_initBreadcrumbNavigation();

		$this->output('product/not_active');
	}

	public function product_set_Action(): void
	{
		Tr::setCurrentDictionary('product.set');
		$this->view->setVar( 'product', static::$product );
		$this->_initBreadcrumbNavigation();

		$this->output('product/set');

	}

	public function product_regular_Action(): void
	{
		Tr::setCurrentDictionary('product.regular');
		$this->view->setVar( 'product', static::$product );
		$this->_initBreadcrumbNavigation();

		$this->output('product/regular');
	}

	public function product_variant_Action(): void
	{
		Tr::setCurrentDictionary('product.variant');
		$this->view->setVar( 'product', static::$product );
		$this->_initBreadcrumbNavigation();

		$this->output('product/variant');
	}

	public function product_variant_master_Action(): void
	{
		Tr::setCurrentDictionary('product.variant_master');
		$this->view->setVar( 'product', static::$product );
		$this->_initBreadcrumbNavigation();

		$this->output('product/variant_master');
	}

}

